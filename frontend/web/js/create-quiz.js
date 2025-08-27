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
 * Sends quiz data to the server via an AJAX request.
 *
 * @param {Object} jsonData - The quiz data to be sent in JSON format.
 * @param {number} subjectId - The ID of the subject the quiz belongs to.
 * @param {number} quizId - The name of the quiz.
 * @returns {void}
 */
const sendData = (jsonData, subjectId, quizId) => {
  let url = ''
  let type = ''
  if (!isNaN(subjectId)) {
    url = `${window.btu.api_base}quiz/${subjectId}/create-quiz`
    type = 'POST'
  } else if (!isNaN(quizId)) {
    url = `${window.btu.api_base}quiz/${quizId}/update-quiz`
    type = 'POST'
  } else {
    // Invalid request
    return
  }
  $.ajax({
    url,
    type,
    data: JSON.stringify(jsonData),
    headers: {
      Authorization: window.btu.api_key
    },
    contentType: 'application/json',
    success: (data) => {
      if (data.success) {
        alert('Success!', 'Quiz saved successfully', 'success')
        // setTimeout(() => {
        //   location = `/teacher/quizzes`
        // }, 2000)
      } else {
        alert('Error!', data.error, 'error')
      }
    },
    error: (error) => {
      alert('Error!', error.error, 'error')
    }
  })
}

$(document).ready(() => {
  // when clicking add_question button
  $('.mcq-options').click((event) => {
    const numOptions = $(event.currentTarget).attr('data-amount')
    addQuestion(1, numOptions)
  })

  $('#add_text_question').click(() => {
    addQuestion(3)
  })

  $('#add_true_false_question').click(() => {
    addQuestion(2)
  })

  $('#submit-quiz').click(() => {
    const subjectId = parseInt($('#subject_id').val(), 10)
    const quizId = parseInt($('#quiz_id').val(), 10)
    // get the quiz title
    const quizName = $('#quiz_name').val()
    // get the quiz description
    const quizDescription = $('#quiz_description').val()

    const quizActive = parseInt($('#quiz_active').val(), 10)
    const quizTime = parseInt($('#quiz_time').val(), 10)
    const quizVisibility = parseInt($('#quiz_result_visibility').val(), 10)
    let quizStartTime = $('#quiz_start_time').val().replace('T', ' ')
    quizStartTime =
      quizStartTime === ''
        ? ''
        : new Date(quizStartTime).toISOString().replace('T', ' ').slice(0, 19)
    let quizEndTime = $('#quiz_end_time').val().replace('T', ' ')
    quizEndTime =
      quizEndTime === ''
        ? ''
        : new Date(quizEndTime).toISOString().replace('T', ' ').slice(0, 19)
    // get the number of questions
    const numQuestions = $('.question').length

    if (quizName === '') {
      Swal.fire({
        title: 'Error!',
        text: 'Please fill in all the required fields',
        icon: 'error',
        confirmButtonText: 'Ok',
        timer: 2000
      })
      return
    }

    // create an array to store the questions
    const questions = []
    // loop through the questions
    for (let i = 1; i <= numQuestions; i++) {
      // get the question text
      const questionText = $(`#question_${i}_text`).val()
      // get the question id
      const questionId = $(`#question_${i}_id`).val()
      // get the correct option
      const correctOption = $(`#question_${i}_correct_option`).val()
      const questionGrade = $(`#question_${i}_grade`).val()
      const questionType = $(`#question_${i}_type`).val()
      // get the number of options, we subtract 2 because we have the correct option and the grade
      // under the same form-group class
      const numOptions = $(`#question_${i} .form-group`).length - 2
      // create an array to store the options
      const options = []
      for (let j = 1; j < numOptions; j++) {
        const optionText = $(`#question_${i}_option_${j}`).val()
        const isCorrect = parseInt(correctOption, 10) === j
        options.push({ option_text: optionText, isCorrect })
      }

      const question =
        questionType !== '2'
          ? {
              question_text: questionText,
              id: questionId,
              options,
              type: questionType,
              grade: questionGrade
            }
          : {
              question_text: questionText,
              id: questionId,
              correct_answer: correctOption,
              type: questionType,
              grade: questionGrade
            }
      // add the question to the questions array
      questions.push(question)
    }
    // create the quiz object
    const quiz = {
      name: quizName,
      description: quizDescription,
      active: quizActive,
      time: quizTime,
      visibility: quizVisibility,
      startTime: quizStartTime,
      endTime: quizEndTime,
      questions
    }
    sendData(quiz, subjectId, quizId)
  })
})

function getQuestionNumber () {
  // get the number of questions
  let questionNumber = $('.question').length
  // increment the question number
  questionNumber++
  return questionNumber
}

function getTextQuestionHtml (questionNumber) {
  const additionalHtml = ''
  return getQuestionHtml(questionNumber, 3, additionalHtml)
}

