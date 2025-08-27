$(document).ready(() => {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  )
  const tooltipList = tooltipTriggerList.map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
  )

  $('#easter').click(() => {
    Swal.fire({
      title: 'YOU FOUND THE EASTER EGG!',
      text: "You are an amazing person! that's all.",
      width: 600,
      padding: '3em',
      color: '#716add',
      background: '#fff url(https://sweetalert2.github.io/images/trees.png)',
      backdrop: `
    rgba(0,0,123,0.4)
    url("https://sweetalert2.github.io/images/nyan-cat.gif")
    left top
    no-repeat
  `
    })
  })
})
