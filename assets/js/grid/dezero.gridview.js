(function(document, window, $) {
  $.dezeroGridview = {
    $grid: null,
    $pjax: null,

    init: function(grid, hash) {
      var self = this;

      self.$grid = $('#'+ grid);

      if ( $.pjax) {
        self.$pjax = self.$grid.parents('[data-pjax-container]').first();

        if ( self.$pjax.length ) {
          self.$pjax
            .off('pjax:complete.' + hash)
            .on('pjax:complete.' + hash, function () {
              self.$grid.find('[data-toggle="tooltip"]').tooltip('hide');
              self.$grid.find('[data-toggle="tooltip"]').tooltip();
              console.log("tooltip enabled");

              self.$grid.find('table thead select').select2({allowClear: false, placeholder: '- All -'});
            });
        }
      }

      self.$grid.find('table thead select').select2({allowClear: false, placeholder: '- All -'});
    }
  };
})(document, window, jQuery);
