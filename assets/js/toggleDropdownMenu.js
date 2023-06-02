$(document).ready(() => {
    const toggleDropdown = $('#toggleDropdown');
    const dropdownMenu = $('#dropdownMenu');

    toggleDropdown.on('click', (e) => {
        e.stopPropagation();
        dropdownMenu.toggleClass('hidden');
    });

    $(window).on('click', (e) => {
        dropdownMenu.addClass('hidden');
    });
})
