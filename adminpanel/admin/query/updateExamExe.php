<?php 
include("../../../conn.php");
 
extract($_POST);

// ลบ cou_id='$courseId', ออก
$updExam = $conn->query("
  UPDATE exam_tbl
  SET
    ex_title='$examTitle',
    ex_questlimit_display='$examQuestDipLimit',
    ex_description='$examDesc'
  WHERE ex_id='$examId'
");

if($updExam) {
  $res = array("res" => "success", "msg" => $examTitle);
} else {
  $res = array("res" => "failed");
}

echo json_encode($res);
?>
