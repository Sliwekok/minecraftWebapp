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

    if (await showAlert('warning', message, 'Be careful!', confirmation)) {
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
});


$(document).on('click', '.connectToServer', function (e) {
    e.preventDefault();
    $(this).attr('title','Copied');
    $(this).hover();
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(this).text()).select();
    document.execCommand("copy");
    $temp.remove();
    setTimeout(() => {
        $(this).attr('title','Click to copy');
    }, 5000);
})
