<link rel="stylesheet" type="text/css" href="css/mycss.css">
<div class="app-main__outer">
  <div class="app-main__inner">
    <div class="app-page-title">
      <div class="page-title-wrapper">
        <div class="page-title-heading">
          <div>EXAMINEE RESULT</div>
        </div>
      </div>
    </div>        
    
    <div class="col-md-12">
      <div class="main-card mb-3 card">
        <div class="card-header">Examinee Result</div>
        <div class="table-responsive">
          <table class="align-middle mb-0 table table-borderless table-striped table-hover" id="tableList">
            <thead>
              <tr>
                <th>Fullname</th>
                <th>Exam Name</th>
                <th>Scores</th>
                <th>Round</th>
                <th>Ratings</th>
                <th class="text-center" width="8%">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                // ดึงข้อมูลผลการสอบจาก examinee_tbl และ exam_attempt
                $selExmne = $conn->query("SELECT * FROM examinee_tbl et 
                                          INNER JOIN exam_attempt ea 
                                            ON et.exmne_id = ea.exmne_id 
                                          ORDER BY ea.examat_id DESC");
                if($selExmne->rowCount() > 0) {
                  while ($selExmneRow = $selExmne->fetch(PDO::FETCH_ASSOC)) { 
                    $eid          = $selExmneRow['exmne_id'];
                    $exam_id      = $selExmneRow['exam_id']; // จาก exam_attempt
                    $attemptRound = $selExmneRow['attempt_round'];
                    $examat_id    = $selExmneRow['examat_id']; // รหัสการสอบ (Exam Attempt ID)
                    
                    // ดึงชื่อวิชาจาก exam_tbl โดยใช้ exam_id
                    $selExNameResult = $conn->query("SELECT * FROM exam_tbl WHERE ex_id='$exam_id'");
                    if($selExNameResult && $selExNameResult->rowCount() > 0) {
                        $selExName = $selExNameResult->fetch(PDO::FETCH_ASSOC);
                    } else {
                        // หากไม่พบข้อมูล exam_tbl (เช่น ผู้ใช้ยังไม่เสร็จข้อสอบ) ให้ข้ามแถวนี้
                        continue;
                    }
                    
                    // ดึงคะแนนจากการสอบ โดยเชื่อมโยงตาราง exam_question_tbl, exam_answers และ exam_attempt
                    $selScore = $conn->query("SELECT * FROM exam_question_tbl eqt 
                                              INNER JOIN exam_answers ea 
                                                ON eqt.eqt_id = ea.quest_id 
                                               AND eqt.exam_answer = ea.exans_answer 
                                              INNER JOIN exam_attempt eat
                                                ON eat.attempt_round = ea.attempt_round 
                                               AND eat.exam_id = ea.exam_id
                                               AND eat.exmne_id = ea.axmne_id
                                               AND eat.examat_id = '$examat_id'
                                              WHERE ea.axmne_id='$eid' 
                                                AND ea.exam_id='$exam_id' 
                                                AND ea.attempt_round='$attemptRound'
                                                AND ea.exans_status='new'
                                              ");
                    if($selScore === false) {
                        // หาก query ผิดพลาดหรือไม่มีข้อมูล ให้ข้ามแถวนี้
                        continue;
                    }
                    $scoreCount = $selScore->rowCount();
                    $over       = $selExName['ex_questlimit_display'];
                    
                    // คำนวณเปอร์เซ็นต์คะแนน
                    $rating = ($over > 0) ? ($scoreCount / $over) * 100 : 0;
              ?>
                  <tr>
                    <td><?php echo htmlspecialchars($selExmneRow['exmne_fullname']); ?></td>
                    <td><?php echo htmlspecialchars($selExName['ex_title']); ?></td>
                    <td>
                      <span><?php echo $scoreCount; ?></span> / <?php echo $over; ?>
                    </td>
                    <td><?php echo $attemptRound; ?></td>
                    <td><?php echo number_format($rating, 2) . '%'; ?></td>
                    <td>
                      <!-- ส่งค่าที่จำเป็นไปยัง result.php -->
                      <a href="?page=result&exam_id=<?php echo $exam_id; ?>&examinee_id=<?php echo $eid; ?>&examat_id=<?php echo $examat_id; ?>&round=<?php echo $attemptRound; ?>"  
                         class="btn btn-success btn-sm">
                        View
                      </a>
                    </td>
                  </tr>
              <?php 
                  }
                } else { 
              ?>
                  <tr>
                    <td colspan="6">
                      <h3 class="p-3">No Data Found</h3>
                    </td>
                  </tr>
              <?php 
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
