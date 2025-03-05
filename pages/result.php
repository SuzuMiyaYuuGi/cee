<?php 
$exmneId = $_SESSION['examineeSession']['exmne_id'];

// รับค่า exam id และ attempt round จาก URL
$examId = isset($_GET['id']) ? $_GET['id'] : '';
$attemptRound = isset($_GET['round']) ? intval($_GET['round']) : 1;
if(empty($examId)){
    echo "Missing exam id.";
    exit;
}

// ดึงข้อมูลของข้อสอบจาก exam_tbl
$selExam = $conn->query("SELECT * FROM exam_tbl WHERE ex_id='$examId'")->fetch(PDO::FETCH_ASSOC);
if(!$selExam){
    echo "Exam not found.";
    exit;
}

// ดึงข้อมูลของผู้สอบ (เฉพาะของตัวเอง)
$selExaminee = $conn->query("SELECT * FROM examinee_tbl WHERE exmne_id='$exmneId'")->fetch(PDO::FETCH_ASSOC);

// ดึงข้อมูลรอบทั้งหมดของการสอบของผู้สอบ (เฉพาะของตัวเอง)
$selRounds = $conn->query("SELECT DISTINCT attempt_round FROM exam_answers 
                          WHERE axmne_id='$exmneId' AND exam_id='$examId' 
                          ORDER BY attempt_round ASC");

// ดึงข้อมูลคำถามและคำตอบของรอบที่เลือก
$selQuest = $conn->query("SELECT * FROM exam_question_tbl eqt 
                          INNER JOIN exam_answers ea 
                          ON eqt.eqt_id = ea.quest_id 
                          WHERE eqt.exam_id='$examId' 
                          AND ea.axmne_id='$exmneId' 
                          AND ea.attempt_round='$attemptRound'");

// คำนวณคะแนนสำหรับรอบที่เลือก
$selScore = $conn->query("SELECT * FROM exam_question_tbl eqt 
                          INNER JOIN exam_answers ea 
                          ON eqt.eqt_id = ea.quest_id 
                          AND eqt.exam_answer = ea.exans_answer  
                          WHERE ea.axmne_id='$exmneId' 
                          AND ea.exam_id='$examId' 
                          AND ea.attempt_round='$attemptRound'");
$scoreVal = $selScore->rowCount();
$over = $selExam['ex_questlimit_display'];
$percentage = ($over > 0) ? ($scoreVal / $over * 100) : 0;

// สร้าง arrays สำหรับกราฟ Trend (จากทุกรอบ)
$roundsArray = [];
$scoresArray = [];
$timeSpentArray = [];
$roundQuery = $conn->query("SELECT DISTINCT attempt_round FROM exam_answers 
                             WHERE axmne_id='$exmneId' AND exam_id='$examId' 
                             ORDER BY attempt_round ASC");
while($r = $roundQuery->fetch(PDO::FETCH_ASSOC)){
    $roundsArray[] = $r['attempt_round'];
    
    // คำนวณคะแนนของแต่ละรอบ
    $roundScoreQuery = "SELECT * FROM exam_question_tbl eqt 
                        INNER JOIN exam_answers ea 
                        ON eqt.eqt_id = ea.quest_id 
                        AND eqt.exam_answer = ea.exans_answer  
                        WHERE ea.axmne_id='$exmneId' 
                        AND ea.exam_id='$examId' 
                        AND ea.attempt_round='".$r['attempt_round']."'";
    $roundScore = $conn->query($roundScoreQuery)->rowCount();
    $scoresArray[] = $roundScore;
    
    // ดึงเวลาใช้สอบของแต่ละรอบ (หน่วยวินาที)
    $stmtTimeSpent = $conn->prepare("SELECT time_spent FROM exam_attempt 
                                     WHERE exmne_id = :exmne_id 
                                     AND exam_id = :exam_id 
                                     AND attempt_round = :attempt_round 
                                     LIMIT 1");
    $stmtTimeSpent->execute([
        ':exmne_id' => $exmneId,
        ':exam_id' => $examId,
        ':attempt_round' => $r['attempt_round']
    ]);
    $timeRow = $stmtTimeSpent->fetch(PDO::FETCH_ASSOC);
    $timeSpent = isset($timeRow['time_spent']) ? $timeRow['time_spent'] : 0;
    $timeSpentArray[] = $timeSpent;
}

// สำหรับ Scatter Chart: สร้าง array ของ objects (x = time spent in minutes, y = score)
$scatterData = [];
for($i = 0; $i < count($roundsArray); $i++){
    $scatterData[] = [
        'x' => round($timeSpentArray[$i] / 60, 2),
        'y' => $scoresArray[$i]
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Exam Result</title>
    <!-- Bootstrap 5 & Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/mycss.css">
    <style>
      .chart-canvas {
        width: 100%;
        height: 300px;
      }
      .answers-table {
        max-height: 450px; 
        overflow-y: auto;
      }
      .answers-table table {
        background-color: #fff;
      }
      .stat-card {
        border-radius: 0.5rem;
        text-align: center;
        padding: 1.25rem;
        color: #fff;
      }
      .stat-card .stat-value {
        font-size: 1.4rem;
        font-weight: 600;
      }
    </style>
</head>
<body>
<div class="app-main__outer">
  <div class="app-main__inner">
    <div class="card mb-4 shadow-sm">
      <div class="card-body text-start">
        <h1 class="card-title mb-3"><?php echo htmlspecialchars($selExam['ex_title']); ?></h1>
        <p class="card-text text-muted"><?php echo htmlspecialchars($selExam['ex_description']); ?></p>
      </div>
    </div>
    
    <!-- ฟอร์มเลือก Attempt Round (ถ้ามีหลายรอบ) -->
    <div class="row mb-3">
      <div class="col-sm-12 text-start">
        <form method="get" action="home.php" class="d-inline-block">
          <input type="hidden" name="page" value="result">
          <input type="hidden" name="id" value="<?php echo $examId; ?>">
          <input type="hidden" name="examinee_id" value="<?php echo $exmneId; ?>">
          <label for="round" class="me-2 fw-bold text-primary">Attempt Round:</label>
          <select name="round" id="round" onchange="this.form.submit()" class="form-select form-select-sm d-inline-block" style="width:auto;">
            <?php while ($roundRow = $selRounds->fetch(PDO::FETCH_ASSOC)) { ?>
              <option value="<?php echo $roundRow['attempt_round']; ?>" <?php echo ($attemptRound == $roundRow['attempt_round']) ? 'selected' : ''; ?>>
                Round <?php echo $roundRow['attempt_round']; ?>
              </option>
            <?php } ?>
          </select>
        </form>
      </div>
    </div>
    
    <!-- แสดงชื่อ Examinee (ของตัวเอง) -->
    <div class="row mb-3">
      <div class="col-sm-12 text-start">
        <h5 class="text-primary">Viewing Score for: <?php echo htmlspecialchars($selExaminee['exmne_fullname']); ?></h5>
      </div>
    </div>
    
    <!-- แสดง Area Chart (Trend จากทุกรอบ) -->
    <div class="card mb-4">
      <div class="card-header">
        <span class="fw-bold">Exam Score Trend (Area Chart)</span>
      </div>
      <div class="card-body">
        <canvas id="areaChart" class="chart-canvas"></canvas>
      </div>
    </div>
    
    <!-- แสดงสถิติตัวเลข -->
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="stat-card bg-dark">
          <h5>Score</h5>
          <div class="stat-value">
            <?php echo $scoreVal; ?> / <?php echo $over; ?>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card bg-success">
          <h5>Percentage</h5>
          <div class="stat-value">
            <?php echo number_format($percentage, 2); ?>%
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <?php 
        $stmtTimeSpent = $conn->prepare("SELECT time_spent FROM exam_attempt 
                                        WHERE exmne_id = :exmne_id 
                                        AND exam_id = :exam_id 
                                        AND attempt_round = :attempt_round 
                                        LIMIT 1");
        $stmtTimeSpent->execute([
            ':exmne_id' => $exmneId,
            ':exam_id' => $examId,
            ':attempt_round' => $attemptRound
        ]);
        $timeSpentRow = $stmtTimeSpent->fetch(PDO::FETCH_ASSOC);
        $timeSpent = isset($timeSpentRow['time_spent']) ? $timeSpentRow['time_spent'] : 0;
        $minutes = floor($timeSpent / 60);
        $seconds = $timeSpent % 60;
        ?>
        <div class="stat-card bg-warning text-dark">
          <h5>Time Spent</h5>
          <div class="stat-value">
            <?php echo sprintf("%02d:%02d", $minutes, $seconds); ?>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <?php 
        $stmtTimeLimit = $conn->prepare("SELECT time_limit FROM exam_attempt 
                                        WHERE exmne_id = :exmne_id 
                                        AND exam_id = :exam_id 
                                        AND attempt_round = :attempt_round 
                                        LIMIT 1");
        $stmtTimeLimit->execute([
            ':exmne_id' => $exmneId,
            ':exam_id' => $examId,
            ':attempt_round' => $attemptRound
        ]);
        $timeLimitRow = $stmtTimeLimit->fetch(PDO::FETCH_ASSOC);
        $timeLimit = isset($timeLimitRow['time_limit']) ? $timeLimitRow['time_limit'] : 0;
        ?>
        <div class="stat-card bg-primary">
          <h5>Time Limit</h5>
          <div class="stat-value">
            <?php echo sprintf("%02d:00", $timeLimit); ?>
          </div>
        </div>
      </div>
    </div>
    
    <!-- แถวสำหรับ Scatter, Line และ Bar Chart -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <span class="fw-bold">Scatter Chart</span>
          </div>
          <div class="card-body">
            <canvas id="scatterChart" class="chart-canvas"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <span class="fw-bold">Line Chart</span>
          </div>
          <div class="card-body">
            <canvas id="lineChart" class="chart-canvas"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <span class="fw-bold">Bar Chart</span>
          </div>
          <div class="card-body">
            <canvas id="barChart" class="chart-canvas"></canvas>
          </div>
        </div>
      </div>
    </div>
    
    <!-- ตารางคำตอบ (Your Answers) -->
    <?php if ($selQuest->rowCount() > 0) { ?>
    <div class="card">
      <div class="card-header">
        <span class="fw-bold">Your Answers (Round <?php echo $attemptRound; ?>)</span>
      </div>
      <div class="card-body answers-table">
        <table class="table table-borderless align-middle">
          <?php 
          $i = 1;
          while ($selQuestRow = $selQuest->fetch(PDO::FETCH_ASSOC)) { ?>
              <tr>
                <td>
                  <strong><?php echo $i++; ?>.)</strong> 
                  <?php echo htmlspecialchars($selQuestRow['exam_question']); ?>
                  <br>
                  <small>
                    <span class="text-muted">Answer:</span>
                    <?php if ($selQuestRow['exam_answer'] != $selQuestRow['exans_answer']) { ?>
                        <span class="text-danger"><?php echo htmlspecialchars($selQuestRow['exans_answer']); ?></span>
                    <?php } else { ?>
                        <span class="text-success"><?php echo htmlspecialchars($selQuestRow['exans_answer']); ?></span>
                    <?php } ?>
                  </small>
                </td>
              </tr>
          <?php } ?>
        </table>
      </div>
    </div>
    <?php } else { ?>
    <div class="alert alert-warning mt-4" role="alert">
      <h4 class="alert-heading">No Data Found!</h4>
      <p>There are no records for Round <?php echo $attemptRound; ?>. Please select another round.</p>
    </div>
    <?php } ?>
    
  </div><!-- app-main__inner -->
</div><!-- app-main__outer -->

<!-- นำเข้า Chart.js จาก CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// AREA CHART (Trend)
const areaCtx = document.getElementById('areaChart').getContext('2d');
const gradientArea = areaCtx.createLinearGradient(0, 0, 0, 300);
gradientArea.addColorStop(0, 'rgba(0, 123, 255, 0.3)');
gradientArea.addColorStop(1, 'rgba(0, 123, 255, 0)');

const areaData = {
  labels: <?php echo json_encode($roundsArray); ?>,
  datasets: [{
    label: 'Exam Score Trend',
    data: <?php echo json_encode($scoresArray); ?>,
    borderColor: '#007bff',
    backgroundColor: gradientArea,
    fill: true,
    tension: 0.1
  }]
};
new Chart(areaCtx, {
  type: 'line',
  data: areaData,
  options: {
    responsive: true,
    scales: { y: { beginAtZero: true } }
  }
});

// SCATTER CHART (Time vs Score)
const scatterCtx = document.getElementById('scatterChart').getContext('2d');
const scatterData = {
  datasets: [{
    label: 'Time vs Score',
    data: <?php echo json_encode($scatterData); ?>,
    backgroundColor: '#007bff'
  }]
};
new Chart(scatterCtx, {
  type: 'scatter',
  data: scatterData,
  options: {
    responsive: true,
    scales: {
      x: { title: { display: true, text: 'Time Spent (minutes)' } },
      y: { title: { display: true, text: 'Score' }, beginAtZero: true }
    }
  }
});

// LINE CHART (Score Trend)
const lineCtx = document.getElementById('lineChart').getContext('2d');
const lineData = {
  labels: <?php echo json_encode($roundsArray); ?>,
  datasets: [{
    label: 'Score Trend',
    data: <?php echo json_encode($scoresArray); ?>,
    borderColor: '#28a745',
    fill: false,
    tension: 0.1
  }]
};
new Chart(lineCtx, {
  type: 'line',
  data: lineData,
  options: {
    responsive: true,
    scales: { y: { beginAtZero: true } }
  }
});

// BAR CHART (Score vs Total Questions)
const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
  type: 'bar',
  data: {
    labels: ['Score', 'Total Questions'],
    datasets: [{
      label: 'Exam Score',
      data: [<?php echo $scoreVal; ?>, <?php echo $over; ?>],
      backgroundColor: ['#ffc107', '#6c757d']
    }]
  },
  options: {
    responsive: true,
    scales: { y: { beginAtZero: true } }
  }
});
</script>
</body>
</html>
