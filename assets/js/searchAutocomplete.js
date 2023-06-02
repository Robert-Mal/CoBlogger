$(document).ready(() => {
    const searchInput = $('.search-input');
    const results = $('.results');

    searchInput.on('keyup', () => {
        if (!results.is(':contains("Searching...")')) {
            results.empty();
            results.append('<p class="searching flex px-3 py-2 mx-auto">Searching...</p>');
        }
    });

    searchInput.on('keyup', debounce(() => getResults(searchInput.val())));

    searchInput.on('click', (e) => {
        e.stopPropagation();
        results.removeClass('hidden');
    });

    $(window).on('click', () => {
        results.addClass('hidden');
    })

    const getResults = (query) => {
        $.ajax({
            url: '/search',
            type: 'GET',
            dataType: 'json',
            data: {
                q: query
            },
            success: (data) => {
                results.empty();
                if (data.length === 0) {
                    results.append("<p class='result flex px-3 py-2 mx-auto'>No result</p>");
                } else {
                    for (const i in data) {
                        results.append("<a href='/dashboard/post/" + i + "' class='result px-3 py-2 hover:bg-gray-200'>" + data[i] + "</a>");
                    }
                }
            },
            error: (xhr, textStatus, errorThrown) => {
                console.log(xhr, textStatus, errorThrown);
            }
        })
    };
})

const debounce = (func, timeout = 300) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            func.apply(this, args);
        }, timeout);
    };
};