import {showAlert} from "./alert";
import {updateButton} from "./updateButton";

$(document).on('click', ".confirmation", async function () {
    let div = $(this),
        url = div.data('url'),
        message = div.data('message'),
        confirmation = div.data('confirmation')
    ;

    if (div.hasClass('active')) {
        return;
    }

    if (confirmation) {
        if (await showAlert('warning', message, 'Be careful!', true)) {
                $.ajax({
                    url: url,
                    method: 'get',
                    error: function(message) {
                        showAlert(
                            'danger',
                            message.statusText,
                            'Oops! Something went wrong'
                        );

                        return false;
                    },

                    success: function(message) {
                        showAlert(
                            'success',
                            message,
                            'Success'
                        );

                        updateButton(div)

                        return true;
                    }
                });
        }
    }
});
