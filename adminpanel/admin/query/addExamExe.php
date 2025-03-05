<?php
include("../../../conn.php");
extract($_POST);

// ตรวจสอบว่ามี Exam Title นี้ในตาราง exam_tbl หรือไม่ (เพื่อป้องกันการซ้ำชื่อ)
$selExam = $conn->query("SELECT * FROM exam_tbl WHERE ex_title='$examTitle'");

// ลบเงื่อนไขที่เกี่ยวกับการเลือกคอร์ส เช่น (if($courseSelected == "0") ...)

if ($examQuestDipLimit == "" || $examQuestDipLimit == null) {
    $res = array("res" => "noDisplayLimit");
}
else if ($selExam->rowCount() > 0) {
    // ถ้าเจอ Exam ชื่อซ้ำ
    $res = array("res" => "exist", "examTitle" => $examTitle);
}
else {
    // บันทึกเฉพาะ ex_title, ex_time_limit, ex_questlimit_display, ex_description
    $insExam = $conn->query("
        INSERT INTO exam_tbl (
            ex_title,
            ex_questlimit_display,
            ex_description
        ) VALUES (
            '$examTitle',
            '$examQuestDipLimit',
            '$examDesc'
        )
    ");
    if ($insExam) {
        $res = array("res" => "success", "examTitle" => $examTitle);
    } else {
        $res = array("res" => "failed", "examTitle" => $examTitle);
    }
}

echo json_encode($res);
?>
