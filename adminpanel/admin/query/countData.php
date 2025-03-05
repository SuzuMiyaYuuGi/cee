<?php 

// ลบส่วนที่เกี่ยวกับ Count All Course ออก

// Count All Exam
$selExam = $conn->query("SELECT COUNT(ex_id) as totExam FROM exam_tbl")->fetch(PDO::FETCH_ASSOC);

?>
