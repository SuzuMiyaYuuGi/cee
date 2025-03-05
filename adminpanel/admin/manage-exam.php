<?php 
session_start();

if(!isset($_SESSION['admin']['adminnakalogin']) == true) header("location:index.php");
?>
<?php include("../../conn.php"); ?>

<!-- MAO NI ANG HEADER -->
<?php include("includes/header.php"); ?>      

<!-- UI THEME DIRI -->
<?php include("includes/ui-theme.php"); ?>

<div class="app-main">
  <!-- sidebar diri  -->
  <?php include("includes/sidebar.php"); ?>

  <?php 
     $exId = $_GET['id'];

     // ดึงข้อมูล Exam จาก exam_tbl
     $selExam = $conn->query("SELECT * FROM exam_tbl WHERE ex_id='$exId' ");
     $selExamRow = $selExam->fetch(PDO::FETCH_ASSOC);
  ?>

  <div class="app-main__outer">
    <div class="app-main__inner">
      <div class="app-page-title">
        <div class="page-title-wrapper">
          <div class="page-title-heading">
            <div> MANAGE EXAM
              <div class="page-title-subheading">
                Add Question for <?php echo $selExamRow['ex_title']; ?>
              </div>
            </div>
          </div>
        </div>
      </div>        
      
      <div class="col-md-12">
        <div id="refreshData">
          <div class="row">
            <div class="col-md-6">
              <div class="main-card mb-3 card">
                <div class="card-header">
                  <i class="header-icon lnr-license icon-gradient bg-plum-plate"> </i>Exam Information
                </div>
                <div class="card-body">
                  <form method="post" id="updateExamFrm">
                    <!-- ฟอร์มแก้ไขข้อมูล Exam -->
                    <div class="form-group">
                      <label>Exam Title</label>
                      <input type="hidden" name="examId" value="<?php echo $selExamRow['ex_id']; ?>">
                      <input type="text" name="examTitle" class="form-control" required
                             value="<?php echo $selExamRow['ex_title']; ?>">
                    </div>  

                    <div class="form-group">
                      <label>Exam Description</label>
                      <input type="text" name="examDesc" class="form-control" required
                             value="<?php echo $selExamRow['ex_description']; ?>">
                    </div>  

                    <div class="form-group">
                      <label>Display limit</label>
                      <input type="number" name="examQuestDipLimit" class="form-control"
                             value="<?php echo $selExamRow['ex_questlimit_display']; ?>"> 
                    </div>

                    <div class="form-group" align="right">
                      <button type="submit" class="btn btn-primary btn-lg">Update</button>
                    </div> 
                  </form>                           
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <?php 
                  $selQuest = $conn->query("SELECT * FROM exam_question_tbl WHERE exam_id='$exId' ORDER BY eqt_id DESC");
              ?>
              <div class="main-card mb-3 card">
                <div class="card-header">
                  <i class="header-icon lnr-license icon-gradient bg-plum-plate"></i>
                  Exam Question's 
                  <span class="badge badge-pill badge-primary ml-2">
                    <?php echo $selQuest->rowCount(); ?>
                  </span>
                  <div class="btn-actions-pane-right">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalForAddQuestion">
                      Add Question
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="scroll-area-sm" style="min-height: 400px;">
                    <div class="scrollbar-container">
                      <?php
                      if($selQuest->rowCount() > 0) { ?>
                        <div class="table-responsive">
                          <table class="align-middle mb-0 table table-borderless table-striped table-hover" id="tableList">
                            <thead>
                              <tr>
                                <th class="text-left pl-1">Question</th>
                                <th class="text-center" width="20%">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $i = 1;
                              while ($selQuestionRow = $selQuest->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr>
                                  <td>
                                    <b><?php echo $i++ ; ?>.) <?php echo $selQuestionRow['exam_question']; ?></b><br>
                                    
                                    <!-- Choice A -->
                                    <?php 
                                    if($selQuestionRow['exam_ch1'] == $selQuestionRow['exam_answer']) { ?>
                                      <span class="pl-4 text-success">
                                        A - <?php echo $selQuestionRow['exam_ch1']; ?>
                                      </span><br>
                                    <?php } else { ?>
                                      <span class="pl-4">
                                        A - <?php echo $selQuestionRow['exam_ch1']; ?>
                                      </span><br>
                                    <?php }

                                    // Choice B
                                    if($selQuestionRow['exam_ch2'] == $selQuestionRow['exam_answer']) { ?>
                                      <span class="pl-4 text-success">
                                        B - <?php echo $selQuestionRow['exam_ch2']; ?>
                                      </span><br>
                                    <?php } else { ?>
                                      <span class="pl-4">
                                        B - <?php echo $selQuestionRow['exam_ch2']; ?>
                                      </span><br>
                                    <?php }

                                    // Choice C
                                    if($selQuestionRow['exam_ch3'] == $selQuestionRow['exam_answer']) { ?>
                                      <span class="pl-4 text-success">
                                        C - <?php echo $selQuestionRow['exam_ch3']; ?>
                                      </span><br>
                                    <?php } else { ?>
                                      <span class="pl-4">
                                        C - <?php echo $selQuestionRow['exam_ch3']; ?>
                                      </span><br>
                                    <?php }

                                    // Choice D
                                    if($selQuestionRow['exam_ch4'] == $selQuestionRow['exam_answer']) { ?>
                                      <span class="pl-4 text-success">
                                        D - <?php echo $selQuestionRow['exam_ch4']; ?>
                                      </span><br>
                                    <?php } else { ?>
                                      <span class="pl-4">
                                        D - <?php echo $selQuestionRow['exam_ch4']; ?>
                                      </span><br>
                                    <?php }

                                    // **Choice E** (ใหม่)
                                    if(!empty($selQuestionRow['exam_ch5'])) {
                                      if($selQuestionRow['exam_ch5'] == $selQuestionRow['exam_answer']) { ?>
                                        <span class="pl-4 text-success">
                                          E - <?php echo $selQuestionRow['exam_ch5']; ?>
                                        </span><br>
                                      <?php } else { ?>
                                        <span class="pl-4">
                                          E - <?php echo $selQuestionRow['exam_ch5']; ?>
                                        </span><br>
                                      <?php }
                                    }
                                    ?>
                                  </td>
                                  <td class="text-center">
                                    <a rel="facebox" href="facebox_modal/updateQuestion.php?id=<?php echo $selQuestionRow['eqt_id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                      Update
                                    </a>
                                    <button type="button" id="deleteQuestion" data-id='<?php echo $selQuestionRow['eqt_id']; ?>'
                                            class="btn btn-danger btn-sm">
                                      Delete
                                    </button>
                                  </td>
                                </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      <?php 
                      } else { ?>
                        <h4 class="text-primary">No question found...</h4>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div> 
      </div>
    </div>

    <!-- MAO NI IYA FOOTER -->
    <?php include("includes/footer.php"); ?>
    <?php include("includes/modals.php"); ?>
  </div>
</div>
