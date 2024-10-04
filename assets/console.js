import * as Alert from "./alert";

// scroll to the bottom of textarea
$(document).ready(function() {
    let textarea = document.getElementById('commandHistory')
    if (textarea !== null) {
        textarea.scrollTop = textarea.scrollHeight;
    }
})

$(document).on('submit', '#consoleCommandForm', function (e) {
    e.preventDefault();
    let url = $(this).data('url'),
        input = $('#consoleCommandInput'),
        command = input.val()
    ;

    input.prop('disabled', true);

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        data: {
            'command': command
        },
        beforeSend: function () {
            Alert.hideAlertMethod();
        },
        success: function (message) {
            input.val('');
            // give time to register change in log file
                input.prop('disabled', false);
            setTimeout(function() {
                Alert.showAlert(
                    'success',
                    message,
                    'Success'
                );
                refreshContainerContent(location.href, 'commandHistoryContainer');
            }, 2500)
        },
        error: function(message) {
            input.val('');
            // give time to register change in log file
                input.prop('disabled', false);
            setTimeout(function() {
                Alert.showAlert(
                    'danger',
                    message.responseJSON,
                    'Oops! Something went wrong'
                );
                refreshContainerContent(location.href, 'commandHistoryContainer');

            }, 2500)
        }

    });
})

function refreshContainerContent(url, div){
    $.ajax({
        url: url,
        method: 'get',
        error: function(error){
            console.log("=========");
            console.log(error);

            return false;
        },
        success: function(){
            $(`#${div}`)
                .html('<textarea id="commandHistory" disabled>\n' +
                    '                        </textarea>')
                .load(url+" #"+div, function () {
                    let textarea = document.getElementById('commandHistory')
                    textarea.scrollTop = textarea.scrollHeight;
                })
            ;
            return true;
        },
    });
}
