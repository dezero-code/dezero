/**
 * Scripts for MYMODULE module
 */
 (function(document, window, $) {

  // Mymodule Form - Global object
  // -------------------------------------------------------------------------------------------
  $.mymoduleForm = {
    init: function() {
      // Disable/enable buttons
      $('a[data-plugin="dz-status-button"]').dzStatusButton();
    }
  };

  // DOCUMENT READY
  // -------------------------------------------------------------------------------------------
  $(document).ready(function() {
    // Mymodule form
    if ( $('#mymodule-form').size() > 0  ) {
      $.mymoduleForm.init();
    }
  });
})(document, window, jQuery);
