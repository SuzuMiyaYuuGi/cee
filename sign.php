<!DOCTYPE html>
<html lang="en">
<head>
  <title>AAE LOGIN</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Favicon -->
  <link rel="icon" type="login-ui/image/png" href="images/icons/favicon.ico"/>

  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="login-ui/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="login-ui/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="login-ui/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
  <link rel="stylesheet" type="text/css" href="login-ui/vendor/animate/animate.css">
  <link rel="stylesheet" type="text/css" href="login-ui/vendor/css-hamburgers/hamburgers.min.css">
  <link rel="stylesheet" type="text/css" href="login-ui/vendor/select2/select2.min.css">
  <link rel="stylesheet" type="text/css" href="login-ui/vendor/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" type="text/css" href="login-ui/css/util.css">
  <link rel="stylesheet" type="text/css" href="login-ui/css/main.css">
</head>

<body>
<a href="index.php" class="btn btn-primary" style="position: absolute; top: 10px; left: 10px; z-index: 1000;">
    Back
</a>
  <div class="limiter">
    <div class="container-login100">
      <div class="wrap-login100">
        
        <!-- Header / Title -->
        <div class="login100-form-title" style="background-image: url(login-ui/images/bg-01.jpg);">
          <span class="login100-form-title-1">
            Registration
          </span>
        </div>

        <!-- ฟอร์มลงทะเบียน -->
        <form method="post" id="examineeSignup" class="login100-form validate-form">
          <!-- ชื่อ-นามสกุล -->
          <div class="wrap-input100 validate-input m-b-26" data-validate="Fullname is required">
            <span class="label-input100">Fullname</span>
            <input class="input100" type="text" name="fullname" id="fullname" placeholder="Enter Fullname">
            <span class="focus-input100"></span>
          </div>

          <!-- เพศ -->
          <div class="wrap-input100 validate-input m-b-18" data-validate = "Gender is required">
            <span class="label-input100">Gender</span>
            <select class="form-control" name="gender" id="gender">
              <option value="0">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>

          <!-- วันเกิด -->
          <div class="wrap-input100 validate-input m-b-26" data-validate="Birthdate is required">
            <span class="label-input100">Birthdate</span>
            <input type="date" name="bdate" id="bdate" class="form-control" required>
            <span class="focus-input100"></span>
          </div>

          <!-- ระดับ/ชั้นปี -->
          <div class="wrap-input100 validate-input m-b-26" data-validate="Year Level is required">
            <span class="label-input100">Year Level</span>
            <input class="input100" type="text" id="year_level" name="year_level" placeholder="Enter Year Level">
            <span class="focus-input100"></span>
          </div>

          <!-- อีเมล -->
          <div class="wrap-input100 validate-input m-b-26" data-validate="Email is required">
            <span class="label-input100">Email</span>
            <input class="input100" type="email" name="email" id="email" placeholder="Enter Email">
            <span class="focus-input100"></span>
          </div>

          <!-- รหัสผ่าน -->
          <div class="wrap-input100 validate-input m-b-26" data-validate="Password is required">
            <span class="label-input100">Password</span>
            <input class="input100" type="password" name="password" id="password" placeholder="Enter Password">
            <span class="focus-input100"></span>
          </div>

          <!-- ปุ่มลงทะเบียน -->
          <div class="container-login100-form-btn" align="right">
            <button type="submit" class="login100-form-btn">
              Register
            </button>
          </div>
        </form>
        <!-- สิ้นสุดฟอร์ม -->
        
      </div>
    </div>
  </div>

  <!-- JS หลักของ Template -->
  <script src="login-ui/vendor/jquery/jquery-3.2.1.min.js"></script>
  <script src="login-ui/vendor/animsition/js/animsition.min.js"></script>
  <script src="login-ui/vendor/bootstrap/js/popper.js"></script>
  <script src="login-ui/vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="login-ui/vendor/select2/select2.min.js"></script>
  <script src="login-ui/vendor/daterangepicker/moment.min.js"></script>
  <script src="login-ui/vendor/daterangepicker/daterangepicker.js"></script>
  <script src="login-ui/vendor/countdowntime/countdowntime.js"></script>
  <script src="login-ui/js/main.js"></script>

  <!-- JS ของเรา -->
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/ajax.js"></script>
  <script type="text/javascript" src="js/sweetalert.js"></script>
</body>
</html>
