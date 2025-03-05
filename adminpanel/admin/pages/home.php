<?php
// เชื่อมต่อฐานข้อมูลและเริ่ม session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- สถิติหลัก ---
$selExamCount = $conn->query("SELECT COUNT(*) AS totExam FROM exam_tbl");
$totalExam = $selExamCount->fetch(PDO::FETCH_ASSOC)['totExam'];

$selExamineeCount = $conn->query("SELECT COUNT(*) AS totalExaminee FROM examinee_tbl");
$totalExaminee = $selExamineeCount->fetch(PDO::FETCH_ASSOC)['totalExaminee'];

$selAttemptCount = $conn->query("SELECT COUNT(*) AS totalatt FROM exam_attempt");
$totalAttempt = $selAttemptCount->fetch(PDO::FETCH_ASSOC)['totalatt'];

$selFeedbackCount = $conn->query("SELECT COUNT(*) AS totalfeed FROM feedbacks_tbl");
$totalFeedback = $selFeedbackCount->fetch(PDO::FETCH_ASSOC)['totalfeed'];

// ตาราง Average Score ของแต่ละ Exam
$avgScoreData = $conn->query("SELECT 
      ex.ex_id,
      ex.ex_title,
      ex.ex_questlimit_display,
      AVG(sub.correct_count) AS avg_correct
    FROM exam_tbl ex
    LEFT JOIN (
      SELECT ea.exam_id, ea.exmne_id, ea.attempt_round,
        (SELECT COUNT(*) 
         FROM exam_question_tbl eqt
         INNER JOIN exam_answers ea2 
           ON eqt.eqt_id = ea2.quest_id 
          AND eqt.exam_answer = ea2.exans_answer
         WHERE ea2.exam_id = ea.exam_id 
           AND ea2.axmne_id = ea.exmne_id 
           AND ea2.attempt_round = ea.attempt_round
           AND ea2.exans_status='new'
        ) AS correct_count
      FROM exam_attempt ea
    ) sub ON ex.ex_id = sub.exam_id
    GROUP BY ex.ex_id
    ORDER BY avg_correct DESC
    LIMIT 5
");

$avgExamLabels = [];
$avgExamScores = [];
while($row = $avgScoreData->fetch(PDO::FETCH_ASSOC)){
    $avgExamLabels[] = $row['ex_title'];
    $avg_percentage = ($row['ex_questlimit_display'] > 0) ? ($row['avg_correct'] / $row['ex_questlimit_display']) * 100 : 0;
    $avgExamScores[] = round($avg_percentage, 2);
}

// --- Bar Chart: Top 5 Exams by Attempts ---
$examChartData = $conn->query("
    SELECT ex.ex_title AS exam_title, COUNT(at.examat_id) AS attempt_count
    FROM exam_tbl ex
    LEFT JOIN exam_attempt at ON ex.ex_id = at.exam_id
    GROUP BY ex.ex_id
    ORDER BY attempt_count DESC
    LIMIT 5
");
$examLabels = [];
$examAttempts = [];
while($row = $examChartData->fetch(PDO::FETCH_ASSOC)) {
    $examLabels[] = $row['exam_title'];
    $examAttempts[] = $row['attempt_count'];
}

// --- Line Chart: สำหรับ Attempt ล่าสุด 6 ครั้ง (แสดงเวลาที่ใช้) ---
$attemptChartData = $conn->query("
    SELECT examat_id, time_spent 
    FROM exam_attempt
    ORDER BY examat_id DESC
    LIMIT 6
");
$attemptLabels = [];
$timeSpentData = [];
while($mrow = $attemptChartData->fetch(PDO::FETCH_ASSOC)) {
    $attemptLabels[] = 'Attempt #'.$mrow['examat_id'];
    $timeSpentData[] = $mrow['time_spent'];
}

// --- Scatter Chart: เปรียบเทียบเวลาที่ใช้กับเปอร์เซ็นต์คะแนน (จำลองข้อมูลเปอร์เซ็นต์) ---
$scatterData = [];
for($i = 0; $i < count($attemptLabels); $i++){
    $scatterData[] = [
        'x' => round($timeSpentData[$i] / 60, 2), // แปลงวินาทีเป็นนาที
        'y' => rand(70, 95) // จำลองเปอร์เซ็นต์คะแนน
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Analytics Dashboard</title>
  <!-- นำเข้า Bootstrap 5 CSS และ Font Awesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Poppins', sans-serif;
    }
    .app-main__outer {
      padding: 20px;
    }
    .app-page-title {
      margin-bottom: 1.5rem;
    }
    .app-page-title .page-title-heading h3 {
      font-weight: 700;
      font-size: 2rem;
    }
    .app-page-title .page-title-heading p {
      font-size: 1rem;
      color: #6c757d;
    }
    .card {
      border: none;
      border-radius: 0.75rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .chart-canvas {
      width: 100%;
      height: 300px;
    }
    .stat-card {
      border-radius: 0.75rem;
      text-align: center;
      padding: 1.5rem;
      color: #fff;
    }
    .stat-card h5 {
      font-size: 1rem;
      margin-bottom: 0.5rem;
    }
    .stat-card .stat-value {
      font-size: 2rem;
      font-weight: 700;
    }
    .card-header {
      background: #4a90e2;
      color: #fff;
      font-size: 1.25rem;
      font-weight: 600;
    }
    /* Custom table styling */
    .custom-table {
      border-collapse: separate;
      border-spacing: 0;
    }
    .custom-table thead th {
      background-color: #007bff;
      color: #fff;
      padding: 12px 15px;
      text-align: center;
      border-top-left-radius: 0.5rem;
      border-top-right-radius: 0.5rem;
    }
    .custom-table tbody td {
      padding: 12px 15px;
      text-align: center;
      vertical-align: middle;
      border-top: 1px solid #dee2e6;
    }
    .custom-table tbody tr:hover {
      background-color: #f1f1f1;
    }
    @media (max-width: 768px) {
      .app-page-title .page-title-heading h3 {
        font-size: 1.75rem;
      }
    }
  </style>
</head>
<body>
<div class="app-main__outer">
  <div class="app-main__inner">
    <!-- Header -->
    <div class="app-page-title">
      <div class="page-title-wrapper d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title-heading">
          <h3 class="fw-bold">Analytics Dashboard</h3>
        </div>
        <p class="text-muted">Overview of system statistics and activity.</p>
      </div>
    </div>

    <!-- Statistic Cards -->
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card text-white stat-card bg-primary">
          <h5>Total Exam</h5>
          <div class="stat-value"><?php echo $totalExam; ?></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white stat-card bg-success">
          <h5>Total Examinee</h5>
          <div class="stat-value"><?php echo $totalExaminee; ?></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white stat-card bg-info">
          <h5>Total Result</h5>
          <div class="stat-value"><?php echo $totalAttempt; ?></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white stat-card bg-warning">
          <h5>Total Feedback</h5>
          <div class="stat-value"><?php echo $totalFeedback; ?></div>
        </div>
      </div>
    </div>

    <!-- Average Score Table with Custom Styling -->
    <div class="card mb-4">
      <div class="card-header">
        <strong>Average Score of Each Exam</strong>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table custom-table">
            <thead>
              <tr>
                <th>Exam Title</th>
                <th>Average Score (%)</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if(!empty($avgExamLabels)){
                for($i = 0; $i < count($avgExamLabels); $i++){
                  echo "<tr>";
                  echo "<td>" . $avgExamLabels[$i] . "</td>";
                  echo "<td>" . $avgExamScores[$i] . "%</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='2'>No Data Available</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Charts Section: Row 1 -->
    <div class="row g-3 mb-4">
      <!-- Bar Chart: Top 5 Exams by Attempts -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <strong>Top 5 Exams by Attempts</strong>
          </div>
          <div class="card-body">
            <canvas id="examChart" class="chart-canvas"></canvas>
          </div>
        </div>
      </div>
      <!-- Line Chart: Average Score (Line Chart) -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <strong>Average Score (Line Chart)</strong>
          </div>
          <div class="card-body">
            <canvas id="avgScoreChart" class="chart-canvas"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section: Row 2 -->
    <div class="row g-3 mb-4">
      <!-- Scatter Chart: Time Spent vs Percentage -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <strong>Time Spent vs Percentage</strong>
          </div>
          <div class="card-body">
            <canvas id="scatterChart" class="chart-canvas"></canvas>
          </div>
        </div>
      </div>
      <!-- Doughnut Chart: Attempt Distribution among Top 5 Exams -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <strong>Attempt Distribution</strong>
          </div>
          <div class="card-body text-center">
            <canvas id="doughnutChart" style="width: 100%; height: 300px;"></canvas>
          </div>
        </div>
      </div>
    </div>

  </div><!-- app-main__inner -->
</div><!-- app-main__outer -->

<!-- นำเข้า Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Bar Chart: Top 5 Exams by Attempts
const ctxExam = document.getElementById('examChart').getContext('2d');
new Chart(ctxExam, {
  type: 'bar',
  data: {
    labels: <?php echo json_encode($examLabels); ?>,
    datasets: [{
      label: 'Attempts',
      data: <?php echo json_encode($examAttempts); ?>,
      backgroundColor: '#28a745'
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: { beginAtZero: true }
    }
  }
});

// Line Chart: Average Score of Each Exam
const ctxAvgScore = document.getElementById('avgScoreChart').getContext('2d');
new Chart(ctxAvgScore, {
  type: 'line',
  data: {
    labels: <?php echo json_encode($avgExamLabels); ?>,
    datasets: [{
      label: 'Average Score (%)',
      data: <?php echo json_encode($avgExamScores); ?>,
      borderColor: '#007bff',
      fill: false,
      tension: 0.1
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: { beginAtZero: true, max: 100 }
    }
  }
});

// Scatter Chart: Time Spent vs Percentage
const ctxScatter = document.getElementById('scatterChart').getContext('2d');
new Chart(ctxScatter, {
  type: 'scatter',
  data: {
    datasets: [{
      label: 'Time vs Percentage',
      data: <?php echo json_encode($scatterData); ?>,
      backgroundColor: '#007bff'
    }]
  },
  options: {
    responsive: true,
    scales: {
      x: { title: { display: true, text: 'Time Spent (minutes)' } },
      y: { title: { display: true, text: 'Score (%)' }, beginAtZero: true, max: 100 }
    }
  }
});

// Doughnut Chart: Attempt Distribution among Top 5 Exams
const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
new Chart(ctxDoughnut, {
  type: 'doughnut',
  data: {
    labels: <?php echo json_encode($examLabels); ?>,
    datasets: [{
      data: <?php echo json_encode($examAttempts); ?>,
      backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d']
    }]
  },
  options: {
    responsive: true
  }
});
</script>
</body>
</html>
