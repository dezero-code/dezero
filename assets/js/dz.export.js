(function(document, window, $) {

  // Submit export action
  $.dzExportSubmit = function(self) {
    // Get export URL with new form options
    var export_url = $.dzExportUrl();
    var export_options = $('#export-options-form').serialize();
    if ( export_options != '' ) {
      if ( export_url.match(/\?/) ) {
        export_url += '&';
      } else {
        export_url += '?';
      }
      export_url += $('#export-options-form').serialize();
    }
    export_url = export_url.replace(/\%5B/g, '[');
    export_url = export_url.replace(/\%5D/g, ']');
    console.log(export_url);

    var $btn_close = $('#dz-modal-export-options').children('.dz-modal-footer').children('.btn-close');

    // Create a batch job to check when Excel creation has been finished
    $.ajax({
      url: $('#export-submit-btn').data('create-url'),
      type: 'post',
      dataType: 'json',
      data: {export_url: export_url},
      success: function(data) {
        if ( data.batch_id > 0 ) {
          $(self).removeClass('disabled').addClass('disabled');
          $btn_close.removeClass('disabled').addClass('disabled');
          $('#export-options-field-wrapper').removeClass('hide').addClass('hide');
          $('#export-loading').removeClass('hide');
          $(self).data('batch', data.batch_id);

          // HTTP download request
          export_url += '&batch_id='+ data.batch_id;
          
          // console.log(export_url);
          window.location = export_url;
          // window.open( export_url ,'_blank', 'width=500, height=100, toolbar=no, scrollbars=no, resizable=no');

          // Check if Excel file has been created every 3 seconds (timeout 9 seconds)
          var check_excel_downloaded = setInterval(function(){
            $.ajax({
              url: $('#export-submit-btn').data('check-url'),
              type: 'post',
              dataType: 'json',
              data: { batch_id: data.batch_id },
              success: function(data) {
                if ( data.result == 1 && $(self).data('batch') !== 0 ) {
                  clearInterval(check_excel_downloaded);
            
                  // Update modal HTML content
                  $(self).removeClass('disabled');
                  $btn_close.removeClass('disabled');
                  $('#export-options-field-wrapper').removeClass('hide');
                  $('#export-loading').removeClass('hide').addClass('hide');
                  $(self).data('batch', 0);

                  // Close modal
                  $('#dz-modal-export-options').children('.dz-modal-footer').children('.btn-close').click();

                  // Notify message
                  $.pnotify({
                    sticker: false,
                    text: 'Excel file generated succesfully',
                    type: 'success'
                  });
                }
              },
              timeout: 9000
            });
          }, 3000);
        } else {
          alert('ERROR: It could not be created the Excel file');
        }
      },
      error: function(request, status, error) {
          alert('ERROR: '+request.responseText);
      },
      cache: false
    });
  };

  // Generate export URL
  $.dzExportUrl = function() {
    var export_url = $('#export-submit-btn').data('export-url');
    var que_grid = $('#export-submit-btn').data('grid');
    if ( $('#'+ que_grid).size() > 0 ) {
      var grid_url = $('#'+ que_grid).yiiGridView('getUrl');
      if ( grid_url.match(/\?/) ) {
        var que_grid_url = grid_url.split('?');
        export_url += '?'+ que_grid_url[1];
      }
    }

    return export_url;
  };

  // Get TOTAL ITEMS to export
  $.dzExporTotalItems = function(total_items_url) {
    // Get total items to export
    $('#export-total-loading').removeClass('hide');
    $('#dz-modal-export-options').find('.dz-modal-submit-button').removeClass('hide').addClass('hide');

    // Get export URL
    var json_data = {};
    json_data.export_url = $.dzExportUrl();

    // Some export params?
    $('#export-options-form').find('.extra-export-param').each(function(){
        json_data[$(this).attr('name')] = $(this).val();
    });

    $.ajax({
      url: total_items_url,
      type: 'post',
      dataType: 'json',
      data: json_data,
      success: function(data) {
        $('#export-total-loading').removeClass('hide').addClass('hide');
        if ( data.total_items > 15000 || data.items < 1 ) {
          $('#export-total-success').removeClass('hide').addClass('hide');
          $('#export-total-error').removeClass('hide');
          $('#export-total-error').find('.export-total-items').html(data.total_items);
        } else {
          $('#export-total-error').removeClass('hide').addClass('hide');
          $('#export-total-success').removeClass('hide');
          $('#export-total-success').find('.export-total-items').html(data.total_items);
          $('#dz-modal-export-options').find('.dz-modal-submit-button').removeClass('hide');
        }
      },
      error: function(request, status, error) {
        $('#export-total-loading').removeClass('hide').addClass('hide');
          alert('ERROR: '+request.responseText);
      },
      cache: false
    });
  };

})(document, window, jQuery);