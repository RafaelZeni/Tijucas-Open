if (typeof sweetAlertData !== 'undefined' && sweetAlertData.icon && sweetAlertData.title && sweetAlertData.text) {
    let swalInstance = Swal.fire({
        icon: sweetAlertData.icon,
        title: sweetAlertData.title,
        text: sweetAlertData.text
    });

    if (sweetAlertData.redirect) {
        swalInstance.then(() => {
            window.location.href = sweetAlertData.redirect;
        });
    }
}
