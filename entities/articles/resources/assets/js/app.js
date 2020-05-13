require('./plugins/tinymce/plugins/articles');

require('../../../../../../widgets/entities/widgets/resources/assets/js/mixins/widget');

require('./stores/articles');

Vue.component(
    'ArticleWidget',
    require('./components/partials/ArticleWidget/ArticleWidget.vue').default,
);

let articles = require('./package/articles');
articles.init();
