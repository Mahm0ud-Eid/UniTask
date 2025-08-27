// function for Swal alert
function alert (title, text, icon) {
  Swal.fire({
    title,
    text,
    icon,
    confirmButtonText: 'Ok',
    timer: 2000
  })
}

/**
 * Sends quiz answers to the server via an AJAX request.
 *
 * @param {Object} jsonData - The quiz data to be sent in JSON format.
 * @param {number} quizId - The name of the quiz.
 * @returns {void}
 */
const sendData = (jsonData, quizId) => {
  $.ajax({
    url: `${window.btu.api_base}quiz/${quizId}/take-quiz`,
    type: 'POST',
    data: JSON.stringify(jsonData),
    headers: {
      Authorization: window.btu.api_key
    },
    contentType: 'application/json',
    success: (data) => {
      if (data.success) {
        alert('Success!', 'Quiz saved successfully', 'success')
        localStorage.clear()
        setTimeout(() => {
          location = '/'
        }, 2000)
      } else {
        alert('Error!', data.error, 'error')
      }
    },
    error: (error) => {
      alert('Error!', error.error, 'error')
    }
  })
}

$(() => {
  const totalQuestions = $('.question-container').length
  let currentQuestion = 1
  const endsAtUTC = new Date($('#ends-at').val())
  const timezoneOffset = endsAtUTC.getTimezoneOffset() * 60 * 1000
  const endsAtLocal = new Date(endsAtUTC.getTime() - timezoneOffset)
  const endTime = ''
  const submitBtn = $('#submit-btn')
  const quizId = localStorage.getItem('quizId') || $('#quiz-id').val()
  updateNavigationButtons()
  localStorage.setItem('quizId', quizId)

  // fill answers from local storage if quiz id is the same
  if (
    localStorage.getItem('answers') &&
    localStorage.getItem('quizId') === quizId
  ) {
    const answers = JSON.parse(localStorage.getItem('answers'))
    for (const questionId in answers) {
      if (Object.prototype.hasOwnProperty.call(answers, questionId)) {
        const answer = answers[questionId]
        $(
          `input[data-api=${questionId}][value='${encodeURIComponent(answer)}']`
        ).prop('checked', true)
        $(`textarea[data-api=${questionId}]`).val(answer)
      }
    }
  }

  // Hide all questions except the first one
  $('.question-container').not(':first').hide()

  // Show the next question when clicking the "Next" button
  $('.next-btn').click(() => {
    let answer = $(`input[name=question${currentQuestion}]:checked`).val()
    let apiQuestionId = $(
      `input[name=question${currentQuestion}]:checked`
    ).data('api')
    if (answer === undefined) {
      answer = $(`textarea[name=question${currentQuestion}]`).val()
      apiQuestionId = $(`textarea[name=question${currentQuestion}]`).data(
        'api'
      )
    }
    if (answer) {
      // Save the answer and move to the next question
      const answers = JSON.parse(localStorage.getItem('answers')) || {}
      answers[apiQuestionId] = answer
      localStorage.setItem('answers', JSON.stringify(answers))
      currentQuestion++
      $('.question-container').hide()
      $(`#question${currentQuestion}`).show()
      updateNavigationButtons()
    } else {
      // Alert the user to select an answer
      alert('Please select an answer before proceeding.')
    }
  })

  // Show the previous question when clicking the "Previous" button
  $('.previous-btn').click(() => {
    if (currentQuestion > 1) {
      // Move to the previous question
      currentQuestion--
      $('.question-container').hide()
      $(`#question${currentQuestion}`).show()
      updateNavigationButtons()
    }
  })

  // Update the "Next" and "Previous" buttons based on the current question
  function updateNavigationButtons () {
    if (currentQuestion === 1) {
      $('.previous-btn').hide()
    } else {
      $('.previous-btn').show()
    }
    if (currentQuestion === totalQuestions) {
      $('.next-btn').hide()
      submitBtn.show()
    } else {
      $('.next-btn').show()
      submitBtn.hide()
    }
  }

  /**
   Starts a countdown timer and updates the text content of an HTML element with an ID of "timer" every second.
   @function
   @name startTimer
   @returns {void} This function does not return any value.
   @description
   This function starts a countdown timer by subtracting the current time from the endsAtLocal time,
   which is a time value in milliseconds obtained from a local source. If the time remaining is less than zero,
   the timer is cleared and the submit button is clicked. Otherwise, the time remaining is calculated in hours,
   minutes, and seconds using the Math.floor() method. The padZero() function is then used to format the hours,
   minutes, and seconds in a two-digit format, which is concatenated using template literals and passed to the text()
   method to update the text content of the HTML element with an ID of "timer".
   @example
   startTimer();
   */
  function startTimer () {
    const timerInterval = setInterval(() => {
      const timeRemaining = endsAtLocal.getTime() - new Date().getTime()

      if (timeRemaining < 0) {
        clearInterval(timerInterval)
        submitBtn.click()
      } else {
        const hours = Math.floor(timeRemaining / (60 * 60 * 1000))
        const minutes = Math.floor(
          (timeRemaining % (60 * 60 * 1000)) / (60 * 1000)
        )
        const seconds = Math.floor((timeRemaining % (60 * 1000)) / 1000)
        $('#timer').text(
          `${padZero(hours)}:${padZero(minutes)}:${padZero(seconds)}`
        )
      }
    }, 1000)
  }

  // Add a leading zero to single-digit numbers
  function padZero (num) {
    return num < 10 ? `0${num}` : num
  }

  // Start the timer when the page is loaded
  startTimer()

  // Submit the quiz when the time runs out
  submitBtn.click(() => {
    // add the answer for the final question
    let answer = $(`input[name=question${currentQuestion}]:checked`).val()
    let apiQuestionId = $(
      `input[name=question${currentQuestion}]:checked`
    ).data('api')
    if (answer === undefined) {
      answer = $(`textarea[name=question${currentQuestion}]`).val()
      apiQuestionId = $(`textarea[name=question${currentQuestion}]`).data(
        'api'
      )
    }
    if (answer) {
      const answers = JSON.parse(localStorage.getItem('answers')) || {}
      answers[apiQuestionId] = answer
      localStorage.setItem('answers', JSON.stringify(answers))
    } else {
      alert('Please select an answer before submitting the quiz.')
      return false
    }
    const answers = JSON.parse(localStorage.getItem('answers')) || {}
    const jsonData = {
      answers
    }
    sendData(jsonData, quizId)
    return false
  })
})
