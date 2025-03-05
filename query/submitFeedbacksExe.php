<?php 
	session_start();
	include("../conn.php");
	extract($_POST);

	$exmneSess = $_SESSION['examineeSession']['exmne_id'];
	$date = date("F d, Y");
	$insFedd = $conn->query("INSERT INTO feedbacks_tbl(exmne_id,fb_exmne_as,fb_feedbacks,fb_date) VALUES('$exmneSess','$asMe','$myFeedbacks','$date') ");

	if($insFedd)
	{
		$res = array("res" => "success");
	}
	else
	{
		$res = array("res" => "failed");
	}

	echo json_encode($res);
?>
