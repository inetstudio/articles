let articles = {};

articles.init = function () {
    if (!window.Admin.vue.modulesComponents.modules.hasOwnProperty('articles-package')) {
        window.Admin.vue.modulesComponents.modules = Object.assign(
            {}, window.Admin.vue.modulesComponents.modules, {
                'articles-package': {
                    components: [],
                },
            });
    }
};

module.exports = articles;
