<?php 
// เริ่ม session หากยังไม่ได้เริ่ม
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// รับค่าจาก URL
$examId       = isset($_GET['exam_id'])    ? $_GET['exam_id']    : '';
$exmneId      = isset($_GET['examinee_id']) ? $_GET['examinee_id'] : (isset($_SESSION['examineeSession']['exmne_id']) ? $_SESSION['examineeSession']['exmne_id'] : '');
$examat_id    = isset($_GET['examat_id'])    ? $_GET['examat_id']    : '';
$attemptRound = isset($_GET['round'])       ? intval($_GET['round']) : 1;

// ตรวจสอบว่ามีค่า exam_id และ examinee_id หรือไม่
if(empty($examId) || empty($exmneId)){
    echo "Missing exam or examinee id.";
    exit;
}

// ดึงข้อมูลรายการข้อสอบทั้งหมดจาก exam_tbl (เพื่อให้เลือกข้อสอบ)
$selAllExam = $conn->query("SELECT * FROM exam_tbl ORDER BY ex_id ASC");

// ดึงข้อมูลรายการผู้สอบทั้งหมดจาก examinee_tbl (เพื่อให้เลือก examinee)
$selAllExaminee = $conn->query("SELECT * FROM examinee_tbl ORDER BY exmne_fullname ASC");

// ดึงข้อมูลของข้อสอบที่เลือกจาก exam_tbl
$selExam = $conn->query("SELECT * FROM exam_tbl WHERE ex_id='$examId'")->fetch(PDO::FETCH_ASSOC);
if(!$selExam){
    echo "Exam not found.";
    exit;
}

// ดึงข้อมูลของ examinee ที่เลือก (ใช้แสดงชื่อ)
$selExaminee = $conn->query("SELECT * FROM examinee_tbl WHERE exmne_id='$exmneId'")->fetch(PDO::FETCH_ASSOC);

