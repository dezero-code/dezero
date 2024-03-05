(function(document, window, $) {
  
  // SlideTable global object
  $.dzSlideTable = {
  
    // Reload table/list via AJAX
    // -------------------------------------------------------------------------------------------
    reload: function(id, settings) {
      var $base = $('#'+ id);

      if ( $base.size() > 0 ) {
        // Custom event "beforeAjaxUpdate"
        if (settings.beforeAjaxUpdate !== undefined) {
          settings.beforeAjaxUpdate(id);
        }

         // Show LOADING
        $base.removeClass('loading').addClass('loading');

        // URL defined as input param?
        if (settings.url === undefined) {
          settings.url = $base.data('url');
        }

        // Ajax callback
        $.ajax({
          url: settings.url,
          type: 'GET',
          cache: false,
          success: function(data) {
            $base.html(data);

            // HIDE loading
            $base.removeClass('loading');

            // Custom event "afterAjaxUpdate"
            if (settings.afterAjaxUpdate !== undefined) {
              settings.afterAjaxUpdate(id, data);
            }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            // HIDE loading
            $base.removeClass('loading');

            alert('Unable to reload list');
          }
        });
      }
    },

    // Delete action
    // -------------------------------------------------------------------------------------------
    delete: function(link, settings) {
      // Action delete
      settings.action = 'delete';

      // Custom event "afterDelete"
      if (settings.afterDelete !== undefined) {
        settings.afterAction = settings.afterDelete;
      }
      
      return $.dzSlideTable.action(link, settings);
    },

    // Run an action
    // -------------------------------------------------------------------------------------------
    action: function(link, settings) {
      $link = $(link);
      if ( settings.confirmMessage === undefined ) {
        settings.confirmMessage = '<h3>Are your sure you want to <span class="text-danger">DELETE</span> it?</h3><p><strong>WARNING:</strong> It will be removed permanently on the platform.</p>';
      }

      if ( settings.successMessage === undefined ) {
        settings.successMessage = 'Removed successfully';
      }

      if ( settings.action === undefined ) {
        settings.action = 'delete';
      }

      bootbox.confirm(
        settings.confirmMessage,
        function(confirmed){
          if ( confirmed ) {
            $.ajax({
              url: $link.attr('href'),
              type: 'POST',
              cache: false,
              contentType: "application/json; charset=utf-8",
              dataType: "json",
              data: settings.ajaxData !== undefined ? settings.ajaxData : {},
              success: function(data) {
                if ( data.error_code == 0 ) {
                  // Show success message
                  $.pnotify({
                    sticker: false,
                    text: settings.successMessage,
                    type: 'success'
                  });

                  // Custom event "afterAction"
                  if (settings.afterAction !== undefined) {
                    settings.afterAction(data);
                  }
                }
                else {
                  alert('ERROR '+ data.error_code +': '+ data.error_msg);
                }
              },
              error: function(xhr, ajaxOptions, thrownError) {
                alert('Unable to perfom '+ settings.action +' action');
              }
            });
          }
        }
      );
    }
  };
})(document, window, jQuery);