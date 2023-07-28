/**
 * Scripts for CATEGORY module
 */
 (function(document, window, $) {

  // Category Form - Global object
  // -------------------------------------------------------------------------------------------
  $.categoryForm = {
    init: function() {
      // Disable/enable buttons
      $('a[data-plugin="dz-status-button"]').dzStatusButton();
    }
  };

  // DOCUMENT READY
  // -------------------------------------------------------------------------------------------
  $(document).ready(function() {
    // Category form
    if ( $('#category-form').size() > 0  ) {
      $.categoryForm.init();
    }
  });
})(document, window, jQuery);
