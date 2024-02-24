/**
 * Scripts for USER module
 */
(function(document, window, $) {

  // User Form - Global object
  // -------------------------------------------------------------------------------------------
  $.userForm = {
    init: function() {
      // Disable/enable buttons
      $('a[data-plugin="dz-status-button"]').dzStatusButton();

      // Change password button on update user form page
      $('#change-password-btn').on('click', function(e){
        e.preventDefault();

        var $this = $(this);
        $('#is-password-changed').val(1);
        $('#user-form').find('.password-row').removeClass('hide');
        $('#password-change-container').addClass('hide');
        // $this.parent().parent().parent().parent().addClass('hide');
      });
    }
  };

  // DOCUMENT READY
  // -------------------------------------------------------------------------------------------
  $(document).ready(function() {
    // User form
    if ( $('#user-form').size() > 0  ) {
      $.userForm.init();
    }

    // User change status via panelSlider
    $.userStatus.slidePanel();
  });
})(document, window, jQuery);
