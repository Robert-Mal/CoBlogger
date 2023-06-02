$(document).ready(() => {
    const followButton = $('.follow-button');
    const userId = followButton.attr('data-user');

    followButton.on('click', () => follow(userId));

    const follow = (userId) => {
        $.ajax({
            url: '/dashboard/profile/' + userId + '/follow',
            type: 'GET',
            dataType: 'json',
            success: (data) => {
                if (data.isFollowed) {
                    followButton.text('Unfollow');
                } else {
                    followButton.text('Follow');
                }
            },
            error: (xhr, textStatus, errorThrown) => {
                console.log(xhr, textStatus, errorThrown);
            }
        })
    };
});