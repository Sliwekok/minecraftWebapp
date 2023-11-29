import {showAlert} from "./alert";

$(document).on('click', ".confirmation", async function () {
    let div = $(this),
        url = div.data('url'),
        message = div.data('message'),
        confirmation = div.data('confirmation')
    ;

    if (confirmation) {
        if (await showAlert('warning', message, 'Be careful!', true)) {
                $.ajax({
                    url: url,
                    method: 'get',
                    error: function(message) {
                        showAlert(
                            'danger',
                            message.responseJSON,
                            'Oops! Something went wrong'
                        );

                        return false;
                    },

                    success: function(message) {
                        showAlert(
                            'success',
                            message.responseJSON,
                            'Success'
                        );

                        return true;
                    }
                });
        }
    }
});
