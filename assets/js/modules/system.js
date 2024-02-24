/**
 * Scripts for SYSTEM module
 */
 (function(document, window, $) {

  // DOCUMENT READY
  // -------------------------------------------------------------------------------------------
  $(document).ready(function() {
    // Log content
    if ( $('#log-content').size() > 0  ) {
      var $log_content = $('#log-content');
      // $('#log-content').scrollTop($('#log-content').height());
      $log_content.scrollTop($log_content.offset().top + $log_content[0].scrollHeight);
    }
  });

})(document, window, jQuery);
