import {showAlert} from "./alert";

$(document).on('click', ".download", async function() {
    let div = $(this),
        url = div.data('url'),
        message = div.data('message'),
        filename = div.data('filename');

    $.ajax({
        url: url,
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        beforeSend: function () {
            showAlert('success', message, 'Success');
        },
        success: function (data) {
            var a = document.createElement('a'),
                url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = filename + '.zip';
            document.body.append(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        },
        error: function(message) {
            showAlert(
                'danger',
                message.statusText,
                'Oops! Something went wrong'
            );
        }

        });
});
