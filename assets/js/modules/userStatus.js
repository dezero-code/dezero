(function(document, window, $) {

  // User SlidePanel global object
  // -------------------------------------------------------------------------------------------
  $.userStatus = {

    // Properties
    // -------------------------------
    _slidePanel: null,
    action: null,

    // STATUS TYPE - Special function to render format for SELECT2 widget
    // -------------------------------------------------------------------------------------------
    select2_format: function(item) {
      if (!item.id) {
        return $('<span>- All -</span>');
      }

      var que_color = '';
      switch ( item.id ) {
        case 'pending':
          que_color = 'blue-800';
        break;

        case 'active':
          que_color = 'green-800';
        break;

        case 'disabled':
        case 'deleted':
          que_color = 'red-800';
        break;

        case 'banned':
          que_color = 'purple-800';
        break;
     }

      if ( que_color !== '' ) {
        return $('<span><i class=\'wb-medium-point '+ que_color +'\' aria-hidden=\'true\'></i> '+ item.text +'</span>');
      }

      return $('<span>'+ item.text +'</span>');
    },

    // Load SlidePanel
    // -------------------------------
    slidePanel: function() {
      var self = this;
      $('#user-status-btn').on('click', self.show);
      $('#user-history-btn').on('click', self.show);
    },

    // Show SlidePanel
    // ------------------------------
    show: function(e) {
      e.preventDefault();

      // Show SLIDE PANEL clicking on an action button from the status table
      $(this).dzSlidePanel({
        afterLoad: $.userStatus.afterLoad
      });
    },

    // Load User status form
    // -------------------------------
    afterLoad: function(){
      var self = this;
      $.userStatus._slidePanel = this;

      // Start scroll
      $.dzSlidePanel.startScroll();

      var $user_btn = $('#user-status-save-btn');
      var $user_status = $('#user-status_type');

      // User change status
      $user_status.select2({
        templateResult: $.userStatus.select2_format,
        templateSelection: $.userStatus.select2_format,
        width: '100%',
        dropdownCssClass: 'modal-dropdown'
      });

      // Select2 change event
      $user_status.on('change', function(e){
        // $('#send-mail-row').removeClass('hide').addClass('hide');
        // $('#help-active-block').removeClass('hide').addClass('hide');
        // $('#help-rejected-block').removeClass('hide').addClass('hide');

        if ( $(this).val() == $(this).data('init-value') ) {
          $user_btn.prop('disabled', true);
        } else {
          $user_btn.prop('disabled', false);

          // Show "send email"?
          /*
          if ( $(this).val() == "active" ) {
            $('#send-mail-row').removeClass('hide');
            $('#help-active-block').removeClass('hide');
          }
          else if ( $(this).val() == "rejected" ) {
            $('#send-mail-row').removeClass('hide');
            $('#help-rejected-block').removeClass('hide');
          }
          */
        }
      });

      // Change sending mails
      // $('#user-is-sending-mail').children('.radio-custom').dzLabelClickable();

      // Save status button
      $user_btn.on('click', $.userStatus.submitForm);
    },

    // Submit Status form
    // -------------------------------
    submitForm: function(e){
      e.preventDefault();

      var $this = $(this);
      var self = $.userStatus._slidePanel;
      var $user_status = $('#user-status_type');
      var $user_btn = $('#user-status-save-btn');

      // var msg_alert = '<h3>Are you sure you want to change the status to '+ $userStatus.val().toUpperCase().replace(/\_/g, ' ') +'?</h3>';
      var msg_alert = '<h3>Are you sure you want to change the status to '+ $.userStatus.statusLabel($user_status.val()) +'?</h3>';
      msg_alert += '<p></p>';

      bootbox.confirm(
        msg_alert,
        function(confirmed){
          if ( confirmed ) {
            // Disable button to avoid click twice
            $user_btn.prop('disabled', true);

            // Change status via AJAX
            $.ajax({
              url: js_globals.base_url +'user/admin/status?user_id='+ $this.data('user'),
              type: 'POST',
              cache: false,
              contentType: "application/json; charset=utf-8",
              dataType: "json",
              data: JSON.stringify({
                user_id: $this.data('user'),
                new_status: $user_status.val(),
                new_comments: $('#Status_comments').val(),
                // is_sending_mail: $('input[name="User[is_sending_mail]"]:checked').val()
              }),
              success: function(data) {
                if ( data.error_code == 0 ) {
                  // Show success message
                  $.pnotify({
                    sticker: false,
                    text: 'Status changes successfully',
                    type: 'success'
                  });

                  // Hide slidePanel
                  $.slidePanel.hide();

                  // Reload page
                  window.location.href = js_globals.base_url +'/user/admin/update?user_id='+ $this.data('user') +'&new_status='+ $user_status.val();
                }
                else {
                  alert('ERROR '+ data.error_code +': '+ data.error_msg);

                  // Enable button again
                  $user_btn.prop('disabled', false);
                }
              },
              error: function(xhr, ajaxOptions, thrownError) {
                console.log('Unable to change the status');

                // Enable button again
                $user_btn.prop('disabled', false);
              }
            });
          }
        }
      );
    },

    // Status labels
    statusLabel: function(status_type) {
      switch ( status_type )
      {
        case 'pending':
          return 'Pending';
        break;

        case 'active':
          return 'Active';
        break;

        case 'disabled':
          return 'Disabled';
        break;

        case 'banned':
          return 'Banned';
        break;

        case 'deleted':
          return 'Deleted';
        break;

        default:
          return '';
        break;

      }
    }
  };
})(document, window, jQuery);
