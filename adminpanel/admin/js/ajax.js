// Admin Log in
$(document).on("submit","#adminLoginFrm", function(){
  $.post("query/loginExe.php", $(this).serialize(), function(data){
     if(data.res == "invalid") {
       Swal.fire(
         'Invalid',
         'Please input valid username / password',
         'error'
       )
     }
     else if(data.res == "success") {
       $('body').fadeOut();
       window.location.href='home.php';
     }
  },'json');

  return false;
});


//-----------------------------------------------------------
// *** ลบทุกฟังก์ชันที่เกี่ยวข้องกับ Course ออกไป ***
//   - Add Course
//   - Update Course
//   - Delete Course
//-----------------------------------------------------------


// Delete Exam
$(document).on("click", "#deleteExam", function(e){
   e.preventDefault();
   var id = $(this).data("id");
   $.ajax({
     type : "post",
     url : "query/deleteExamExe.php",
     dataType : "json",  
     data : {id:id},
     cache : false,
     success : function(data){
       if(data.res == "success")
       {
         Swal.fire(
           'Success',
           'Selected Exam successfully deleted',
           'success'
         )
         refreshDiv();
       }
     },
     error : function(xhr, ErrorStatus, error){
       console.log(status.error);
     }
   });
   return false;
});


// Add Exam 
$(document).on("submit","#addExamFrm" , function(){
 $.post("query/addExamExe.php", $(this).serialize() , function(data){
  // *** ลบส่วนเช็ค noSelectedCourse ออกไป ***
  if(data.res == "noDisplayLimit") {
    Swal.fire(
      'No Display Limit',
      'Please input question display limit',
      'error'
    )
  }
  else if(data.res == "exist") {
    Swal.fire(
     'Already Exist',
     data.examTitle.toUpperCase() + '<br>Already Exist',
     'error'
    )
  }
  else if(data.res == "success") {
    Swal.fire(
     'Success',
     data.examTitle.toUpperCase() + '<br>Successfully Added',
     'success'
    ).then(() => {
     location.reload(); // รีเฟรชหน้าหลังจากเพิ่มสำเร็จ
    });
  }
 },'json')
 return false;
});


// Update Exam 
$(document).on("submit","#updateExamFrm" , function(){
 $.post("query/updateExamExe.php", $(this).serialize() , function(data){
   if(data.res == "success") {
     Swal.fire(
         'Update Successfully',
         data.msg + ' <br>are now successfully updated',
         'success'
      )
     refreshDiv();
   }
   else if(data.res == "failed") {
     Swal.fire(
       "Something's went wrong!",
        'Somethings went wrong',
       'error'
     )
   }
 },'json')
 return false;
});


// ... โค้ดอื่น ๆ ในไฟล์ ajax.js ...

// Update Question
$(document).on("submit", "#updateQuestionFrm", function(e) {
  e.preventDefault();

  // ใช้ FormData สำหรับไฟล์
  var formData = new FormData(this);

  $.ajax({
    url: "query/updateQuestionExe.php", // ปรับ path ให้ตรง
    type: "POST",
    data: formData,
    contentType: false,   // สำคัญ
    processData: false,   // สำคัญ
    dataType: "json",
    success: function(data) {
      if (data.res === "success") {
        Swal.fire(
          'Success',
          'Selected question has been successfully updated!',
          'success'
        ).then(() => {
          location.reload(); // รีเฟรชหน้าหลังอัปเดตสำเร็จ
        });
      } else if (data.res === "failed") {
        Swal.fire(
          "Error",
          data.msg || "Failed to update question!",
          'error'
        );
      } else {
        Swal.fire(
          "Error",
          data.msg || "An unexpected error occurred.",
          "error"
        );
      }
    },
    error: function(xhr, status, error) {
      console.error("AJAX Error:", xhr.responseText);
      // ตรวจสอบว่ามีเนื้อหา HTML ผิดปกติกลับมาหรือไม่
      if (xhr.responseText.trim().startsWith("<")) {
        Swal.fire(
          "Error",
          "Server returned an invalid response. Please check the PHP file.",
          "error"
        );
      } else {
        Swal.fire("Error", "AJAX Error: " + error, "error");
      }
    }
  });

  return false;
});

// ... โค้ดอื่น ๆ ในไฟล์ ajax.js ...




// ... โค้ดอื่นในไฟล์ ajax.js ...