// ดึงข้อมูลรอบทั้งหมดของผู้สอบจาก exam_answers สำหรับข้อสอบที่เลือก
$selRounds = $conn->query("SELECT DISTINCT attempt_round FROM exam_answers 
                          WHERE axmne_id='$exmneId' AND exam_id='$examId' 
                          ORDER BY attempt_round ASC");

// ดึงข้อมูลคำถามและคำตอบของรอบที่เลือก (สำหรับตารางคำตอบ)
$selQuest = $conn->query("SELECT * FROM exam_question_tbl eqt 
                          INNER JOIN exam_answers ea 
                          ON eqt.eqt_id = ea.quest_id 
                          WHERE eqt.exam_id='$examId' 
                          AND ea.axmne_id='$exmneId' 
                          AND ea.attempt_round='$attemptRound'");

// ดึงคะแนนสำหรับรอบที่เลือก (ใช้สำหรับ Bar Chart)
$selScoreQuery = "SELECT * FROM exam_question_tbl eqt 
                   INNER JOIN exam_answers ea 
                   ON eqt.eqt_id = ea.quest_id 
                   AND eqt.exam_answer = ea.exans_answer  
                   WHERE ea.axmne_id='$exmneId' 
                   AND ea.exam_id='$examId' 
                   AND ea.attempt_round='$attemptRound'";
$selScore = $conn->query($selScoreQuery);
$scoreVal = $selScore->rowCount();
$over     = $selExam['ex_questlimit_display']; // จำนวนคำถามทั้งหมด
$percentage = ($over > 0) ? ($scoreVal / $over * 100) : 0;

// สร้าง arrays สำหรับกราฟ trend (จากทุกรอบของข้อสอบที่เลือก)
$roundsArray = [];
$scoresArray = [];
$timeSpentArray = [];

$roundQuery = $conn->query("SELECT DISTINCT attempt_round FROM exam_answers WHERE axmne_id='$exmneId' AND exam_id='$examId' ORDER BY attempt_round ASC");
while($r = $roundQuery->fetch(PDO::FETCH_ASSOC)){
    $roundsArray[] = $r['attempt_round'];
    
    // คำนวณคะแนนสำหรับแต่ละรอบ
    $roundScoreQuery = "SELECT * FROM exam_question_tbl eqt 
                        INNER JOIN exam_answers ea 
                        ON eqt.eqt_id = ea.quest_id 
                        AND eqt.exam_answer = ea.exans_answer  
                        WHERE ea.axmne_id='$exmneId' 
                        AND ea.exam_id='$examId' 
                        AND ea.attempt_round='".$r['attempt_round']."'";
    $roundScore = $conn->query($roundScoreQuery)->rowCount();
    $scoresArray[] = $roundScore;
    
    // ดึงเวลาใช้สำหรับแต่ละรอบ (หน่วยวินาที)
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

<!-- ใช้โครงสร้างเดียวกับ manage-examinee.php -->
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

<div class="app-main__outer">
  <div class="app-main__inner">
    <!-- Title -->
    <div class="app-page-title">
      <div class="page-title-wrapper">
        <div class="page-title-heading">
          <div>EXAM RESULT</div>
        </div>
      </div>
    </div>

    <!-- ฟอร์มเลือก Examinee, Exam และ Round -->
    <div class="row mb-3">
      <div class="col-sm-4 text-sm-start">
        <!-- ฟอร์มเลือก Examinee -->
        <form method="get" action="home.php" class="d-inline-block me-2">
          <input type="hidden" name="page" value="result">
          <input type="hidden" name="exam_id" value="<?php echo $examId; ?>">
          <input type="hidden" name="examat_id" value="<?php echo $examat_id; ?>">
          <input type="hidden" name="round" value="<?php echo $attemptRound; ?>">
          <label for="examinee_id" class="fw-bold text-primary me-2">Select Examinee:</label>
          <select name="examinee_id" id="examinee_id" class="form-select form-select-sm d-inline-block" style="width:auto;" onchange="this.form.submit()">
            <?php 
              // Query examinee list is already stored in $selAllExaminee
              while($examineeRow = $selAllExaminee->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo $examineeRow['exmne_id']; ?>" <?php echo ($exmneId == $examineeRow['exmne_id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($examineeRow['exmne_fullname']); ?>
                </option>
            <?php } ?>
          </select>
        </form>
      </div>
      <div class="col-sm-4 text-sm-center">
        <!-- ฟอร์มเลือก Exam -->
        <form method="get" action="home.php" class="d-inline-block me-2">
          <input type="hidden" name="page" value="result">
          <input type="hidden" name="examinee_id" value="<?php echo $exmneId; ?>">
          <input type="hidden" name="examat_id" value="<?php echo $examat_id; ?>">
          <input type="hidden" name="round" value="1"> 
          <label for="exam_id" class="fw-bold text-primary me-2">Select Exam:</label>
          <select name="exam_id" id="exam_id" class="form-select form-select-sm d-inline-block" style="width:auto;" onchange="this.form.submit()">
            <?php while ($examRow = $selAllExam->fetch(PDO::FETCH_ASSOC)) { ?>
              <option value="<?php echo $examRow['ex_id']; ?>" <?php echo ($examId == $examRow['ex_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($examRow['ex_title']); ?>
              </option>
            <?php } ?>
          </select>
        </form>
      </div>
      <div class="col-sm-4 text-sm-end">
        <!-- ฟอร์มเลือก Round -->
        <form method="get" action="home.php" class="d-inline-block">
          <input type="hidden" name="page" value="result">
          <input type="hidden" name="exam_id" value="<?php echo $examId; ?>">
          <input type="hidden" name="examinee_id" value="<?php echo $exmneId; ?>">
          <input type="hidden" name="examat_id" value="<?php echo $examat_id; ?>">
          <label for="round" class="me-2 fw-bold text-primary">Attempt Round:</label>
          <select name="round" id="round" onchange="this.form.submit()" class="form-select form-select-sm d-inline-block" style="width:auto;">
            <?php while ($roundRow = $selRounds->fetch(PDO::FETCH_ASSOC)) { ?>
              <option value="<?php echo $roundRow['attempt_round']; ?>" <?php echo $attemptRound == $roundRow['attempt_round'] ? 'selected' : ''; ?>>
                Round <?php echo $roundRow['attempt_round']; ?>
              </option>
            <?php } ?>
          </select>
        </form>
      </div>
    </div>

    <!-- แสดงชื่อ examinee ที่เลือก -->
    <div class="row mb-3">
      <div class="col-sm-12">
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
        // Time Spent สำหรับรอบที่เลือก
        $stmtTimeSpent = $conn->prepare("SELECT time_spent FROM exam_attempt 
                                        WHERE exmne_id = :exmne_id 
                                        AND exam_id = :exam_id 
                                        AND attempt_round = :attempt_round 
                                        LIMIT 1");
        $stmtTimeSpent->execute([
            ':exmne_id' => $exmneId,
            ':exam_id'  => $examId,
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
        // Time Limit สำหรับรอบที่เลือก
        $stmtTimeLimit = $conn->prepare("SELECT time_limit FROM exam_attempt 
                                        WHERE exmne_id = :exmne_id 
                                        AND exam_id = :exam_id 
                                        AND attempt_round = :attempt_round 
                                        LIMIT 1");
        $stmtTimeLimit->execute([
            ':exmne_id' => $exmneId,
            ':exam_id'  => $examId,
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
  </div><!-- card-body -->
      </div><!-- main-card -->
    </div><!-- col-md-12 -->
  </div><!-- app-main__inner -->
</div><!-- app-main__outer -->

<!-- นำเข้า Chart.js จาก CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// AREA CHART (Trend ของทุกรอบ)
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

// SCATTER CHART (Time vs Score สำหรับทุกรอบ)
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

// LINE CHART (Trend ของทุกรอบ)
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

// BAR CHART (เฉพาะรอบที่เลือก: Score vs Total Questions)
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