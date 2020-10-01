$(document).ready(function () {
    if ($('form[name="user"]').length) {
        if ($('#user_username').val() !== '') {
            let plainPassword_first = $('#user_plainPassword_first');
            let plainPassword_second = $('#user_plainPassword_second');

            plainPassword_first.removeAttr('required');
            plainPassword_first.prev().removeClass('required');
            plainPassword_second.removeAttr('required');
            plainPassword_second.prev().removeClass('required');
        }
    }
});