<?php 
  $id = $_GET['id'];
 
  // ดึงข้อมูล examinee จากตาราง examinee_tbl
  $selExmne = $conn->query("SELECT * FROM examinee_tbl WHERE exmne_id='$id'")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- jQuery และ SweetAlert -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<body class="bg-warning bg-gradient">
<div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow-lg rounded-4 bg-white w-50 w-md-50">
    <div class="card-header bg-primary text-white text-center py-3">
      <h2 class="mb-0 text-uppercase fw-bold">
        <i class="fa-solid fa-user-edit me-2"></i> Edit Profile
      </h2>
    </div>
    <div class="card-body px-4 py-4">
      <form method="post" id="updateExamineeFrm">
        <input type="hidden" name="exmne_id" value="<?php echo $id; ?>">

        <!-- Fullname -->
        <div class="mb-3">
          <label for="exFullname" class="form-label fw-bold">Fullname</label>
          <div class="input-group">
            <span class="input-group-text bg-primary text-white"><i class="fa-solid fa-user"></i></span>
            <input type="text" name="exFullname" id="exFullname" class="form-control" required
                   value="<?php echo htmlspecialchars($selExmne['exmne_fullname']); ?>">
          </div>
        </div>

        <!-- Gender -->
        <div class="mb-3">
          <label for="exGender" class="form-label fw-bold">Gender</label>
          <div class="input-group">
            <span class="input-group-text bg-primary text-white"><i class="fa-solid fa-venus-mars"></i></span>
            <select class="form-select" name="exGender" id="exGender">
              <option value="male"   <?php echo ($selExmne['exmne_gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
              <option value="female" <?php echo ($selExmne['exmne_gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
            </select>
          </div>
        </div>

        <!-- Birthdate -->
        <div class="mb-3">
          <label for="exBdate" class="form-label fw-bold">Birthdate</label>
          <div class="input-group">
            <span class="input-group-text bg-primary text-white"><i class="fa-solid fa-calendar-days"></i></span>
            <input type="date" name="exBdate" id="exBdate" class="form-control" required
                   value="<?php echo date('Y-m-d', strtotime($selExmne['exmne_birthdate'])); ?>">
          </div>
        </div>

        <!-- Year level -->
        <div class="mb-3">
          <label for="exYrlvl" class="form-label fw-bold">Year Level</label>
          <div class="input-group">
            <span class="input-group-text bg-primary text-white"><i class="fa-solid fa-graduation-cap"></i></span>
            <input type="text" name="exYrlvl" id="exYrlvl" class="form-control" required
                   value="<?php echo htmlspecialchars($selExmne['exmne_year_level']); ?>">
          </div>
        </div>

        <!-- Email -->
        <div class="mb-3">
          <label for="exEmail" class="form-label fw-bold">Email</label>
          <div class="input-group">
            <span class="input-group-text bg-primary text-white"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" name="exEmail" id="exEmail" class="form-control" required
                   value="<?php echo htmlspecialchars($selExmne['exmne_email']); ?>">
          </div>
        </div>

        <!-- Password -->
        <div class="mb-4">
          <label for="exPass" class="form-label fw-bold">Password</label>
          <div class="input-group">
            <span class="input-group-text bg-primary text-white"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="exPass" id="exPass" class="form-control" required
                   value="<?php echo htmlspecialchars($selExmne['exmne_password']); ?>">
          </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
          <button type="submit" class="btn btn-success btn-lg px-5 shadow-lg rounded-pill">
            <i class="fa-solid fa-check-circle me-2"></i> Update Now
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).on("submit", "#updateExamineeFrm", function (e) {
  e.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ

  $.post("query/editProfileExe.php", $(this).serialize(), function (data) {
    if (data.res === "success") {
      Swal.fire({
        title: 'Success',
        text: 'Profile updated successfully!',
        icon: 'success',
        showConfirmButton: false,
        timer: 2000
      }).then(() => {
        window.location.href = "home.php?page="; // ส่งไปยังหน้า home.php?page=
      });
    } else {
      Swal.fire(
        'Error',
        'Failed to update the profile.',
        'error'
      );
    }
  }, "json").fail(function (xhr, status, error) {
    Swal.fire(
      'Error',
      'An unexpected error occurred: ' + error,
      'error'
    );
    console.error("AJAX Error:", xhr.responseText);
  });
});
</script>

</body>
</html>
