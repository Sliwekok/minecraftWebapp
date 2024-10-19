import {showAlert} from "./alert";
import {updateButton} from "./updateButton";

$(document).on("change", ".serverChangeType", async function() {
    if ( await showAlert(
        'warning',
        'Are you sure you want to change server type to: ' + $(this).val() + "?",
        'Changing server type',
        true,
        false
            )
    ) {
        let url = $(this).data('url'),
            newType = $(this).val()
        ;

        $.ajax({
            url: url,
            method: 'get',
            data: {
                'gameType': newType,
            },
            error: function(message) {
                showAlert(
                    'danger',
                    message.responseText,
                    'Oops! Something went wrong'
                );

                $(".serverChangeType option")
                    .removeAttr('selected')
                    .removeAttr('disabled')
                ;

                $(".serverChangeType option[value='"+oldType+"']")
                    .attr('selected', 'selected')
                    .attr('disabled', 'disabled')
                ;

                return false;
            },

            success: function(message) {
                showAlert(
                    'success',
                    message,
                    'Success'
                );

                $(".serverChangeType option")
                    .removeAttr('selected')
                    .removeAttr('disabled')
                ;

                $(".serverChangeType option[value='"+newType+"']")
                    .attr('selected', 'selected')
                    .attr('disabled', 'disabled')
                ;

                return true;
            }
        });
    }
})

$(document).on('change', '#mods_load_custom_mods_form', function() {
    let fileInput = $('#mods_load_custom_mods_form_files'),
        fileSubmit = $('#mods_load_custom_mods_form_submit');

    if (fileInput.val() !== null || fileInput.val() !== "" ) {
        fileSubmit.removeAttr('disabled');
    }
})

$(document).ready(function() {
    let modsLoaded =  $('.modPanel').length + $('.paginator').data('index');
    $('.loadedModsOnBrowse').html(modsLoaded);
});

$(document).on('click', '.modAction', function() {
    let button = $(this),
        modId = button.data('mod-id'),
        url = button.data('url'),
        action = button.data('action')
    ;

    $.ajax({
        url: url,
        method: 'get',
        data: {
            'id': modId
        },
        error: function(message) {
            showAlert(
                'danger',
                message.responseText,
                'Oops! Something went wrong'
            );

            return false;
        },

        success: function(message) {
            if (action === 'delete') {
                button.parent().parent().remove()
            } else {
                button.text('Installed')
                    .attr('disabled', 'disabled');
            }
            showAlert(
                'success',
                message,
                'Success'
            );

            return true;
        }
    });
});
