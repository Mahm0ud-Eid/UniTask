/**
 * Sends a POST request to the server to add students data to the database.
 * @param {Object} jsonData - A JSON object containing student data in the format {name: string, email: string, department: string, semester: string}.
 */
const sendData = (jsonData) => {
  Swal.fire({
    title: 'Processing',
    text: 'Please wait while we process your request',
    icon: 'info',
    showConfirmButton: false,
    allowOutsideClick: false,
    allowEscapeKey: false
  })
  $.ajax({
    url: `${window.btu.api_base}add-students`,
    type: 'POST',
    data: JSON.stringify(jsonData),
    headers: {
      Authorization: window.btu.api_key
    },
    success: (response) => {
      if (response.success) {
        Swal.fire({
          title: 'Success!',
          text: 'Students imported successfully',
          icon: 'success',
          confirmButtonText: 'Ok'
        })
      } else {
        Swal.fire({
          title: 'Error!',
          text: 'There was an error importing students',
          icon: 'error',
          confirmButtonText: 'Ok'
        })
      }
    },
    error: (xhr, status, error) => {
      Swal.fire({
        title: 'Error!',
        text: 'There was an error importing students',
        icon: 'error',
        confirmButtonText: 'Ok'
      })
    }
  })
}

$(document).ready(() => {
  // Toggle import-students-form visibility
  $('#import-students').click(() => {
    $('#import-students-form').toggleClass('d-none')
  })

  // Handle file input change
  $('#import-students-file').change((event) => {
    readXlsxFile(event.target.files[0]).then((data) => {
      // Create options for column selection
      const options = $('#options-import')
      // remove previous options
      options.empty()
      const prompt = $('<p>').text(
        'Select the column that contains the data you want to import'
      )
      options.append(prompt)

      // Create dropdowns for selecting columns for name, email, department, and semester
      const nameSelect = $('<select>')
        .addClass('form-select mb-3 name-select')
        .attr('aria-label', 'Select name column')
      const emailSelect = $('<select>')
        .addClass('form-select mb-3 email-select')
        .attr('aria-label', 'Select email column')
      const deptSelect = $('<select>')
        .addClass('form-select mb-3 dept-select')
        .attr('aria-label', 'Select department column')
      const semSelect = $('<select>')
        .addClass('form-select mb-3 sem-select')
        .attr('aria-label', 'Select semester column')

      // Add default option to each dropdown
      nameSelect.append(
        $('<option>').attr('value', '').text('Select Name column')
      )
      emailSelect.append(
        $('<option>').attr('value', '').text('Select Email column')
      )
      deptSelect.append(
        $('<option>').attr('value', '').text('Select Department column')
      )
      semSelect.append(
        $('<option>').attr('value', '').text('Select Semester column')
      )

      // Add columns to dropdown options
      for (let i = 0; i < data[0].length; i++) {
        const option = $('<option>').attr('value', i).text(data[0][i])
        nameSelect.append(option.clone())
        emailSelect.append(option.clone())
        deptSelect.append(option.clone())
        semSelect.append(option.clone())
      }

      // Append dropdowns to options container
      options.append(nameSelect, emailSelect, deptSelect, semSelect)

      // Handle column selection and form submission
      const headers = data[0]
      const jsonData = []

      $('#import-students-submit').click(() => {
        const nameColumn = parseInt(nameSelect.val(), 10)
        const emailColumn = parseInt(emailSelect.val(), 10)
        const deptColumn = parseInt(deptSelect.val(), 10)
        const semColumn = parseInt(semSelect.val(), 10)

        if (
          isNaN(nameColumn) ||
          isNaN(emailColumn) ||
          isNaN(deptColumn) ||
          isNaN(semColumn)
        ) {
          Swal.fire({
            title: 'Error!',
            text: 'Please select a column for each field',
            icon: 'error',
            confirmButtonText: 'Ok',
            timer: 3000
          })
          return
        }

        $.each(data, (i, row) => {
          if (i > 0) {
            const temp = {
              name: row[nameColumn],
              email: row[emailColumn],
              department: row[deptColumn],
              semester: row[semColumn]
            }

            jsonData.push(temp)
          }
        })
        sendData(jsonData)
      })
    })
  })
})
