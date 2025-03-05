<?php 
  include("../../../conn.php");
  $id = $_GET['id'];
 
  // ดึงข้อมูล examinee จากตาราง examinee_tbl
  $selExmne = $conn->query("SELECT * FROM examinee_tbl WHERE exmne_id='$id' ")->fetch(PDO::FETCH_ASSOC);
?>

<fieldset style="width:543px;">
  <legend>
    <i class="facebox-header"><i class="edit large icon"></i>&nbsp;Update 
      <b>( <?php echo strtoupper($selExmne['exmne_fullname']); ?> )</b>
    </i>
  </legend>

  <div class="col-md-12 mt-4">
    <form method="post" id="updateExamineeFrm">
      <!-- Fullname -->
      <div class="form-group">
        <legend>Fullname</legend>
        <input type="hidden" name="exmne_id" value="<?php echo $id; ?>">
        <input type="text" name="exFullname" class="form-control" required
               value="<?php echo $selExmne['exmne_fullname']; ?>">
      </div>

      <!-- Gender -->
      <div class="form-group">
        <legend>Gender</legend>
        <select class="form-control" name="exGender">
          <option value="<?php echo $selExmne['exmne_gender']; ?>">
            <?php echo $selExmne['exmne_gender']; ?>
          </option>
          <option value="male">Male</option>
          <option value="female">Female</option>
        </select>
      </div>

      <!-- Birthdate -->
      <div class="form-group">
        <legend>Birthdate</legend>
        <input type="date" name="exBdate" class="form-control" required
               value="<?php echo date('Y-m-d', strtotime($selExmne['exmne_birthdate'])); ?>">
      </div>

      <!-- ลบฟิลด์ Course ทั้งหมด (รวมถึงการ select คอร์สจาก course_tbl) ออก -->

      <!-- Year level -->
      <div class="form-group">
        <legend>Year level</legend>
        <input type="text" name="exYrlvl" class="form-control" required
               value="<?php echo $selExmne['exmne_year_level']; ?>">
      </div>

      <!-- Email -->
      <div class="form-group">
        <legend>Email</legend>
        <input type="email" name="exEmail" class="form-control" required
               value="<?php echo $selExmne['exmne_email']; ?>">
      </div>

      <!-- Password -->
      <div class="form-group">
        <legend>Password</legend>
        <input type="text" name="exPass" class="form-control" required
               value="<?php echo $selExmne['exmne_password']; ?>">
      </div>

      <!-- Status (สมมติว่าอยากเก็บสถานะผู้ใช้งานไว้) -->
      <div class="form-group">
        <legend>Status</legend>
        <!-- ลบ input type="hidden" name="course_id" ทิ้ง เพราะเกี่ยวกับ course -->
        <!-- ปรับ name="newCourseName" เป็น name="exStatus" เพื่อให้ชัดเจนว่าเป็นสถานะ -->
        <input type="text" name="exStatus" class="form-control" required
               value="<?php echo $selExmne['exmne_status']; ?>">
      </div>

      <div class="form-group" align="right">
        <button type="submit" class="btn btn-sm btn-primary">Update Now</button>
      </div>
    </form>
  </div>
</fieldset>
