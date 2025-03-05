<?php
// เรียก conn.php ที่เป็น PDO
include("../../../conn.php");

// รับ id ของ question
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// สร้างคำสั่ง SELECT
$sql = "SELECT * FROM exam_question_tbl WHERE eqt_id = :eqt_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':eqt_id', $id, PDO::PARAM_INT);
$stmt->execute();

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($stmt->rowCount() > 0) {
    // ดึงแถวเดียว
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // แปลง BLOB -> base64 (ถ้ามี)
    $img1 = '';
    $img2 = '';
    $img3 = '';

    if (!empty($row['exam_pic1'])) {
        $imgData = base64_encode($row['exam_pic1']);
        $img1 = 'data:image/jpeg;base64,' . $imgData;
    }
    if (!empty($row['exam_pic2'])) {
        $imgData2 = base64_encode($row['exam_pic2']);
        $img2 = 'data:image/jpeg;base64,' . $imgData2;
    }
    if (!empty($row['exam_pic3'])) {
        $imgData3 = base64_encode($row['exam_pic3']);
        $img3 = 'data:image/jpeg;base64,' . $imgData3;
    }
} else {
    // ถ้าไม่มีข้อมูล
    $row = null;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Update Question (PDO)</title>
    <!-- ใส่ CSS/JS (Bootstrap / jQuery) ตามที่ต้องการ -->
</head>
<body>

<fieldset style="width:543px;">
  <legend>
    <i class="facebox-header">
      <i class="edit large icon"></i>&nbsp;Update Question
    </i>
  </legend>

  <div class="col-md-12 mt-4">
    <?php if (!is_null($row)): ?>
      <!-- ฟอร์มส่งไปอัปเดตที่ updateQuestionExe.php -->
      <form method="post" id="updateQuestionFrm" action="updateQuestionExe.php" enctype="multipart/form-data">
        <!-- Primary Key -->
        <input type="hidden" name="eqt_id" value="<?php echo $row['eqt_id']; ?>" />

        <!-- exam_id -->
        <input type="hidden" name="exam_id" value="<?php echo $row['exam_id']; ?>" />

        <!-- exam_status (ถ้ามี) -->
        <input type="hidden" name="exam_status" value="<?php echo $row['exam_status']; ?>" />

        <div class="form-group">
          <legend>Question</legend>
          <textarea name="exam_question" class="form-control" rows="2" required>
            <?php echo htmlspecialchars($row['exam_question']); ?>
          </textarea>
        </div>

        <div class="form-group">
          <legend>Choice A</legend>
          <input type="text" name="exam_ch1" 
                 value="<?php echo htmlspecialchars($row['exam_ch1']); ?>" 
                 class="form-control" required>
        </div>

        <div class="form-group">
          <legend>Choice B</legend>
          <input type="text" name="exam_ch2" 
                 value="<?php echo htmlspecialchars($row['exam_ch2']); ?>" 
                 class="form-control" required>
        </div>

        <div class="form-group">
          <legend>Choice C</legend>
          <input type="text" name="exam_ch3" 
                 value="<?php echo htmlspecialchars($row['exam_ch3']); ?>" 
                 class="form-control" required>
        </div>

        <div class="form-group">
          <legend>Choice D</legend>
          <input type="text" name="exam_ch4" 
                 value="<?php echo htmlspecialchars($row['exam_ch4']); ?>" 
                 class="form-control" required>
        </div>

        <div class="form-group">
          <legend>Choice E</legend>
          <input type="text" name="exam_ch5" 
                 value="<?php echo htmlspecialchars($row['exam_ch5']); ?>" 
                 class="form-control">
        </div>

        <fieldset>
          <legend>Update Pictures</legend>

          <!-- Picture 1 -->
          <div class="form-group">
            <legend>Picture 1</legend>
            <input type="file" name="exam_pic1" class="form-control">
            <?php if ($img1): ?>
              <p style="margin-top:8px;">
                <img src="<?php echo $img1; ?>" alt="Picture 1" style="max-height:120px;">
              </p>
            <?php else: ?>
              <p>No image found.</p>
            <?php endif; ?>
          </div>

          <!-- Picture 2 -->
          <div class="form-group">
            <legend>Picture 2</legend>
            <input type="file" name="exam_pic2" class="form-control">
            <?php if ($img2): ?>
              <p style="margin-top:8px;">
                <img src="<?php echo $img2; ?>" alt="Picture 2" style="max-height:120px;">
              </p>
            <?php else: ?>
              <p>No image found.</p>
            <?php endif; ?>
          </div>

          <!-- Picture 3 -->
          <div class="form-group">
            <legend>Picture 3</legend>
            <input type="file" name="exam_pic3" class="form-control">
            <?php if ($img3): ?>
              <p style="margin-top:8px;">
                <img src="<?php echo $img3; ?>" alt="Picture 3" style="max-height:120px;">
              </p>
            <?php else: ?>
              <p>No image found.</p>
            <?php endif; ?>
          </div>
        </fieldset>

        <div class="form-group">
          <legend class="text-success">Correct Answer</legend>
          <input type="text" name="exam_answer"
                 value="<?php echo htmlspecialchars($row['exam_answer']); ?>"
                 class="form-control" required>
        </div>

        <div class="form-group" align="right">
          <button type="submit" class="btn btn-sm btn-primary">
            Update Now
          </button>
        </div>
      </form>
    <?php else: ?>
      <p><b>Data not found!</b></p>
    <?php endif; ?>
  </div>
</fieldset>

<!-- 
  หากต้องการใช้ AJAX ในการอัปเดต ก็สามารถเขียน script เพิ่ม
  หรือจะใช้ action="updateQuestionExe.php" แบบฟอร์มปกติก็ได้ 
-->

</body>
</html>
