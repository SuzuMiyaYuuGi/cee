<?php
// Get `examId` and `attempt_round` from GET request
$examId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$attemptRound = isset($_GET['attempt_round']) ? intval($_GET['attempt_round']) : 1;

// Retrieve exam details
$sqlExam = "SELECT * FROM exam_tbl WHERE ex_id = :examId";
$stmtExam = $conn->prepare($sqlExam);
$stmtExam->bindParam(':examId', $examId, PDO::PARAM_INT);
$stmtExam->execute();

$exTitle = "";
$exDesc = "";
$exDisplayLimit = 0;

if ($stmtExam->rowCount() > 0) {
    $rExam = $stmtExam->fetch();
    $exTitle = $rExam['ex_title'];
    $exDesc = $rExam['ex_description'];
    $exDisplayLimit = $rExam['ex_questlimit_display'];
}

// ดึงค่า time_limit จากฐานข้อมูล
$sqlTimeLimit = "SELECT time_limit FROM exam_attempt 
                 WHERE exmne_id = :exmne_id 
                   AND exam_id = :exam_id 
                   AND attempt_round = :attempt_round";
$stmtTimeLimit = $conn->prepare($sqlTimeLimit);
$stmtTimeLimit->execute([
    ':exmne_id' => $_SESSION['examineeSession']['exmne_id'],
    ':exam_id' => $examId,
    ':attempt_round' => $attemptRound
]);
$timeLimitRow   = $stmtTimeLimit->fetch(PDO::FETCH_ASSOC);
$examTimeLimit  = $timeLimitRow['time_limit'] ?? 0; // (เป็นนาที)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Exam Page</title>
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Arial', sans-serif;
        }
        .exam-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            background: #fff;
            margin-bottom: 2rem;
        }
        .exam-card-header {
            background: #4a90e2;
            color: #fff;
            padding: 1.5rem;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .exam-card-header h2 {
            margin: 0;
            font-size: 2rem;
        }
        .exam-card-header .subheading {
            font-size: 1rem;
            opacity: 0.85;
        }
        .timer-container {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .question-container {
            margin-bottom: 1.5rem;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        .question-container:last-child {
            border-bottom: none;
        }
        .question-text {
            font-size: 1.1rem;
            font-weight: bold;
        }
        .answer-option {
            margin-bottom: 0.5rem;
        }
        #tableList {
            width: 100%;
        }
        #submitAnswerBtn {
            font-size: 1.1rem;
            font-weight: bold;
        }
        /* Button styling for "Jump to Unanswered" */
        #jumpUnanswered {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- โครงสร้างหลัก (Main Content) -->
    <div class="app-main__outer">
      <div class="app-main__inner">
        <!-- container -->
        <div class="container my-5">
            <!-- Exam Details Card -->
            <div class="exam-card">
                <div class="exam-card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2><?php echo htmlspecialchars($exTitle); ?></h2>
                            <p class="subheading"><?php echo htmlspecialchars($exDesc); ?></p>
                        </div>
                        <div class="timer-container">
                            <label>Remaining Time: </label>
                            <span id="timerDisplay" style="color: #ffd700;">00:00</span>
                        </div>
                    </div>
                </div>
                <div class="exam-card-body p-4">
                    <!-- Exam Form -->
                    <form method="post" id="submitAnswerFrm">
                        <input type="hidden" name="submit" value="1">
                        <input type="hidden" name="exam_id" value="<?php echo $examId; ?>">
                        <input type="hidden" name="attempt_round" value="<?php echo $attemptRound; ?>">
                        <input type="hidden" name="time_spent" id="timeSpent">
                        
                        <!-- Hidden field สำหรับจำนวนคำถามทั้งหมด -->
                        <?php $questionCount = 0; ?>
                        <div class="table-responsive">
                            <table class="table table-borderless" id="tableList">
                                <?php
                                $exDisplayLimit = (int)$exDisplayLimit;
                                $sqlQuest = "SELECT * FROM exam_question_tbl 
                                             WHERE exam_id = :examId 
                                             ORDER BY RAND() 
                                             LIMIT $exDisplayLimit";
                                $stmtQuest = $conn->prepare($sqlQuest);
                                $stmtQuest->bindParam(':examId', $examId, PDO::PARAM_INT);
                                $stmtQuest->execute();
                                
                                if ($stmtQuest->rowCount() > 0) {
                                    $i = 1;
                                    while ($qRow = $stmtQuest->fetch()) {
                                        $questionCount++;
                                        $questId = $qRow['eqt_id'];
                                        // เพิ่ม attribute data-quest-id ให้กับ container
                                        ?>
                                        <tr class="question-container" data-quest-id="<?php echo $questId; ?>">
                                            <td>
                                                <p class="question-text">
                                                    <?php echo $i++; ?>.) <?php echo htmlspecialchars($qRow['exam_question']); ?>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php 
                                                // แสดงรูป (ถ้ามี)
                                                for ($j = 1; $j <= 3; $j++) {
                                                    $picField = 'exam_pic' . $j;
                                                    if (!empty($qRow[$picField])) {
                                                        $imgData = base64_encode($qRow[$picField]);
                                                        $src = 'data:image/jpeg;base64,' . $imgData;
                                                        echo "<p class='text-center'><img src='{$src}' class='img-fluid rounded mb-3' style='max-height:300px;'></p>";
                                                    }
                                                } 
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="row">
                                                    <?php 
                                                    // สร้างอาเรย์ของตัวเลือกทั้งหมด
                                                    $choices = [
                                                        $qRow['exam_ch1'],
                                                        $qRow['exam_ch2'],
                                                        $qRow['exam_ch3'],
                                                        $qRow['exam_ch4'],
                                                        $qRow['exam_ch5'],
                                                    ];
                                                    // กรองตัวเลือกที่ไม่ว่างเปล่า
                                                    $choices = array_values(array_filter($choices, function($choice) {
                                                        return trim($choice) !== "";
                                                    }));
                                                    // สุ่มตัวเลือก
                                                    shuffle($choices);
                                                    foreach ($choices as $choice): ?>
                                                    <div class="col-md-6 answer-option">
                                                        <div class="form-check">
                                                            <input 
                                                                name="answer[<?php echo $questId; ?>]"
                                                                value="<?php echo htmlspecialchars($choice); ?>"
                                                                class="form-check-input" 
                                                                type="radio" 
                                                                required
                                                            >
                                                            <label class="form-check-label">
                                                                <?php echo htmlspecialchars($choice); ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <!-- เก็บจำนวนคำถามทั้งหมดใน hidden field -->
                                    <input type="hidden" id="totalQuestions" value="<?php echo $questionCount; ?>">
                                    <tr>
                                        <td class="text-end py-4">
                                            <button 
                                                type="button" 
                                                id="submitAnswerBtn" 
                                                class="btn btn-primary px-4 py-2"
                                            >
                                                Submit <i class="fas fa-paper-plane ms-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php
                                } else {
                                    echo "<tr><td><b class='text-danger'>No question at this moment</b></td></tr>";
                                }
                                ?>
                            </table>
                        </div><!-- /.table-responsive -->
                        <!-- ปุ่ม Jump to Unanswered อยู่ด้านล่างสุด -->
                        <div class="text-center">
                          <button type="button" id="jumpUnanswered" class="btn btn-secondary">
                              Jump to Unanswered Question
                          </button>
                        </div>
                    </form>
                </div><!-- /.exam-card-body -->
            </div> <!-- /.exam-card -->
        </div> <!-- /.container -->
      </div> <!-- /.app-main__inner -->
    </div> <!-- /.app-main__outer -->

    <!-- JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const timerDisplay    = document.getElementById('timerDisplay');
        const totalTime       = <?php echo $examTimeLimit * 60; ?>; // รวมเวลาทั้งหมด (วินาที)
        const currentExam     = `exam_<?php echo $examId; ?>_round_<?php echo $attemptRound; ?>`;
        let examFinished      = false; // Flag บอกว่าส่งข้อสอบแล้วหรือยัง

        // ป้องกันการออกจากหน้า (ถ้ายังไม่ส่งข้อสอบ)
        window.addEventListener('beforeunload', function (e) {
            if (!examFinished && timeRemaining > 0) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // จัดการ localStorage สำหรับการจับเวลาข้อสอบ
        const lastExam = localStorage.getItem('currentExam');
        if (lastExam !== currentExam) {
            localStorage.removeItem('timeRemaining');
            localStorage.removeItem('savedAnswers');
            localStorage.setItem('currentExam', currentExam);
        }

        let timeRemaining = localStorage.getItem('timeRemaining')
            ? parseInt(localStorage.getItem('timeRemaining'))
            : totalTime;

        function updateTimerDisplay() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        }

        // โหลดคำตอบที่บันทึกไว้จาก localStorage (ถ้ามี)
        const savedAnswers = JSON.parse(localStorage.getItem('savedAnswers')) || {};
        for (const [name, value] of Object.entries(savedAnswers)) {
            const input = document.querySelector(`input[name="${name}"][value="${value}"]`);
            if (input) input.checked = true;
        }

        // ฟังก์ชันตรวจสอบว่าตอบครบทุกข้อหรือไม่
        const submitBtn = document.getElementById('submitAnswerBtn');
        const totalQuestions = parseInt(document.getElementById('totalQuestions').value);
        function checkAllAnswered() {
            const answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
            submitBtn.disabled = answeredCount !== totalQuestions;
        }

        // ตั้งค่า submit button เริ่มต้นให้ disabled
        submitBtn.disabled = true;

        // บันทึกคำตอบเมื่อมีการเปลี่ยนแปลงและตรวจสอบว่าตอบครบหรือไม่
        document.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener('change', function () {
                const savedAnswers = JSON.parse(localStorage.getItem('savedAnswers')) || {};
                savedAnswers[this.name] = this.value;
                localStorage.setItem('savedAnswers', JSON.stringify(savedAnswers));
                checkAllAnswered();
            });
        });

        updateTimerDisplay();

        // ฟังก์ชันส่งข้อมูลด้วย Ajax
        function submitAnswers() {
            examFinished = true; // ระบุว่าส่งข้อสอบแล้ว
            document.getElementById('timeSpent').value = totalTime - timeRemaining;
            const form = document.getElementById('submitAnswerFrm');
            const formData = new FormData(form);

            fetch('query/submitAnswerExe.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.res === "success") {
                    localStorage.removeItem('timeRemaining');
                    localStorage.removeItem('savedAnswers');
                    window.location.href = "?page=result&id=<?php echo $examId; ?>&round=<?php echo $attemptRound; ?>";
                } else {
                    alert("Submission failed: " + data.msg);
                    document.getElementById('submitAnswerBtn').disabled = false;
                    examFinished = false;
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while submitting your answers.");
                document.getElementById('submitAnswerBtn').disabled = false;
                examFinished = false; 
            });
        }

        // ตั้งเวลานับถอยหลัง เมื่อหมดเวลาให้ส่งข้อมูล
        const timer = setInterval(() => {
            if (timeRemaining > 0) {
                timeRemaining--;
                localStorage.setItem('timeRemaining', timeRemaining);
                updateTimerDisplay();
            } else {
                clearInterval(timer);
                alert("Time's up!");
                localStorage.removeItem('timeRemaining');
                localStorage.removeItem('savedAnswers');
                submitAnswers();
            }
        }, 1000);

        // เมื่อคลิกปุ่ม Submit ให้ตรวจสอบก่อนส่ง
        submitBtn.addEventListener('click', function () {
            if (confirm("Are you sure you want to submit your exam?")) {
                this.disabled = true;
                submitAnswers();
            }
        });

        // เมื่อคลิกปุ่ม Jump to Unanswered ให้เลื่อนไปยังคำถามแรกที่ยังไม่ได้ตอบ
        document.getElementById('jumpUnanswered').addEventListener('click', function () {
            const questionContainers = document.querySelectorAll('tr.question-container');
            for (const container of questionContainers) {
                const questId = container.getAttribute('data-quest-id');
                if (!document.querySelector(`input[name="answer[${questId}]"]:checked`)) {
                    container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    container.style.backgroundColor = "#fff3cd";
                    setTimeout(() => {
                        container.style.backgroundColor = "";
                    }, 1500);
                    break;
                }
            }
        });
    });
    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
