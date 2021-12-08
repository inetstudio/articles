import hash from 'object-hash';
import { v4 as uuidv4 } from 'uuid';

window.Admin.vue.stores['articles-package_articles'] = new window.Vuex.Store({
  state: {
    emptyArticle: {
      model: {
        title: '',
        slug: '',
        description: '',
        content: '',
        setka_content: '',
        publish_date: null,
        webmaster_id: '',
        status_id: 0,
        created_at: null,
        updated_at: null,
        deleted_at: null,
      },
      isModified: false,
      hash: '',
    },
    article: {},
    mode: '',
  },
  mutations: {
    setArticle(state, article) {
      let emptyArticle = JSON.parse(JSON.stringify(state.emptyArticle));
      emptyArticle.model.id = uuidv4();

      let resultArticle = _.merge(emptyArticle, article);
      resultArticle.hash = hash(resultArticle.model);

      state.article = resultArticle;
    },
    setMode(state, mode) {
      state.mode = mode;
    },
  },
});