// Delete Question
$(document).on("click", "#deleteQuestion", function(e){
   e.preventDefault();
   var id = $(this).data("id");
   $.ajax({
     type : "post",
     url : "query/deleteQuestionExe.php",
     dataType : "json",  
     data : {id:id},
     cache : false,
     success : function(data){
       if(data.res == "success") {
         Swal.fire(
           'Deleted Success',
           'Selected question successfully deleted',
           'success'
         )
         refreshDiv();
       }
     },
     error : function(xhr, ErrorStatus, error){
       console.log(status.error);
     }
   });
   return false;
});


$(document).on("submit", "#addQuestionFrm", function (e) {
  e.preventDefault();

  var formData = new FormData(this); // ใช้ FormData เพื่อรองรับไฟล์
  $.ajax({
    url: "query/addQuestionExe.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (data) {
      if (data.res === "success") {
        Swal.fire({
          title: "Success",
          text: data.msg + " question successfully added!",
          icon: "success",
          allowOutsideClick: false,
          confirmButtonText: "OK",
        }).then((result) => {
          if (result.isConfirmed) {
            location.reload(); // รีเฟรชทั้งหน้า
          }
        });
      } else if (data.res === "exist") {
        Swal.fire("Already Exist", data.msg + " question already exists", "error");
      } else if (data.res === "failed") {
        Swal.fire("Error", data.msg, "error");
      } else if (data.res === "error") {
        Swal.fire("Error", "Server Error: " + data.msg, "error");
      } else {
        Swal.fire("Error", "Unexpected error occurred.", "error");
      }
    },
    error: function (xhr, status, error) {
      console.log(xhr.responseText); // แสดงข้อความ error ใน console สำหรับ debugging
      Swal.fire("Error", "AJAX Error: " + error, "error");
    },
  });
});





// Add Examinee
$(document).on("submit","#addExamineeFrm" , function(){
 $.post("query/addExamineeExe.php", $(this).serialize() , function(data){
   if(data.res == "noGender") {
     Swal.fire(
       'No Gender',
       'Please select gender',
       'error'
     )
   }
   // *** ลบ else if(data.res == "noCourse") ออกไป ***
   else if(data.res == "noLevel") {
     Swal.fire(
       'No Year Level',
       'Please select year level',
       'error'
     )
   }
   else if(data.res == "fullnameExist") {
     Swal.fire(
       'Fullname Already Exist',
       data.msg + ' are already exist',
       'error'
     )
   }
   else if(data.res == "emailExist") {
     Swal.fire(
       'Email Already Exist',
       data.msg + ' are already exist',
       'error'
     )
   }
   else if(data.res == "success") {
     Swal.fire(
       'Success',
       data.msg + ' are now successfully added',
       'success'
     )
     refreshDiv();
     $('#addExamineeFrm')[0].reset();
   }
   else if(data.res == "failed") {
     Swal.fire(
       "Something's Went Wrong",
       '',
       'error'
     )
   }
 },'json')
 return false;
});


// Update Examinee
$(document).on("submit","#updateExamineeFrm" , function(){
 $.post("query/updateExamineeExe.php", $(this).serialize() , function(data){
    if(data.res == "success") {
       Swal.fire(
         'Success',
         data.exFullname + ' <br>has been successfully updated!',
         'success'
       )
       refreshDiv();
    }
 },'json')
 return false;
});


// Refresh
function refreshDiv() {
 $('#tableList').load(document.URL +  ' #tableList');
 $('#refreshData').load(document.URL +  ' #refreshData');
}

// Import Questions (Submit form #importQuestionFrm)
$(document).on("submit", "#importQuestionFrm", function(e) {
  e.preventDefault();

  // ใช้ FormData สำหรับอัปโหลดไฟล์
  var formData = new FormData(this);

  $.ajax({
    url: "query/importQuestionExe.php", // ไฟล์ประมวลผล
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(data) {
      if (data.res == "success") {
        Swal.fire({
          title: 'Imported Successfully',
          text: data.msg, // เช่น "Imported 10 questions"
          icon: 'success',
          allowOutsideClick: false,
          confirmButtonText: 'OK'
        }).then((result) => {
          if (result.isConfirmed) {
            location.reload(); // รีเฟรชทั้งหน้า
          }
        });
      } else if (data.res == "invalidFile") {
        Swal.fire(
          'Invalid File',
          data.msg,
          'error'
        );
      } else {
        Swal.fire(
          'Error',
          data.msg,
          'error'
        );
      }
    },
    error: function() {
      Swal.fire(
        'Error',
        'Something',
        'error'
      );
    }
  });
});


