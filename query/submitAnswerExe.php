<?php
session_start();
include("../conn.php");

if (isset($_POST['submit'])) {
    if (!isset($_SESSION['examineeSession']['exmne_id'])) {
        echo json_encode(["res" => "error", "msg" => "Session not found"]);
        exit();
    }

    $exmne_id   = $_SESSION['examineeSession']['exmne_id']; 
    $exam_id    = isset($_POST['exam_id']) ? intval($_POST['exam_id']) : 0;
    $attempt_round = isset($_POST['attempt_round']) ? intval($_POST['attempt_round']) : 1;
    $time_spent = isset($_POST['time_spent']) ? intval($_POST['time_spent']) : 0;

    if ($exam_id === 0 || $time_spent < 0) {
        echo json_encode(["res" => "error", "msg" => "Invalid data submitted"]);
        exit();
    }

    try {
        $conn->beginTransaction();

        if (isset($_POST['answer']) && is_array($_POST['answer'])) {
            foreach ($_POST['answer'] as $questId => $answer) {
                $stmtAnswer = $conn->prepare("INSERT INTO exam_answers 
                    (axmne_id, exam_id, quest_id, exans_answer, attempt_round) 
                    VALUES (:axmne_id, :exam_id, :quest_id, :exans_answer, :attempt_round)");
                $stmtAnswer->execute([
                    ':axmne_id'     => $exmne_id,
                    ':exam_id'      => $exam_id,
                    ':quest_id'     => $questId,
                    ':exans_answer' => $answer,
                    ':attempt_round'=> $attempt_round
                ]);
            }
        }

        $stmtUpdateAttempt = $conn->prepare("UPDATE exam_attempt 
            SET time_spent = :time_spent 
            WHERE exmne_id = :exmne_id AND exam_id = :exam_id AND attempt_round = :attempt_round");
        $stmtUpdateAttempt->execute([
            ':time_spent'    => $time_spent,
            ':exmne_id'      => $exmne_id,
            ':exam_id'       => $exam_id,
            ':attempt_round' => $attempt_round
        ]);

        $conn->commit();

        echo json_encode(["res" => "success", "msg" => "Submitted successfully"]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(["res" => "error", "msg" => "Failed to submit: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["res" => "error", "msg" => "No data submitted"]);
}
?>
