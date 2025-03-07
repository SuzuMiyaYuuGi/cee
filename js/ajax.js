// ajax.js

// Admin Log in
$(document).on("submit","#examineeLoginFrm", function(){
  $.post("query/loginExe.php", $(this).serialize(), function(data){
     if(data.res == "invalid")
     {
       Swal.fire(
         'Invalid',
         'Please input valid email / password',
         'error'
       )
     }
     else if(data.res == "success")
     {
       $('body').fadeOut();
       window.location.href='home.php';
     }
  },'json');
  return false;
});

// Add Examinee
$(document).on("submit", "#examineeSignup", function () {
  $.post("query/signExe.php", $(this).serialize(), function (data) {
    if (data.res === "noGender") {
      Swal.fire("No Gender", "Please select gender", "error");
    } else if (data.res === "noLevel") {
      Swal.fire("No Year Level", "Please enter year level", "error");
    } else if (data.res === "fullnameExist") {
      Swal.fire("Fullname Already Exist", data.msg + " is already exist", "error");
    } else if (data.res === "emailExist") {
      Swal.fire("Email Already Exist", data.msg + " is already exist", "error");
    } else if (data.res === "success") {
      Swal.fire({
        title: "Success",
        text: data.msg + " is now successfully registered",
        icon: "success",
        confirmButtonText: "OK"
      }).then(() => {
        // เปลี่ยนไปยังหน้า index.php หลังจากสำเร็จ
        window.location.href = "index.php";
      });
    } else if (data.res === "failed") {
      Swal.fire("Something's Went Wrong", "", "error");
    }
  }, "json");
  return false;
});

// Submit Feedbacks
$(document).on("submit","#addFeedbacks", function(){
  $.post("query/submitFeedbacksExe.php", $(this).serialize(), function(data){
     if(data.res == "success")
     {
       Swal.fire(
         'Success',
         'your feedbacks has been submitted successfully',
         'success'
       )
       $('#addFeedbacks')[0].reset();
     }
     else
     {
       Swal.fire(
         'Error',
         'An error occurred while submitting your feedbacks',
         'error'
       )
     }
  },'json');
  return false;
});





