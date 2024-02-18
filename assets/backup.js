$(document).on('change', '#backup_load_user_world_file', function() {
    let fileInput = $('#backup_load_user_world_file'),
        fileSubmit = $('#backup_load_user_world_submit');

    if (fileInput.val() !== null || fileInput.val() !== "" ) {
        fileSubmit.removeAttr('disabled');
    }
})
