document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.btn-excluir').forEach(button => {
    button.addEventListener('click', function (event) {
      event.preventDefault();

      const sweetAlertText = this.getAttribute('data-text') || "Deseja excluir este item?";

      Swal.fire({
        title: 'Tem certeza?',
        text: sweetAlertText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = this.href;
        }
      });
    });
  });
});
