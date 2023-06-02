$(document).ready(() => {
    $('#togglePassword').on('click', () => {
        const passwordInput = $('#inputPassword').get(0);
        const showPassword = $('#showPassword');
        const hidePassword = $('#hidePassword');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            showPassword.addClass('hidden');
            hidePassword.removeClass('hidden');
        } else {
            passwordInput.type = 'password';
            showPassword.removeClass('hidden');
            hidePassword.addClass('hidden');
        }
    });


    $('#togglePlainPassword').on('click', () => {
        const togglePlainPassword = $('#registration_form_plainPassword').get(0);
        const showPassword = $('#showPassword');
        const hidePassword = $('#hidePassword');
        if (togglePlainPassword.type === 'password') {
            togglePlainPassword.type = 'text';
            showPassword.addClass('hidden');
            hidePassword.removeClass('hidden');
        } else {
            togglePlainPassword.type = 'password';
            showPassword.removeClass('hidden');
            hidePassword.addClass('hidden');
        }
    });
});

