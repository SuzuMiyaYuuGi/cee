<?php
include("../../../conn.php");

// ตั้งค่า header เป็น JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $eqt_id        = $_POST['eqt_id']        ?? null;
    $exam_id       = $_POST['exam_id']       ?? null;
    $exam_question = $_POST['exam_question'] ?? '';
    $exam_ch1      = $_POST['exam_ch1']      ?? '';
    $exam_ch2      = $_POST['exam_ch2']      ?? '';
    $exam_ch3      = $_POST['exam_ch3']      ?? '';
    $exam_ch4      = $_POST['exam_ch4']      ?? '';
    $exam_ch5      = $_POST['exam_ch5']      ?? '';
    $exam_answer   = $_POST['exam_answer']   ?? '';
    $exam_status   = $_POST['exam_status']   ?? 'inactive';

    // สร้าง array สำหรับ field ที่จะอัปเดต
    $fields = [
        "exam_id"       => $exam_id,
        "exam_question" => addslashes($exam_question),
        "exam_ch1"      => addslashes($exam_ch1),
        "exam_ch2"      => addslashes($exam_ch2),
        "exam_ch3"      => addslashes($exam_ch3),
        "exam_ch4"      => addslashes($exam_ch4),
        "exam_ch5"      => addslashes($exam_ch5),
        "exam_answer"   => addslashes($exam_answer),
        "exam_status"   => addslashes($exam_status)
    ];

    // สำหรับ Picture 1
    if (isset($_FILES['exam_pic1']) && $_FILES['exam_pic1']['error'] === 0) {
        $exam_pic1 = addslashes(file_get_contents($_FILES['exam_pic1']['tmp_name']));
        $fields["exam_pic1"] = $exam_pic1;
    } else if (isset($_POST['delete_exam_pic1']) && $_POST['delete_exam_pic1'] == 1) {
        // ถ้าคลิกลบรูป
        $fields["exam_pic1"] = null;
    }
    // สำหรับ Picture 2
    if (isset($_FILES['exam_pic2']) && $_FILES['exam_pic2']['error'] === 0) {
        $exam_pic2 = addslashes(file_get_contents($_FILES['exam_pic2']['tmp_name']));
        $fields["exam_pic2"] = $exam_pic2;
    } else if (isset($_POST['delete_exam_pic2']) && $_POST['delete_exam_pic2'] == 1) {
        $fields["exam_pic2"] = null;
    }
    // สำหรับ Picture 3
    if (isset($_FILES['exam_pic3']) && $_FILES['exam_pic3']['error'] === 0) {
        $exam_pic3 = addslashes(file_get_contents($_FILES['exam_pic3']['tmp_name']));
        $fields["exam_pic3"] = $exam_pic3;
    } else if (isset($_POST['delete_exam_pic3']) && $_POST['delete_exam_pic3'] == 1) {
        $fields["exam_pic3"] = null;
    }

    // สร้าง SQL UPDATE แบบ dynamic
    $setClause = [];
    foreach ($fields as $column => $value) {
        if (is_null($value)) {
            $setClause[] = "$column = NULL";
        } else {
            $setClause[] = "$column = '$value'";
        }
    }
    $setClauseStr = implode(", ", $setClause);

    $sql = "UPDATE exam_question_tbl SET $setClauseStr WHERE eqt_id = '$eqt_id'";

    if ($conn->query($sql)) {
        echo json_encode(['res' => 'success']);
    } else {
        echo json_encode(['res' => 'failed', 'msg' => $conn->error]);
    }
    exit;
}

echo json_encode(['res' => 'invalid']);
