
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

$(document).on('click', '.phone-button', function () {
    let button = $(this);
    axios.post(button.data('source')).then(function (response) {
        button.find('.number').html(response.data)
    }).catch(function (error) {
        console.error(error);
    });
});

$('.banner').each(function () {
    let block = $(this);
    let url = block.data('url');
    let format = block.data('format');
    let category = block.data('category');
    let region = block.data('region');

    axios.get(url, {
        params: {
                format: format,
                category: category,
                region: region
            }})
        .then(function (response) {
            block.html(response.data);
        })
        .catch(function (error) {
            console.error(error);
        });
});
