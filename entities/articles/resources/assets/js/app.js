require('./plugins/tinymce/plugins/articles');

require('../../../../../../widgets/entities/widgets/resources/assets/js/mixins/widget');

require('./stores/articles');

window.Vue.component(
    'ArticleWidget',
    () => import('./components/partials/ArticleWidget/ArticleWidget.vue'),
);

let articles = require('./package/articles');
articles.init();
