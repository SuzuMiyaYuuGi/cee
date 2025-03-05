<?php 
$exmneId = $_SESSION['examineeSession']['exmne_id'];

// Select Data ของ examinee ที่ล็อกอิน
$selExmneeData = $conn->query("
    SELECT * 
    FROM examinee_tbl 
    WHERE exmne_id = '$exmneId'
")->fetch(PDO::FETCH_ASSOC);

// --- ลบ exmne_course ---
// $exmneCourse =  $selExmneeData['exmne_course'];

// ดึงข้อสอบทั้งหมด (หรือจะใส่เงื่อนไขอื่น ๆ แทนก็ได้)
$selExam = $conn->query("
    SELECT * 
    FROM exam_tbl 
    ORDER BY ex_id DESC
");
