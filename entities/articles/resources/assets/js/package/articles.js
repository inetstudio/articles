let articles = {};

articles.init = function () {
    $('#choose_article_modal').on('hidden.bs.modal', function (e) {
        let modal = $(this);

        modal.find('.choose-data').val('');
        modal.find('input[name=article]').val('');
    })
};

module.exports = articles;
