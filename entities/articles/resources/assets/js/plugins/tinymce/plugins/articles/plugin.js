import Swal from 'sweetalert2';

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

  function loadWidget() {
    let component = window.Admin.vue.helpers.getVueComponent('articles-package', 'ArticleWidget');

    component.$data.model.id = widgetData.model.id;
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

        window.Admin.vue.helpers.initComponent('articles-package', 'ArticleWidget', widgetData);

        window.waitForElement('#add_article_widget_modal', function() {
          loadWidget();

          $('#add_article_widget_modal').modal();
        });
      } else {
        Swal.fire({
          title: 'Ошибка',
          text: 'Необходимо выбрать виджет-статью',
          icon: 'error',
        });

        return false;
      }
    }
  });
});
