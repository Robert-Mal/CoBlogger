$(document).ready(() => {
    const postLike = $('.post-like');
    const postLikeCounter = $('.post-like-counter');

    postLike.on('click', () => {
        const postId = postLike.attr('data-post');
        $.ajax({
            url: '/dashboard/post/like/' + postId,
            type: 'GET',
            dataType: 'json',
            async: true,
            success: (data) => {
                postLikeCounter.text(data.likeCount);
            },
            error: (xhr, textStatus, errorThrown) => {
                console.log(xhr, textStatus, errorThrown);
            }
        })
    })
})