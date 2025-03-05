<?php
include("../../../conn.php");

extract($_POST);

try {
    // ตรวจสอบว่าคำถามซ้ำหรือไม่
    $selQuest = $conn->query("SELECT * FROM exam_question_tbl WHERE exam_id='$examId' AND exam_question='$question'");
    if ($selQuest->rowCount() > 0) {
        $res = array("res" => "exist", "msg" => $question);
    } else {
        // อัปโหลดรูปภาพ
        $uploadedPics = [];
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($_FILES["exam_pic$i"]["name"])) {
                $fileTmp = $_FILES["exam_pic$i"]["tmp_name"];
                $fileName = time() . "_$i_" . $_FILES["exam_pic$i"]["name"];
                $uploadDir = "../../../uploads/";
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($fileTmp, $targetFile)) {
                    $uploadedPics["exam_pic$i"] = $fileName;
                } else {
                    $uploadedPics["exam_pic$i"] = '';
                }
            } else {
                $uploadedPics["exam_pic$i"] = ''; // ตั้งค่าเป็นค่าว่างถ้าไม่ได้อัปโหลดรูป
            }
        }

        // Insert พร้อมรูปภาพ
        $insQuest = $conn->prepare("INSERT INTO exam_question_tbl (
                exam_id,
                exam_question,
                exam_ch1,
                exam_ch2,
                exam_ch3,
                exam_ch4,
                exam_ch5,
                exam_answer,
                exam_pic1,
                exam_pic2,
                exam_pic3
            ) VALUES (
                :examId,
                :question,
                :choice_A,
                :choice_B,
                :choice_C,
                :choice_D,
                :choice_E,
                :correctAnswer,
                :pic1,
                :pic2,
                :pic3
            )
        ");
        $insQuest->execute([
            ':examId' => $examId,
            ':question' => $question,
            ':choice_A' => $choice_A,
            ':choice_B' => $choice_B,
            ':choice_C' => $choice_C,
            ':choice_D' => $choice_D,
            ':choice_E' => $choice_E,
            ':correctAnswer' => $correctAnswer,
            ':pic1' => $uploadedPics['exam_pic1'],
            ':pic2' => $uploadedPics['exam_pic2'],
            ':pic3' => $uploadedPics['exam_pic3']
        ]);

        if ($insQuest) {
            $res = array("res" => "success", "msg" => $question);
        } else {
            $res = array("res" => "failed", "msg" => "Failed to insert question.");
        }
    }
} catch (Exception $e) {
    $res = array("res" => "error", "msg" => $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($res);
?>
