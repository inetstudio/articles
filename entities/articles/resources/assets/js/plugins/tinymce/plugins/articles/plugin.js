window.tinymce.PluginManager.add('articles', function(editor) {
  let widgetData = {
    widget: {
      events: {
        widgetSaved: function(model) {
          editor.execCommand(
              'mceReplaceContent',
              false,
              '<img class="content-widget" data-type="article" data-id="' + model.id + '" alt="Виджет-статья: '+model.additional_info.title+'" />',
          );
        },
      },
    },
  };

  function initFormsComponents() {
    if (typeof window.Admin.vue.modulesComponents.$refs['articles-package_ArticleWidget'] == 'undefined') {
      window.Admin.vue.modulesComponents.modules['articles-package'].components = _.union(
          window.Admin.vue.modulesComponents.modules['articles-package'].components, [
            {
              name: 'ArticleWidget',
              data: widgetData,
            },
          ]);
    } else {
      let component = window.Admin.vue.modulesComponents.$refs['articles-package_ArticleWidget'][0];

      component.$data.model.id = widgetData.model.id;
    }
  }
  
  editor.addButton('add_article_widget', {
    title: 'Статьи',
    icon: 'fa fa-file-alt',
    onclick: function() {
      editor.focus();

      let content = editor.selection.getContent();
      let isArticle = /<img class="content-widget".+data-type="article".+>/g.test(content);

      if (content === '' || isArticle) {
        widgetData.model = {
          id: parseInt($(content).attr('data-id')) || 0,
        };

        initFormsComponents();

        window.waitForElement('#add_article_widget_modal', function() {
          $('#add_article_widget_modal').modal();
        });
      } else {
        swal({
          title: 'Ошибка',
          text: 'Необходимо выбрать виджет-статью',
          type: 'error',
        });

        return false;
      }
    }
  });
});
