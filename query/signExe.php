<?php 
include("../conn.php"); // ไฟล์เชื่อมต่อฐานข้อมูล

// รับค่าจากฟอร์ม
extract($_POST);

// ตรวจสอบข้อมูลซ้ำในตาราง examinee_tbl
$selExamineeFullname = $conn->query("SELECT * FROM examinee_tbl WHERE exmne_fullname='$fullname'");
$selExamineeEmail    = $conn->query("SELECT * FROM examinee_tbl WHERE exmne_email='$email'");

if ($gender == "0") {
  // เพศยังไม่ได้เลือก
  $res = array("res" => "noGender");
} else if (empty($year_level)) {
  // ถ้ายังไม่กรอกระดับ / ชั้นปี
  $res = array("res" => "noLevel");
} else if ($selExamineeFullname->rowCount() > 0) {
  // ชื่อเต็มซ้ำ
  $res = array("res" => "fullnameExist", "msg" => $fullname);
} else if ($selExamineeEmail->rowCount() > 0) {
  // อีเมลซ้ำ
  $res = array("res" => "emailExist", "msg" => $email);
} else {
  // บันทึกลงฐานข้อมูล
  $insData = $conn->query("INSERT INTO examinee_tbl (
        exmne_fullname,
        exmne_gender,
        exmne_birthdate,
        exmne_year_level,
        exmne_email,
        exmne_password
    ) VALUES (
        '$fullname',
        '$gender',
        '$bdate',
        '$year_level',
        '$email',
        '$password'
    )
  ");
  
  if ($insData) {
    $res = array("res" => "success", "msg" => $email);
  } else {
    $res = array("res" => "failed");
  }
}

// ส่งข้อมูลกลับเป็น JSON ให้ Ajax
echo json_encode($res);
?>
