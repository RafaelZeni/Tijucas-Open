
window.addEventListener('DOMContentLoaded', () => {
    if (typeof sweetAlertData !== 'undefined' && sweetAlertData.icon && sweetAlertData.title && sweetAlertData.text) {
        Swal.fire({
            icon: sweetAlertData.icon,
            title: sweetAlertData.title,
            text: sweetAlertData.text
        }).then(() => {
            if (sweetAlertData.redirect) {
                window.location.href = sweetAlertData.redirect;
            }
        });
    }
});

