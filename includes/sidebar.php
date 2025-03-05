<div class="app-sidebar sidebar-shadow">
    <!-- สไตล์สำหรับลิงก์และการแสดงผลใน Sidebar -->
    <style>
        .vertical-nav-menu a {
            text-decoration: none !important;
        }
        .vertical-nav-menu a:hover {
            text-decoration: none !important;
        }
        /* เมื่อ Sidebar ถูกพับ (collapse) จะซ่อนเฉพาะข้อความ */
        .app-sidebar.closed-sidebar .menu-text {
            display: none;
        }
        /* จัดรูปแบบให้ไอคอนมีขนาดคงที่ */
        .menu-icon {
            display: inline-block;
            width: 30px;
            text-align: center;
        }
        /* ปรับการจัดวางในลิงก์เมื่อ Sidebar ถูกพับ */
        .app-sidebar.closed-sidebar .vertical-nav-menu a {
            text-align: center;
            padding: 10px 0;
        }
    </style>

    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <!-- ปุ่มสำหรับพับ Sidebar -->
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar" onclick="toggleSidebar()">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                    <!-- Icon ที่แสดงในปุ่ม -->
                    <span class="hamburger-icon">🔒</span>
                </button>
            </div>
        </div>
    </div>

    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
                <span class="hamburger-icon">📱</span>
            </button>
        </div>
    </div>

    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>

    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu">
                
                <!-- ลิงก์ Continue Exam (ซ่อนไว้ก่อน) -->
                <li id="continueExamItem" style="display: none;">
                    <a href="#" id="continueExamLink">
                        <i class="metismenu-icon pe-7s-play"></i>
                        <span class="menu-text">Continue Exam</span>
                        <span class="menu-icon">⏳</span>
                    </a>
                </li>

                <li class="app-sidebar__heading">
                    <a href="home.php?page=">
                        <i class="metismenu-icon pe-7s-home"></i>
                        <span class="menu-text">Home</span>
                        <span class="menu-icon">🏠</span>
                    </a>
                </li>
                <li class="app-sidebar__heading">
                    <span class="menu-icon">📚</span>
                    <span class="menu-text">AVAILABLE EXAM'S</span>
                </li>
                <li>
                    <a href="#">
                        <i class="metismenu-icon pe-7s-display2"></i>
                        <span class="menu-text">All Exam's</span>
                        <span class="menu-icon">📝</span>
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul>
                        <?php 
                        if ($selExam->rowCount() > 0) {
                            while ($selExamRow = $selExam->fetch(PDO::FETCH_ASSOC)) { ?>
                                <li>
                                    <a href="home.php?page=examdetail&id=<?php echo $selExamRow['ex_id']; ?>">
                                        <span class="menu-text">
                                        <?php 
                                        $lengthOfTxt = strlen($selExamRow['ex_title']);
                                        if ($lengthOfTxt >= 23) { 
                                            echo substr($selExamRow['ex_title'], 0, 20); ?>..... ⏱️
                                        <?php } else {
                                            echo $selExamRow['ex_title'];
                                        }
                                        ?>
                                        </span>
                                    </a>
                                </li>
                            <?php }
                        } else { ?>
                            <a href="#">
                                <i class="metismenu-icon"></i>
                                <span class="menu-text">No Exam's @ the moment 😔</span>
                            </a>
                        <?php } ?>
                    </ul>
                </li>

                <li class="app-sidebar__heading">
                    <span class="menu-icon">✅</span>
                    <span class="menu-text">TAKEN EXAM'S</span>
                </li>
                <li>
                    <?php 
                    // ใช้ GROUP BY เพื่อให้แสดงเฉพาะข้อสอบแต่ละชุดครั้งเดียว
                    $selTakenExam = $conn->query("SELECT et.ex_id, et.ex_title FROM exam_tbl et 
                                                  INNER JOIN exam_attempt ea 
                                                  ON et.ex_id = ea.exam_id 
                                                  WHERE exmne_id='$exmneId' 
                                                  GROUP BY ea.exam_id 
                                                  ORDER BY ea.examat_id");

                    if ($selTakenExam->rowCount() > 0) {
                        while ($selTakenExamRow = $selTakenExam->fetch(PDO::FETCH_ASSOC)) { ?>
                            <a href="home.php?page=result&id=<?php echo $selTakenExamRow['ex_id']; ?>">
                                <i class="metismenu-icon pe-7s-ribbon"></i>
                                <span class="menu-text"><?php echo htmlspecialchars($selTakenExamRow['ex_title']); ?></span>
                                <span class="menu-icon">✅</span>
                            </a>
                        <?php }
                    } else { ?>
                        <a href="#" class="pl-3">
                            <span class="menu-text">You are not taking exam yet 😕</span>
                        </a>
                    <?php } ?>
                </li>

                <li class="app-sidebar__heading">
                    <span class="menu-icon">💬</span>
                    <span class="menu-text">FEEDBACKS</span>
                </li>
                <li>
                    <a href="#" data-toggle="modal" data-target="#feedbacksModal">
                        <i class="metismenu-icon pe-7s-comment"></i>
                        <span class="menu-text">Add Feedbacks</span>
                        <span class="menu-icon">✍️</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
// ฟังก์ชันสำหรับสลับคลาส closed-sidebar เมื่อคลิกปุ่มพับ Sidebar
function toggleSidebar(){
    var sidebar = document.querySelector('.app-sidebar');
    sidebar.classList.toggle('closed-sidebar');
}

// ตรวจสอบ localStorage ว่ามีข้อสอบที่ยังทำค้างอยู่หรือไม่
document.addEventListener('DOMContentLoaded', function() {
    // อ่านค่าจาก localStorage
    const currentExam   = localStorage.getItem('currentExam');    // ตัวอย่าง: "exam_1_round_1"
    const timeRemaining = localStorage.getItem('timeRemaining');  // เวลาที่เหลือ (วินาที)
    
    // ตรวจสอบเงื่อนไข: มีข้อมูล currentExam และเวลาที่เหลือ > 0
    if (currentExam && timeRemaining && parseInt(timeRemaining) > 0) {
        const continueExamItem = document.getElementById('continueExamItem');
        const continueExamLink = document.getElementById('continueExamLink');
        continueExamItem.style.display = 'block';

        // แยก examId กับ attemptRound จาก currentExam (เช่น "exam_1_round_2")
        const parts        = currentExam.split('_');
        const extractedId  = parts[1]; 
        const extractedRnd = parts[3];

        // กำหนด href ให้ลิงก์ "Continue Exam" ไปยังหน้าที่ค้างไว้
        continueExamLink.href = `home.php?page=exam&id=${extractedId}&attempt_round=${extractedRnd}`;
    }
});
</script>
