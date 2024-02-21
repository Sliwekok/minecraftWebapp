// show alert with return message from server
export function showAlert(status, message, header, alertConfirmation, isTemporary = false) {
    switch (status) {
        case 'success':
            var className = 'primary';
            break;
        case 'error':
            var className = 'danger';
            break;
        case 'warning':
            var className = 'warning';
            break;
        default:
            var className = 'danger';
    }

    let alert = $(".alert"),
        alertHeader = $("#alertHeader"),
        alertContent = $("#alertContent"),
        alertConfirmationDiv = $('#alertConfirmation')
    ;

    alert.addClass('alert-'+className).fadeIn(100);
    alertHeader.text(header);
    alertContent.text(message);

    if (isTemporary) {
        setTimeout(function() {
            hideAlertMethod();
        }, 15000)
    }

    if (alertConfirmation) {
        alertConfirmationDiv.show(0);
    }

    function hideAlertMethod() {
        alert.fadeOut(100);
        setTimeout(function() {
            alert.removeClass("alert-success alert-danger alert-warning");
            alertConfirmationDiv.hide(0);
            alertHeader.text('');
            alertContent.text('');
        }, 100);
    }

    return new Promise((resolve) => {
        if (alertConfirmation === false) {
            resolve(true);
        }
        $(document).on('click', ".btn-close, #alertNo", function() {
            hideAlertMethod();

            resolve(false);
        });

        $(document).on('click', "#alertYes", function() {
            hideAlertMethod();

            resolve(true);
        });
    });
}
