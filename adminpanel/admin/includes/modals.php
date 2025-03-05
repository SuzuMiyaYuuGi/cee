<!-- 
  ลบ Modal For Add Course ทั้งบล็อก
  ลบ Modal For Update Course ทั้งบล็อก
-->

<!-- Modal For Add Exam -->
<div class="modal fade" id="modalForExam" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
   <form class="refreshFrm" id="addExamFrm" method="post">
     <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Exam</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="col-md-12">
          <!-- ลบ Form Group "Select Course" ออก -->

          <div class="form-group">
            <label>Question Limit to Display</label>
            <input type="number" name="examQuestDipLimit" id="" class="form-control" placeholder="Input question limit to display">
          </div>

          <div class="form-group">
            <label>Exam Title</label>
            <input type="" name="examTitle" class="form-control" placeholder="Input Exam Title" required="">
          </div>

          <div class="form-group">
            <label>Exam Description</label>
            <textarea name="examDesc" class="form-control" rows="4" placeholder="Input Exam Description" required=""></textarea>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Add Now</button>
      </div>
    </div>
   </form>
  </div>
</div>


<!-- Modal For Add Examinee -->
<div class="modal fade" id="modalForAddExaminee" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
   <form class="refreshFrm" id="addExamineeFrm" method="post">
     <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Examinee</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="col-md-12">
          <div class="form-group">
            <label>Fullname</label>
            <input type="" name="fullname" id="fullname" class="form-control" placeholder="Input Fullname" autocomplete="off" required="">
          </div>
          <div class="form-group">
            <label>Birhdate</label>
            <input type="date" name="bdate" id="bdate" class="form-control" placeholder="Input Birhdate" autocomplete="off" >
          </div>
          <div class="form-group">
            <label>Gender</label>
            <select class="form-control" name="gender" id="gender">
              <option value="0">Select gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
          </div>

          <!-- ลบ Form Group "Course" ออก -->

          <div class="form-group">
            <label>Year Level</label>
            <input type="" name="year_level" id="year_level" class="form-control" placeholder="Input Year Level" autocomplete="off" required="">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Input Email" autocomplete="off" required="">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Input Password" autocomplete="off" required="">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Add Now</button>
      </div>
    </div>
   </form>
  </div>
</div>


<!-- Modal For Add Exam -->
<div class="modal fade" id="modalForExam" tabindex="-1" role="dialog" aria-labelledby="addExamLabel" aria-modal="true">
  <div class="modal-dialog" role="document">
   <form class="refreshFrm" id="addExamFrm" method="post">
     <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addExamLabel">Add Exam</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="col-md-12">

          <div class="form-group">
            <label>Question Limit to Display</label>
            <input type="number" name="examQuestDipLimit" class="form-control" placeholder="Input question limit to display" required>
          </div>

          <div class="form-group">
            <label>Exam Title</label>
            <input type="text" name="examTitle" class="form-control" placeholder="Input Exam Title" required>
          </div>

          <div class="form-group">
            <label>Exam Description</label>
            <textarea name="examDesc" class="form-control" rows="4" placeholder="Input Exam Description" required></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Add Now</button>
      </div>
    </div>
   </form>
  </div>
</div>

<!-- Modal For Add Question OR Import Questions -->
<div class="modal fade" id="modalForAddQuestion" tabindex="-1" role="dialog" aria-labelledby="addQuestionLabel" aria-modal="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addQuestionLabel">
          Add/Import Questions for <br><?php echo $selExamRow['ex_title']; ?>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Nav tabs -->
      <ul class="nav nav-tabs" id="questionTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="add-q-tab" data-toggle="tab" href="#add-q" role="tab" aria-controls="add-q" aria-selected="true">
            Add Single Question
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="import-q-tab" data-toggle="tab" href="#import-q" role="tab" aria-controls="import-q" aria-selected="false">
            Import from File
          </a>
        </li>
      </ul>

      <div class="tab-content" id="questionTabContent">
        <!-- TAB 1: Add Single Question -->
        <div class="tab-pane fade show active" id="add-q" role="tabpanel" aria-labelledby="add-q-tab">
          <form class="refreshFrm" id="addQuestionFrm" method="post" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Question</label>
                  <input type="hidden" name="examId" value="<?php echo $exId; ?>">
                  <input type="text" name="question" class="form-control" placeholder="Input question" autocomplete="off" required>
                </div>

                <fieldset>
                  <legend>Input word for choice's</legend>
                  <div class="form-group">
                    <label>Choice A</label>
                    <input type="text" name="choice_A" class="form-control" placeholder="Input choice A" autocomplete="off" required>
                  </div>
                  <div class="form-group">
                    <label>Choice B</label>
                    <input type="text" name="choice_B" class="form-control" placeholder="Input choice B" autocomplete="off" required>
                  </div>
                  <div class="form-group">
                    <label>Choice C</label>
                    <input type="text" name="choice_C" class="form-control" placeholder="Input choice C" autocomplete="off" required>
                  </div>
                  <div class="form-group">
                    <label>Choice D</label>
                    <input type="text" name="choice_D" class="form-control" placeholder="Input choice D" autocomplete="off" required>
                  </div>
                  <div class="form-group">
                    <label>Choice E</label>
                    <input type="text" name="choice_E" class="form-control" placeholder="Input choice E" autocomplete="off">
                  </div>
                  <div class="form-group">
                    <label>Correct Answer</label>
                    <input type="text" name="correctAnswer" class="form-control" placeholder="Input correct answer" required>
                  </div>
                </fieldset>

                <fieldset>
                  <legend>Optional Pictures</legend>
                  <div class="form-group">
                    <label>Picture 1 (Optional)</label>
                    <input type="file" name="exam_pic1" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Picture 2 (Optional)</label>
                    <input type="file" name="exam_pic2" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Picture 3 (Optional)</label>
                    <input type="file" name="exam_pic3" class="form-control">
                  </div>
                </fieldset>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Add Now</button>
            </div>
          </form>
        </div>


        <!-- TAB 2: Import from File -->
        <div class="tab-pane fade" id="import-q" role="tabpanel" aria-labelledby="import-q-tab">
          <form id="importQuestionFrm" method="post" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="col-md-12">
                <input type="hidden" name="examId" value="<?php echo $exId; ?>">
                <div class="form-group">
                  <label>Select File (.csv, .xls, .xlsx)</label>
                  <input type="file" name="questionFile" class="form-control" accept=".csv,.xls,.xlsx" required>
                </div>
                <p class="text-muted mt-3">*ไฟล์ต้องมีโครงสร้างคอลัมน์ตัวอย่าง เช่น:<br>Question,ChoiceA,ChoiceB,ChoiceC,ChoiceD,ChoiceE,CorrectAnswer</p>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success">Import Now</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
