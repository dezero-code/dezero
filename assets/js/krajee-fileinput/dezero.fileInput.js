(function(document, window, $) {

  // CUSTOM KARJEE FILEINPUT
  // -------------------------------------------------------------------------------------------
  $.dezeroFileinput = {

    /**
     * This event is triggered when the file input remove button or
     * preview window close icon is pressed for clearing the file preview.
     *
     * @see https://plugins.krajee.com/file-input/plugin-events#fileclear
     */
    fileclear: function(e) {
      // Clear hidden field
      $hidden_field = $.dezeroFileinput.getHiddenField($(this).attr('id'));
      $hidden_field.val('');
    },

    getHiddenField: function(id) {
      return $('#'+ id +'-hidden');
    }
  };

})(document, window, jQuery);
