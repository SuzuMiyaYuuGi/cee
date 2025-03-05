<?php
include("../../../conn.php");

// ชี้ไปยังตำแหน่งจริงของ autoload.php
require __DIR__ . '/../js/vendor/autoload.php'; // ชี้ไปที่ admin/js/vendor/autoload.php

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    $examId = $_POST['examId']; // รับ examId
    
    if (isset($_FILES['questionFile']) && $_FILES['questionFile']['error'] == 0) {
        // รับไฟล์
        $fileName = $_FILES['questionFile']['name'];
        $fileTmp  = $_FILES['questionFile']['tmp_name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        $uploadDir = '../../../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $targetFile = $uploadDir . time() . '.' . $fileExtension;
        move_uploaded_file($fileTmp, $targetFile);

        if (in_array($fileExtension, ['csv', 'CSV'])) {
            $reader = IOFactory::createReader('Csv');
        } elseif (in_array($fileExtension, ['xls', 'XLS'])) {
            $reader = IOFactory::createReader('Xls');
        } elseif (in_array($fileExtension, ['xlsx', 'XLSX'])) {
            $reader = IOFactory::createReader('Xlsx');
        } else {
            echo json_encode(["res" => "invalidFile", "msg" => "Invalid file format"]);
            exit;
        }

        $spreadsheet = $reader->load($targetFile);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        $countInsert = 0;

        // เตรียมคำสั่ง SQL
        $stmt = $conn->prepare("
            INSERT INTO exam_question_tbl 
            (exam_id, exam_question, exam_ch1, exam_ch2, exam_ch3, exam_ch4, exam_ch5, exam_answer) 
            VALUES (:exam_id, :exam_question, :exam_ch1, :exam_ch2, :exam_ch3, :exam_ch4, :exam_ch5, :exam_answer)
        ");

        foreach ($data as $rowIndex => $row) {
            if ($rowIndex == 1) continue; // ข้าม header row

            $question = $row['A'] ?? '';
            $choiceA = $row['B'] ?? '';
            $choiceB = $row['C'] ?? '';
            $choiceC = $row['D'] ?? '';
            $choiceD = $row['E'] ?? '';
            $choiceE = $row['F'] ?? '';
            $correctAnswer = $row['G'] ?? '';

            if (!empty($question)) {
                // Bind ค่าลงใน Prepared Statement
                $stmt->bindParam(':exam_id', $examId, PDO::PARAM_INT);
                $stmt->bindParam(':exam_question', $question, PDO::PARAM_STR);
                $stmt->bindParam(':exam_ch1', $choiceA, PDO::PARAM_STR);
                $stmt->bindParam(':exam_ch2', $choiceB, PDO::PARAM_STR);
                $stmt->bindParam(':exam_ch3', $choiceC, PDO::PARAM_STR);
                $stmt->bindParam(':exam_ch4', $choiceD, PDO::PARAM_STR);
                $stmt->bindParam(':exam_ch5', $choiceE, PDO::PARAM_STR);
                $stmt->bindParam(':exam_answer', $correctAnswer, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $countInsert++;
                }
            }
        }

        if (isset($targetFile)) {
            @unlink($targetFile);
        }

        echo json_encode(["res" => "success", "msg" => "Imported $countInsert questions"]);
        exit;
    } else {
        echo json_encode(["res" => "noFile", "msg" => "No file uploaded"]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(["res" => "error", "msg" => $e->getMessage()]);
    exit;
}
?>
