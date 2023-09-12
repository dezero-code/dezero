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


  // Category Nestable - Global object
  // -------------------------------------------------------------------------------------------
  $.categoryNestable = function() {
    var $nestable = $('#category-nestable-wrapper');
    $nestable.dezeroNestable({
      maxDepth: 1,
      readOnly: $nestable.attr('data-readonly') ? true : false
    });
  };

  // DOCUMENT READY
  // -------------------------------------------------------------------------------------------
  $(document).ready(function() {
    // Category form
    if ( $('#category-form').length > 0  ) {
      $.categoryForm.init();
    }

    // Load nestable tree widget
    if ( $('#category-nestable-wrapper').length > 0 ) {
      $.categoryNestable();
    }
  });
})(document, window, jQuery);
