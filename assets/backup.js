$(document).on('change', '#backup_load_user_world_file', function() {
    let fileInput = $('#backup_load_user_world_file'),
        // we need to find label since it's styled, not input
        fileLabel = fileInput.siblings('label');

    if (fileInput.val() !== null || fileInput.val() !== "" ) {
        fileLabel.css("background-color: red");
        console.log('test');
    }
})
