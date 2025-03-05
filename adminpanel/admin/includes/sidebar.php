<div class="app-sidebar sidebar-shadow">
    <!-- สไตล์สำหรับลิงก์และการแสดงผลใน Sidebar -->
    <style>
        .vertical-nav-menu a {
            text-decoration: none !important;
        }
        .vertical-nav-menu a:hover {
            text-decoration: none !important;
        }
        /* เมื่อ Sidebar ถูกพับ (collapsed) ให้ซ่อนข้อความ แต่ให้แสดงไอคอน */
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
        <div class=""></div>
        <div class="header__pane ml-auto">
            <div>
                <!-- ปุ่มสำหรับพับ Sidebar พร้อมเรียกฟังก์ชัน toggleSidebar() -->
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
                <li class="app-sidebar__heading">
                    <a href="home.php">
                    <i class="metismenu-icon pe-7s-home"></i>
                        <span class="menu-text">Dashboards</span>
                        <span class="menu-icon">📊</span>
                    </a>
                </li>

                <!-- ลบหัวข้อ MANAGE COURSE และรายการในเมนูทั้งหมดที่เกี่ยวกับ course ออก -->

                <li class="app-sidebar__heading">
                    <span class="menu-icon">📝</span>
                    <span class="menu-text">MANAGE EXAM</span>
                </li>
                <li>
                    <a href="#">
                        <i class="metismenu-icon pe-7s-display2"></i>
                        <span class="menu-text">Exam</span>
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modalForExam">
                                <i class="metismenu-icon"></i>
                                <span class="menu-text">Add Exam</span>
                                <span class="menu-icon">➕</span>
                            </a>
                        </li>
                        <li>
                            <a href="home.php?page=manage-exam">
                                <i class="metismenu-icon"></i>
                                <span class="menu-text">Manage Exam</span>
                                <span class="menu-icon">⚙️</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="app-sidebar__heading">
                    <span class="menu-icon">👥</span>
                    <span class="menu-text">MANAGE EXAMINEE</span>
                </li>
                <li>
                    <a href="" data-toggle="modal" data-target="#modalForAddExaminee">
                        <i class="metismenu-icon pe-7s-add-user"></i>
                        <span class="menu-text">Add Examinee</span>
                        <span class="menu-icon">➕</span>
                    </a>
                </li>
                <li>
                    <a href="home.php?page=manage-examinee">
                        <i class="metismenu-icon pe-7s-users"></i>
                        <span class="menu-text">Manage Examinee</span>
                        <span class="menu-icon">👤</span>
                    </a>
                </li>

                <li class="app-sidebar__heading">
                    <span class="menu-icon">📈</span>
                    <span class="menu-text">REPORTS</span>
                </li>
                <li>
                    <a href="home.php?page=examinee-result">
                        <i class="metismenu-icon pe-7s-cup"></i>
                        <span class="menu-text">Examinee Result</span>
                        <span class="menu-icon">🏆</span>
                    </a>
                </li>

                <li class="app-sidebar__heading">
                    <span class="menu-icon">💬</span>
                    <span class="menu-text">FEEDBACKS</span>
                </li>
                <li>
                    <a href="home.php?page=feedbacks">
                        <i class="metismenu-icon pe-7s-chat"></i>
                        <span class="menu-text">All Feedbacks</span>
                        <span class="menu-icon">✍️</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
// ฟังก์ชันสำหรับสลับคลาส "closed-sidebar" เมื่อคลิกปุ่มพับ Sidebar
function toggleSidebar(){
    var sidebar = document.querySelector('.app-sidebar');
    sidebar.classList.toggle('closed-sidebar');
}
</script>
