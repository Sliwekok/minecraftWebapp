export function updateButton (button) {
    button.addClass('active');
    button.siblings('.confirmation').removeClass('active');
}
