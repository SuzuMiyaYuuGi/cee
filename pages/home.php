<?php
include("conn.php");

// ดึงรายการข้อสอบทั้งหมด
$sql = "SELECT * FROM exam_tbl";
$stmt = $conn->prepare($sql);
$stmt->execute();
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงจำนวนข้อสอบทั้งหมด
$seltotalex = $conn->query("SELECT COUNT(*) AS exam FROM exam_tbl");
$totalExam = $seltotalex->fetch(PDO::FETCH_ASSOC)['exam'];

// ดึงจำนวนข้อสอบที่เคยทำสำหรับผู้ใช้ปัจจุบัน
$exmneId = $_SESSION['examineeSession']['exmne_id'] ?? null;
$seltotalre = $conn->query("SELECT COUNT(*) AS re FROM exam_attempt WHERE exmne_id = '$exmneId'");
$totalresult = $seltotalre->fetch(PDO::FETCH_ASSOC)['re'];
?>
<!-- Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="app-main__outer">
    <div id="refreshData">
        <div class="app-main__inner">
            
            <!-- 🏆 แบนเนอร์ต้อนรับ -->
            <div class="bg-plum-plate text-white text-center p-5 rounded-4 shadow-lg mb-5">
                <h1 class="fw-bold">🎓 Welcome to the A-Level Practice</h1>
                <p class="fs-5">Prepare yourself and test your knowledge with our wide range of exams!</p>
            </div>

            <!-- 📊 สถิติโดยรวม -->
            <div class="row g-4">
                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-lg border-0 bg-happy-green text-white rounded-4">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold"><i class="fas fa-book-open me-2"></i> Total Exams</h5>
                            <h2 class="fw-bold display-4"><?php echo $totalExam; ?></h2>
                            <p class="mb-0">Available exams</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-lg border-0 bg-love-kiss text-white rounded-4">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold"><i class="fas fa-check-circle me-2"></i> Exams Taken</h5>
                            <h2 class="fw-bold display-4"><?php echo $totalresult; ?></h2>
                            <p class="mb-0">Completed exam attempts</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4"> 
                    <div class="card shadow-lg border-0 bg-arielle-smile text-white rounded-4">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold"><i class="fas fa-stopwatch me-2"></i> Fastest Exam Completion</h5>
                            <h2 class="fw-bold display-4">
                                <?php 
                                // ดึงเวลาที่ใช้น้อยที่สุดจาก exam_attempt สำหรับผู้ใช้ปัจจุบัน (exmne_id)
                                $stmtMinTime = $conn->prepare("SELECT MIN(time_spent) AS min_time FROM exam_attempt WHERE time_spent > 0 AND exmne_id = :exmneId");
                                $stmtMinTime->execute([':exmneId' => $exmneId]);
                                $minTimeRow = $stmtMinTime->fetch(PDO::FETCH_ASSOC);
                                $minTimeSpent = isset($minTimeRow['min_time']) ? $minTimeRow['min_time'] : 0;

                                // แปลงเวลาเป็นรูปแบบ MM:SS
                                $minutes = floor($minTimeSpent / 60);
                                $seconds = $minTimeSpent % 60;
                                echo sprintf("%02d:%02d", $minutes, $seconds);
                                ?>
                            </h2>
                            <p class="mb-0">Shortest exam completion time</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📝 รายการข้อสอบ -->
            <div class="container mt-5">
                <div class="row">
                    <div class="col text-center">
                        <h2 class="fw-bold text-primary"><i class="fas fa-list-alt me-2"></i> Available Exams</h2>
                        <p class="text-muted">Select an exam and start your journey.</p>
                    </div>
                </div>

                <div class="row g-4">
                    <?php if (!empty($exams)): ?>
                        <?php foreach ($exams as $exam): ?>
                            <div class="col-lg-6 col-md-6">
                                <div class="card shadow-sm border-0 rounded-4 p-3 hover-effect">
                                    <div class="card-body">
                                        <h4 class="text-primary fw-bold"><i class="fas fa-file-alt me-2"></i> <?php echo htmlspecialchars($exam['ex_title']); ?></h4>
                                        <p class="text-muted">
                                            <?php echo htmlspecialchars(substr($exam['ex_description'], 0, 120)); ?>...
                                        </p>
                                        <p><strong>Number of Questions:</strong> <?php echo $exam['ex_questlimit_display']; ?></p>
                                        <a href="home.php?page=examdetail&id=<?php echo intval($exam['ex_id']); ?>" 
                                           class="btn btn-outline-primary w-100 fw-bold"><i class="fas fa-play-circle me-2"></i> Take Exam</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col text-center">
                            <p class="text-muted">⚠ No exams available at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- CSS: ทำให้ Card มีเอฟเฟกต์ -->
<style>
    .hover-effect:hover {
        transform: translateY(-5px);
        transition: 0.3s ease-in-out;
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
    }

    .bg-deep-blue {
        background-color: #0056b3 !important;
    }
</style>
