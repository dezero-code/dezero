(function(document, window, $) {
  'use strict';

  // -------------------------------------------------------------------------------------------
  // Status change button
  // -------------------------------------------------------------------------------------------
  $.fn.dezeroStatusButton = function() {
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


  // -------------------------------------------------------------------------------------------
  // LOADER global object for Dezero Framework
  // -------------------------------------------------------------------------------------------
  $.dezeroLoader = {
    dialog: null,
    _timeout: null,
    settings: {
      title: '<h3>Loading...</h3>',
      message: '<p>This action could take several seconds. <span class="text-danger">Please, do not refresh the page!</span></p>',
      closeLabel: 'Close',
      isCloseButton: false,
      className: 'dezero-loader-modal',
      timeout: 30000
    },

    // Show loading modal
    // ----------------------------------------------------
    show: function(options) {
      var self = this;

      if ( ! $.isEmptyObject(options) ) {
        $.extend(self.settings, options);
      }
      // Add loader gif
      var loader_message = `<div class="dz-loader loader loader-circle"></div><div class="title">${self.settings.title}</div><div class="message">${self.settings.message}</div>`;

      // Open modal with dialgo
      self.dialog = bootbox.dialog({
        message: loader_message,
        closeButton: self.settings.isCloseButton,
        className: self.settings.className,
        buttons: {
          cancel: {
            label: self.settings.closeLabel,
            className: 'btn-primary',
            callback: function(){
              $.dezeroLoader.close();
            }
          },
        }
      });

      // Timeout
      if ( self.settings.timeout !== null && self.settings.timeout > 0 ) {
        self._timeout = setTimeout(function(){
          // $.dezeroLoader.close();
          $.dezeroLoader.showClose();
        }, self.settings.timeout);
      }
    },

    // Close loading modal
    // ----------------------------------------------------
    close: function() {
      if ( this.dialog !== null ) {
        this.dialog.modal('hide');
        if ( this._timeout !== null ) {
          clearTimeout(this._timeout);
        }
      }
    },

    // Show close button
    // ----------------------------------------------------
    showClose: function() {
      if ( this.dialog !== null ) {
        this.dialog.find('.modal-footer').removeClass('block').addClass('block');
      }
    },

    // Hide close button
    // ----------------------------------------------------
    hideClose: function() {
      if ( this.dialog !== null ) {
        this.dialog.find('.modal-footer').removeClass('block');
      }
    },

    // Set title for loading modal
    // ----------------------------------------------------
    setTitle: function(title) {
      if ( this.dialog !== null ) {
        this.dialog.find('.title').html(title);
      }
    },

    // Set message for loading modal
    // ----------------------------------------------------
    setMessage: function(message) {
      if ( this.dialog !== null ) {
        this.dialog.find('.message').html(message);
      }
    },

    // Override default settings
    // ----------------------------------------------------
    setDefaults: function(options) {
      if ( ! $.isEmptyObject(options) ) {
        $.extend(this.settings, options);
      }
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
        bootbox.confirm({
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
        });

        // confirm will always return false on the first call
        // to cancel click handler
        return false;
      };
    }

  });
})(document, window, jQuery);
