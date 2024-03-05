(function(document, window, $) {
  'use strict';

  // Status change button ===========================================================
  $.fn.dzStatusButton = function() {
    function init() {
      $base.on('click', function(e){
        e.preventDefault();
        var $link = $(this);
        bootbox.confirm(
          $link.data('dialog'),
          function(confirmed){
            if ( confirmed ) {
              $('#status-change').val($link.data('value'));
              $('#'+ $link.data('form-submit')).submit();
            }
          }
        );
      });
    }

    var $base = $(this);
    if ($(this).size() > 0) {
      init();
    }
  };

  // Loader global object for Dezero Framework
  // ----------------------------------------------------
  $.dezeroLoader = {
    dialog: null,

    show: function(loading_message) {
      this.dialog = bootbox.dialog({
        message: loading_message
      });
    },

    hide: function() {
      this.dialog.modal('hide');
    }
  };

  // Document ready ===========================================================
  var Site = window.Site;
  $(document).ready(function() {
    Site.run();

    // AJAX Session Timeout - DZ_LOGIN_REQUIRED
    $(document).ajaxComplete(
      function(event, request, options) {
        if (request.responseText == "DZ_LOGIN_REQUIRED") {
          window.location.href = window.js_globals.baseUrl +'/user/login';
        }
      }
    );

    if ( typeof(yii) !== 'undefined' ) {
      // Confirm via Bootbox
      // --- Delete action (bootbox) ---
      yii.confirm = function (message, ok, cancel) {
        bootbox.confirm(
          {
            message: `<h3>${message}</h3>`,
            buttons: {
              confirm: {
                label: "Continue"
              },
              cancel: {
                label: "Cancel"
              }
            },
            callback: function (confirmed) {
              if (confirmed) {
                !ok || ok();
              } else {
                !cancel || cancel();
              }
            }
          }
        );
        // confirm will always return false on the first call
        // to cancel click handler
        return false;
      };
    }

  });
})(document, window, jQuery);