function getMcqQuestionHtml (questionNumber, numOptions) {
  let question = ''
  // create loop to add mcq options
  for (let i = 1; i <= numOptions; i++) {
    question += `
      <div class="form-group">
        <label for="question_${questionNumber}_option_${i}">Option ${i}</label>
        <input type="text" class="form-control" id="question_${questionNumber}_option_${i}" name="question_${questionNumber}_option_${i}">
      </div>
    `
  }

  question += `
        <div class="form-group">
          <label for="question_${questionNumber}_correct_option">Correct Option</label>
          <select class="form-select" id="question_${questionNumber}_correct_option" name="question_${questionNumber}_correct_option">
  `
  // loop through the number of options and create the options to select
  for (let i = 1; i <= numOptions; i++) {
    question += `
            <option value="${i}">Option ${i}</option>
    `
  }
  question += `
      </select>
    </div>
  `

  return getQuestionHtml(questionNumber, 1, question)
}

function getTrueFalseQuestionHtml (questionNumber) {
  const additionalHtml = `
    <div class="form-group">
      <label for="question_${questionNumber}_correct_option">Correct Option</label>
      <select class="form-select" id="question_${questionNumber}_correct_option" name="question_${questionNumber}_correct_option">
        <option value="True">True</option>
        <option value="False">False</option>
      </select>
    </div>
  `
  return getQuestionHtml(questionNumber, 2, additionalHtml)
}

function getQuestionHtml (questionNumber, questionType, additionalHtml) {
  return `
    <div class="card mb-3 question" id="question_${questionNumber}">
      <div class="card-header">Question ${questionNumber}</div>
      <div class="card-body">
        <div class="form-group">
          <label for="question_${questionNumber}_text">Question Text</label>
          <input type="text" class="form-control" id="question_${questionNumber}_text" name="question_${questionNumber}_text">
        </div>
        <input type="hidden" id="question_${questionNumber}_type" name="question_${questionNumber}_type" value="${questionType}">
        ${additionalHtml}
        <div class="form-group">
            <label for="question_${questionNumber}_grade">Grade</label>
            <input type="number" class="form-control" id="question_${questionNumber}_grade" name="question_${questionNumber}_grade">
        </div>
        <button type="button" class="btn btn-danger" onclick="deleteQuestion(${questionNumber})">Delete Question</button>
      </div>
    </div>
  `
}

function addQuestion (type, numOptions = 0) {
  // get the question number
  const questionNumber = getQuestionNumber()

  // get the question html based on the type
  let question = ''
  if (type === 1) {
    question = getMcqQuestionHtml(questionNumber, numOptions)
  } else if (type === 3) {
    question = getTextQuestionHtml(questionNumber)
  } else if (type === 2) {
    question = getTrueFalseQuestionHtml(questionNumber)
  }

  // append the question to the quiz
  $('#quiz-questions').append(question)
}

function deleteQuestion (questionNumber) {
  $(`#question_${questionNumber}`).hide('slow', () => {
    $(`#question_${questionNumber}`).remove()
    $('.question:visible').each((index, question) => {
      const currentId = $(question).attr('id')
      const currentNumber = parseInt(currentId.replace('question_', ''), 10)
      if (currentNumber > questionNumber) {
        // update the question number
        const newNumber = currentNumber - 1
        $(question).find('.card-header').text(`Question ${newNumber}`)
        $(question).attr('id', `question_${newNumber}`)
        $(question)
          .find(`#question_${currentNumber}_text`)
          .attr('name', `question_${newNumber}_text`)
        $(question)
          .find(`#question_${currentNumber}_text`)
          .attr('id', `question_${newNumber}_text`)
        $(question)
          .find(`#question_${currentNumber}_id`)
          .attr('id', `question_${newNumber}_id`)
        // update labels
        $(question)
          .find(`label[for="question_${currentNumber}_text"]`)
          .attr('for', `question_${newNumber}_text`)
        $(question)
          .find(`#question_${currentNumber}_correct_option`)
          .attr('name', `question_${newNumber}_correct_option`)
        $(question)
          .find(`#question_${currentNumber}_correct_option`)
          .attr('id', `question_${newNumber}_correct_option`)
        $(question)
          .find(`label[for="question_${currentNumber}_correct_option"]`)
          .attr('for', `question_${newNumber}_correct_option`)
        // update the option IDs
        const numOptions = $(question).find('.form-group').length - 1
        for (let j = 1; j <= numOptions; j++) {
          $(question)
            .find(`#question_${currentNumber}_option_${j}`)
            .attr('name', `question_${newNumber}_option_${j}`)
          $(question)
            .find(`#question_${currentNumber}_option_${j}`)
            .attr('id', `question_${newNumber}_option_${j}`)
          $(question)
            .find(`label[for="question_${currentNumber}_option_${j}"]`)
            .attr('for', `question_${newNumber}_option_${j}`)
        }
        // update the delete button
        $(question)
          .find('button')
          .attr('onclick', `deleteQuestion(${newNumber})`)
      }
    })
  })
}
