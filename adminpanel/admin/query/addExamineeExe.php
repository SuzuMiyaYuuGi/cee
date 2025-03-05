<?php 
include("../../../conn.php");

extract($_POST);

// ตรวจสอบข้อมูลซ้ำในตาราง examinee_tbl
$selExamineeFullname = $conn->query("SELECT * FROM examinee_tbl WHERE exmne_fullname='$fullname' ");
$selExamineeEmail    = $conn->query("SELECT * FROM examinee_tbl WHERE exmne_email='$email' ");

// เงื่อนไขตรวจสอบ (ลบ else if($course == "0") { ... } ออก)
if($gender == "0")
{
	$res = array("res" => "noGender");
}
else if($year_level == "0")
{
	$res = array("res" => "noLevel");
}
else if($selExamineeFullname->rowCount() > 0)
{
	$res = array("res" => "fullnameExist", "msg" => $fullname);
}
else if($selExamineeEmail->rowCount() > 0)
{
	$res = array("res" => "emailExist", "msg" => $email);
}
else
{
	// ลบคอลัมน์ exmne_course ออกจากการ INSERT
	$insData = $conn->query("
		INSERT INTO examinee_tbl(
			exmne_fullname,
			exmne_gender,
			exmne_birthdate,
			exmne_year_level,
			exmne_email,
			exmne_password
		) 
		VALUES(
			'$fullname',
			'$gender',
			'$bdate',
			'$year_level',
			'$email',
			'$password'
		)
	");
	if($insData)
	{
		$res = array("res" => "success", "msg" => $email);
	}
	else
	{
		$res = array("res" => "failed");
	}
}

echo json_encode($res);
?>
