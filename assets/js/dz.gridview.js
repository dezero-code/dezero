(function(document, window, $) {
  
  // DzGridView global object
  $.dzGridView = {

    // Before grid update
    beforeAjaxUpdate: function(id, options) {
      // Scroll top
      $("html, body").animate({scrollTop: 0}, 100);

      // Add search form fields
      if ( $('#'+ id +'-search-form').size() > 0 ) {
        options.url += "&"+$('#'+ id +'-search-form').serialize();
      }
    },

    // Clear search fields
    clearFields: function(grid_id) {
      $("#cbcwr_clear").tooltip('hide');
      try
      {
        $('#'+ grid_id +' :input').clearFields(); // this will clear all input in the current grid
        $('#'+ grid_id +'-search-form :input').clearFields(); // this will clear all input in the search form grid
        $('#'+ grid_id +' :input').first().trigger('change'); // to submit the form
        return false;
      }
      catch(cbwr_err)
      {
          return false;
      }
    }
  };

  // GRIDVIEW - CLEAR BUTTON
  $.fn.clearFields = $.fn.clearInputs = function() {
    return this.each(function() {
        var t = this.type, tag = this.tagName.toLowerCase();
        if (t == 'text' || t == 'password' || tag == 'textarea') {
            this.value = '';
        }
        else if (t == 'checkbox' || t == 'radio') {
            this.checked = false;
        }
        else if (tag == 'select') {
            this.selectedIndex = -1;
        }
    });
  };

  // GRIDVIEW - SEARCH BUTTON
  $.fn.dzGridSearch = function(options) {
    function init() {
      if ( ! $.isEmptyObject(options) ) {
        $.extend(settings, options);
      }

      // Submit form
      $base.on('submit', function(e){
        $.fn.yiiGridView.update($base.data('grid'));
        /*
        $.fn.yiiGridView.update($base.data('grid'), {
          data: $('#'+ $base.attr('id') +', #'+ $base.data('grid') +'-filters :input').serialize()
        });
        */
        return false;
      });
    }

    var $base = $(this);
    
    if ($(this).size() > 0) {
      init();
    }
  };
})(document, window, jQuery);