import * as Alert from "./alert";
import {showAlert} from "./alert";

// add players to whitelist
$(document).on('submit', '.playersWhitelistFormAdd', function (e) {
    e.preventDefault();
    callToPlayerEndpoint($(this));
})
// remove player from whitelist
$(document).on('click', ".whitelistRemoveFormSubmit", function (e) {
    $('.playersWhitelistFormRemove').on('submit', function (e) {
        e.preventDefault();
        callToPlayerEndpoint($(this))
    })

    $('.playersWhitelistFormRemove').submit;
})

// add players to op list
$(document).on('submit', '#playersOPFormAdd', function (e) {
    e.preventDefault();
    callToPlayerEndpoint($(this));
})
// add players to op list from input
$(document).on('click', ".OPAddFormSubmit", function (e) {
    $('.playersOPFormAdd').on('submit', function (e) {
        e.preventDefault();
        callToPlayerEndpoint($(this))
    })

    $('.playersOPFormAdd').submit;
})
// remove player from op list
$(document).on('click', ".OPRemoveFormSubmit", function (e) {
    $('.playersOPFormRemove').on('submit', function (e) {
        e.preventDefault();
        callToPlayerEndpoint($(this))
    })

    $('.playersOPFormRemove').submit;
})

// add players to blacklist
$(document).on('submit', '.playersBlacklistFormAdd', function (e) {
    e.preventDefault();
    callToPlayerEndpoint($(this));
})

// add players to blacklist from input
$(document).on("click", ".blacklistAddFormSubmit", function (e) {
    $('.playersBlacklistFormAdd').on('submit', function (e) {
        e.preventDefault();
        callToPlayerEndpoint($(this))
    })

    $('.playersBlacklistFormAdd').submit;
})

// remove player from blacklist
$(document).on('click', ".blacklistRemoveFormSubmit", function (e) {
    $('.playersBlacklistFormRemove').on('submit', function (e) {
        e.preventDefault();
        callToPlayerEndpoint($(this))
    })

    $('.playersBlacklistFormRemove').submit;
})


function callToPlayerEndpoint (form) {
    if ($.active > 0) {
        showAlert(
            'error',
            'Wait for previous call to finish',
            'Too many requests',
            false,
            true
        );

        return;
    }

    let url = form.data('url'),
        input = form.find(".playerDataValue"),
        players = input.val()
    ;

    input.prop('disabled', true);

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        data: {
            'players': players
        },
        success: function (message) {
            window.location.href = '/players/list';
        },
        error: function(message) {
            input.val('');
            // give time to register change in log file
            input.prop('disabled', false);
            Alert.showAlert(
                'danger',
                message.responseJSON,
                'Oops! Something went wrong'
            );
        }

    });
}
