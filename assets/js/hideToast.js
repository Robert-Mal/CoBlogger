$(document).ready(() => {
    const toast = $('#toast');
    if (toast) {
        setTimeout(() => {
            toast.remove();
        }, 2000);
    }
})