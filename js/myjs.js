/*
document.addEventListener('DOMContentLoaded', function () {
  // Initialize timer
  let examTimeLimit = parseInt(sessionStorage.getItem('examTimeLimit'), 10) || 0;
  let timeRemaining = parseInt(sessionStorage.getItem('timeRemaining'), 10);

  if (isNaN(timeRemaining)) {
      timeRemaining = examTimeLimit * 60;
      sessionStorage.setItem('timeRemaining', timeRemaining);
  }

  const timerDisplay = document.getElementById('timerDisplay');
  const timer = setInterval(function () {
      timeRemaining--;
      sessionStorage.setItem('timeRemaining', timeRemaining);
      let minutes = Math.floor(timeRemaining / 60);
      let seconds = timeRemaining % 60;
      timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
      if (timeRemaining <= 0) {
          clearInterval(timer);
          Swal.fire("Time's up!", "The exam has ended.", "warning").then(() => {
              document.getElementById('submitAnswerFrm').submit();
          });
      }
  }, 1000);

  // Save answers to localStorage
  document.querySelectorAll('input[type="radio"]').forEach(radio => {
      radio.addEventListener('change', function () {
          let savedAnswers = JSON.parse(localStorage.getItem('savedAnswers')) || {};
          savedAnswers[this.name] = this.value;
          localStorage.setItem('savedAnswers', JSON.stringify(savedAnswers));
      });
  });

  // Load saved answers
  const savedAnswers = JSON.parse(localStorage.getItem('savedAnswers')) || {};
  for (let name in savedAnswers) {
      const input = document.querySelector(`input[name="${name}"][value="${savedAnswers[name]}"]`);
      if (input) input.checked = true;
  }

  // Submit button
  const submitButton = document.getElementById('submitAnswerBtn');
  if (submitButton) {
      submitButton.addEventListener('click', function () {
          console.log("Submit button clicked");
          Swal.fire({
              title: "Are you sure?",
              text: "Do you want to submit your answers?",
              icon: "warning",
              showCancelButton: true,
              confirmButtonText: "Yes, submit!",
          }).then((result) => {
              if (result.isConfirmed) {
                  console.log("Submitting the form");
                  document.getElementById('submitAnswerFrm').submit();
              } else {
                  console.log("Submission canceled");
              }
          });
      });
  } else {
      console.error("Submit button not found");
  }
});
*/