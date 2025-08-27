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
 * @param {int} type - The URL to send the data to.
 * @param {number} questionId - The ID of the question
 * @param {number} attemptId - The ID of the answer
 * @returns {void}
 */
const sendData = (type, jsonData, questionId, attemptId) => {
  let url = `${window.btu.api_base}result/${attemptId}/mark-as-incorrect/${questionId}`
  if (type === 1) {
    url = `${window.btu.api_base}result/${attemptId}/mark-as-correct/${questionId}`
  }
  $.ajax({
    url,
    type: 'POST',
    data: JSON.stringify(jsonData),
    headers: {
      Authorization: window.btu.api_key
    },
    contentType: 'application/json',
    success: (data) => {
      if (data.success) {
        alert('Success!', 'Question marked as correct', 'success')
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
  $(document.body).on('click', '.mark-as-correct', function () {
    // variable for the parent div, it will have id like mark-buttons-16
    const parentDiv = $(this).closest("div[id^='mark-buttons-']")
    const button = $(this)
    const gradeForm = parentDiv.find('#add-grade-form')
    const removeButton = parentDiv.find('.mark-as-incorrect')

    button.hide()
    gradeForm.slideDown()
  })

  $(document.body).on('click', '.cancel-add-grade-button', function () {
    const button = $(this)
    const gradeForm = button.parents('#add-grade-form')
    const addButton = button.parents().find('.mark-as-correct')

    gradeForm.slideUp(() => {
      addButton.show()
    })
  })

  $(document.body).on('click', '.save-add-grade-button', function () {
    const button = $(this)
    const gradeForm = button.parents('#add-grade-form')
    const addButton = button.parents().find('.mark-as-correct')
    const questionId = button.data('question-id')
    const attemptId = button.data('attempt-id')
    const grade = gradeForm.find('#add-grade').val()

    const jsonData = {
      grade
    }

    sendData(1, jsonData, questionId, attemptId)
    button
      .closest('.card')
      .removeClass('border-danger')
      .addClass('border-success')
    gradeForm.slideUp(() => {
      addButton
        .removeClass('btn-success')
        .addClass('btn-danger')
        .removeClass('mark-as-correct')
        .addClass('mark-as-incorrect')
        .attr('data-value', 'false')
        .text('Mark as Wrong')
      addButton.show()
    })
  })

  $(document.body).on('click', '.mark-as-incorrect', function () {
    const button = $(this)
    const questionId = button.data('question-id')
    const attemptId = button.data('attempt-id')

    sendData(0, null, questionId, attemptId)
    button
      .closest('.card')
      .removeClass('border-success')
      .addClass('border-danger')
    button
      .removeClass('btn-danger')
      .addClass('btn-success')
      .removeClass('mark-as-incorrect')
      .addClass('mark-as-correct')
      .attr('data-value', 'true')
      .text('Mark as Correct')
  })
  // mark-as-reviewed
  $(document.body).on('click', '.mark-as-reviewed', function () {
    const button = $(this)
    const attemptId = button.data('attempt-id')
    $.get({
      url: `${window.btu.api_base}result/${attemptId}/mark-as-reviewed`,
      headers: {
        Authorization: window.btu.api_key
      },
      contentType: 'application/json',
      success: (data) => {
        if (data.success) {
          alert('Success!', 'Quiz marked as reviewed', 'success')
        } else {
          alert('Error!', data.error, 'error')
        }
      }
    })
  })

  $('.cancel-change-grade-button').on('click', () => {
    $('#changeGradeForm').collapse('hide')
  })

  $('.save-change-grade-button').on('click', function () {
    const button = $(this)
    const attemptId = button.data('attempt-id')
    const grade = $('#changeGradeForm').find('#change-grade').val()
    $.ajax({
      url: `${window.btu.api_base}result/${attemptId}/change-grade`,
      type: 'POST',
      data: JSON.stringify({ grade }),
      headers: {
        Authorization: window.btu.api_key
      },
      contentType: 'application/json',
      success: (data) => {
        if (data.success) {
          alert('Success!', 'Grade changed', 'success')
        } else {
          alert('Error!', data.error, 'error')
        }
      }
    })
  })
})
