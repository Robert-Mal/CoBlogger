$(document).ready(() => {
    const fileUpload = $('.file-upload');
    const imageContainer = $('.image-container');
    const imagePreview = $('.image-preview');

    fileUpload.change((e) => {
        const file = e.target.files[0];
        if (file) {
            imagePreview.attr('src', URL.createObjectURL(file));
            imageContainer.removeClass('hidden');
        }
    });

    $('#removeImage').click((e) => {
        fileUpload.val('');
        imageContainer.addClass('hidden');
        imagePreview.attr('src', '');
    })
})