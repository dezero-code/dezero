(function(document, window, $) {

  // Export global object for Dezero Framework
  // ----------------------------------------------------
  $.dezeroExport = {
    $items: null,

    // Init the export to Excel process
    // ----------------------------------------------------
    toExcel: function($items) {
      var self = this;

      self.$items = $items;
      self.exportExcel();
    },

    // Export results to Excel
    // ----------------------------------------------------
    exportExcel: function() {
      var self = this;

      self.$items.each(function(){
        $(this).off('click').on('click', function(e) {
          e.preventDefault();
          var $this = $(this);

          self.ensureExportIframe();

          var $export = $('<input/>', {'name': 'export', 'value': 1, 'type': 'hidden'}),
          $csrf = $('<input/>', {
            'name': window.yii.getCsrfParam() || '_csrf',
            'value': window.yii.getCsrfToken(),
            'type': 'hidden'
          });

          $('<form/>', {
            'action': $this.attr('href'),
            'target': self.targetIframe(),
            'method': 'post',
            css: {'display': 'none'}
          })
          .append($export, $csrf)
          .appendTo('body')
          .submit()
          .remove();
        });
      });
    },

    targetIframe: function() {
      return 'export-excel-iframe';
    },

    ensureExportIframe: function() {
      var target_id = this.targetIframe();
      var $iframe = $('iframe[name="' + target_id +'"]');
      if ( $iframe.length ) {
        return $iframe;
      }

      return $('<iframe/>', {name: target_id, css: {'display': 'none'}}).appendTo('body');
    }
  };
})(document, window, jQuery);
