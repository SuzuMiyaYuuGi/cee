<?php 
include(dirname(__DIR__) . '/conn.php');

$examId = $_GET['id'] ?? null;
$selExam = $conn->query("SELECT * FROM exam_tbl WHERE ex_id='$examId'")->fetch(PDO::FETCH_ASSOC);

$redirectUrl = ""; // ตัวแปรสำหรับเก็บ URL ที่ต้องการ Redirect

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $timeLimit = $_POST['examTimeLimit']; // เวลาเป็นนาที
    $examineeId = $_SESSION['examineeSession']['exmne_id'] ?? null; // ดึง ID ผู้เข้าสอบจาก session

    if ($examineeId) {
        // ตรวจสอบ attempt_round ล่าสุด
        $stmtAttempt = $conn->prepare("SELECT MAX(attempt_round) AS max_round FROM exam_attempt 
                                       WHERE exmne_id = :exmne_id AND exam_id = :exam_id");
        $stmtAttempt->execute([
            ':exmne_id' => $examineeId,
            ':exam_id' => $examId
        ]);
        $attemptRow = $stmtAttempt->fetch(PDO::FETCH_ASSOC);
        $nextAttemptRound = isset($attemptRow['max_round']) ? $attemptRow['max_round'] + 1 : 1;

        // เพิ่มข้อมูลใน `exam_attempt`
        $stmtInsert = $conn->prepare("INSERT INTO exam_attempt (exmne_id, exam_id, time_limit, attempt_round) 
                                      VALUES (:exmne_id, :exam_id, :time_limit, :attempt_round)");
        $stmtInsert->execute([
            ':exmne_id' => $examineeId,
            ':exam_id' => $examId,
            ':time_limit' => $timeLimit,
            ':attempt_round' => $nextAttemptRound,
        ]);

        // ตั้งค่า URL สำหรับ Redirect
        $redirectUrl = "home.php?page=exam&id=$examId&attempt_round=$nextAttemptRound";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Exam Details</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Additional CSS for aesthetics and responsiveness -->
    <style>
        body {
            background: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .card-header {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .card {
                margin: 1rem;
            }
        }
        /* Fade-in animation */
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
        // หากมี URL สำหรับ Redirect ให้เปลี่ยนหน้า
        const redirectUrl = "<?php echo $redirectUrl; ?>";
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    </script>
</head>
<body>
<div class="container min-vh-100 d-flex justify-content-center align-items-center">
    <div class="row w-100">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <div class="card fade-in">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="fw-bold">
                        <i class="fas fa-file-alt me-2"></i>
                        <?php echo htmlspecialchars($selExam['ex_title']); ?>
                    </h2>
                </div>
                <div class="card-body px-5">
                    <p class="text-muted text-center mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php echo htmlspecialchars($selExam['ex_description']); ?>
                    </p>

                    <!-- Exam Details Section -->
                    <div class="row text-center mb-4">
                        <div class="col-6">
                            <h6 class="fw-bold">
                                <i class="fas fa-list-ol me-2"></i> Number of Questions
                            </h6>
                            <p class="badge bg-primary fs-6">
                                <?php echo $selExam['ex_questlimit_display']; ?>
                            </p>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-bold">
                                <i class="fas fa-stopwatch me-2"></i> Time Limit
                            </h6>
                            <p class="badge bg-danger fs-6">Set Time Limit</p>
                        </div>
                    </div>

                    <!-- Exam Form -->
                    <form method="post" id="startExamForm">
                        <div class="mb-3">
                            <label for="examTimeLimit" class="form-label fw-bold">
                                <i class="fas fa-clock me-2"></i> Enter Time (in minutes):
                            </label>
                            <input type="number" id="examTimeLimit" name="examTimeLimit" class="form-control" placeholder="e.g., 30" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg fw-bold">
                                <i class="fas fa-play me-2"></i> Start Exam
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ใช้การยืนยันแบบ native confirm dialog
document.getElementById('startExamForm').addEventListener('submit', function(e) {
    e.preventDefault(); // ป้องกันไม่ให้ฟอร์มส่งทันที
    // แสดงการยืนยัน
    if (confirm("Are you sure you want to start the exam? Once started, the timer will begin.")) {
        // หากยืนยัน ส่งฟอร์มต่อไป
        this.submit();
    }
});
</script>
</body>
</html>
