import {showAlert} from "./alert";
$(document).on('click', "#userDeleteAccount", async function () {
    let div = $(this),
        url = div.data('url'),
        message = div.data('message'),
        confirmation = div.data('confirmation')
    ;
    if (await showAlert('danger', message, 'Do you want to delete account?', confirmation)) {
        window.location.replace(url);
    }
})
