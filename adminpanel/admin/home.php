<?php 
session_start();

// เช็ค session หากไม่ได้ล็อกอินก็ redirect
if(!isset($_SESSION['admin']['adminnakalogin']) == true) {
  header("location:index.php");
}
?>
<?php include("../../conn.php"); ?>

<!-- HEADER -->
<?php include("includes/header.php"); ?>      

<!-- UI THEME -->
<?php include("includes/ui-theme.php"); ?>

<div class="app-main">
  <!-- sidebar -->
  <?php include("includes/sidebar.php"); ?>

  <!-- Condition If unza nga page gi click -->
  <?php 
    @$page = $_GET['page'];

    if($page != '')
    {
      // *** ลบ else if เกี่ยวกับ add-course / manage-course ออก ***
      
      if($page == "manage-exam")
      {
        include("pages/manage-exam.php");
      }
      else if($page == "manage-examinee")
      {
        include("pages/manage-examinee.php");
      }
      else if($page == "result")
      {
        include("pages/result.php");
      }
      else if($page == "feedbacks")
      {
        include("pages/feedbacks.php");
      }
      else if($page == "examinee-result")
      {
        include("pages/examinee-result.php");
      }
    }
    // ถ้าไม่มี page ไหนถูกระบุ ก็ไปหน้า home
    else
    {
      include("pages/home.php"); 
    }
  ?> 

  <!-- FOOTER -->
  <?php include("includes/footer.php"); ?>

  <?php include("includes/modals.php"); ?>
</div>
