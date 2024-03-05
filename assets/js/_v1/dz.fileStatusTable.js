(function(document, window, $) {
  
  // FileStatusTable global object
  $.dzFileStatusTable = {
    
    // Properties
    // -------------------------------
    $base: null,
    _slidePanel: null,
    action: null,

    // Load SlidePanel actions
    // -------------------------------
    init: function(element) {
      if ( $(element).size() > 0 ) {
        this.$base = $(element);

        var $actions = this.$base.children("tbody").children("tr").children('td.actions-column');

        // UPDATE action
        $actions.children('.update-action').on('click', function(e){      
          e.preventDefault();
          $.dzFileStatusTable.action = $(this).data('action');
          $(this).tooltip('hide');

          // Show SLIDE PANEL clicking on an action button from the file list table
          $(this).dzSlidePanel({
            afterLoad: $.dzFileStatusTable.afterLoad
          });
        });

        // DELETE action
        $actions.children('.delete-action').on('click', function(e){
          e.preventDefault();
          $.dzFileStatusTable.action = 'delete';
          var self = this;
          $.dzSlideTable.delete(self, {
            confirmMessage: '<h3>Are your sure you want to <span class="text-danger">DELETE</span> this file?</h3>',
            successMessage: 'File removed successfully',
            
            // Params sent as INPUT params via AJAX delete action
            ajaxData: JSON.stringify({
              file_id: $(self).data('file'),
              validation_type: $(self).data('validation')
            }),

            // After delete event -> Reload file table
            afterDelete: function(data) {
              $.dzFileStatusTable.refreshTable();
            }
          });
        });

      }
    },

    // Refresh/reload file table
    // ---------------------------------------------------
    refreshTable: function(params) {
      // Default options
      var options = {
        afterAjaxUpdate: function(id, data) {
          // $('html, body').animate({scrollTop: 0}, 100);
          $.dzFileStatusTable.init('#'+ id);
        }
      };

      // Add input params
      if ( ! $.isEmptyObject(params) ) {
        $.extend(options, params);
      }

      // Reload table
      $.dzSlideTable.reload(this.$base.attr('id'), options);
    },

    // AfterLoad callback for table actions
    // ------------------------------------------------------------------------------------
    afterLoad: function(){
      var self = this;
      var que_action = $.dzFileStatusTable.action;
      $.dzFileStatusTable._slidePanel = this;

      // Get current action from data-action attribute on <div class="file-status-slidepanel-wrapper">
      $.dzFileStatusTable.action = self.$panel.find('#file-status-slidepanel-action').eq(0).data('action');
      que_action = $.dzFileStatusTable.action;

      // Start scroll
      $.dzSlidePanel.startScroll();

      // Change status
      if ( que_action === 'change_status' )
      {
        // File change status
        var $file_status = $('#AssetFileStatus_status_type');
        var $file_btn = $('#file-status-save-btn');

        $file_status.select2({
          templateResult: $.dzFileStatusTable.select2_format,
          templateSelection: $.dzFileStatusTable.select2_format,
          width: '100%',
          dropdownCssClass: 'modal-dropdown'
        });

        // Select2 change event
        /*
        $file_status.on('change', function(e){
          if ( $(this).val() == $(this).data('init-value') ) {
            $file_btn.prop('disabled', true);
          } else {
            $file_btn.prop('disabled', false);
          }
        });
        */
        
        // Save button -> Submit form
        $file_btn.on('click', $.dzFileStatusTable.submitForm);
      }

      // Upload file via Dropzone
      else if ( que_action === 'upload_files')
      {
        $.dzFileStatusTable.dropzone();
      }
    },

    // Submit Status form
    // -------------------------------
    submitForm: function(e){
      e.preventDefault();

      var $this = $(this);
      var self = $.dzFileStatusTable._slidePanel;
      var $file_status = $('#AssetFileStatus_status_type');
      var is_changed = false;

      if ( $file_status.val() == $file_status.data('init-value') ) {
        msg_alert = '<h3>Are you sure you want to save it?</h3>';
      } else {
        is_changed = true;
        msg_alert = '<h3>Are you sure you want to change the status to '+ $.dzFileStatusTable.statusLabel($file_status.val()) +'?</h3>';
      }

      bootbox.confirm(
        msg_alert,
        function(confirmed){
          if ( confirmed ) {
            // Disable button to avoid click twice
            $this.prop('disabled', true);

            // Change status via AJAX
            $.ajax({
              url: js_globals.base_url +'/asset/status/update?validation_type='+ $this.data('validation') +'&file_id='+  $this.data('file'),
              type: 'POST',
              cache: false,
              contentType: "application/json; charset=utf-8",
              dataType: "json",
              data: JSON.stringify({
                validation_type: $this.data('validation'),
                file_id: $this.data('file'),
                new_status: $file_status.val(),
                new_comments: $('#AssetFileStatus_comments').val()
              }),
              success: function(data) {
                if ( data.error_code == 0 ) {
                  // Show success message
                  $.pnotify({
                    sticker: false,
                    text: is_changed ? 'Status changed successfully' : 'Changes saved successfully',
                    type: 'success'
                  });

                  // Hide slidePanel
                  $.slidePanel.hide();

                  // Reload file table
                  $.dzFileStatusTable.refreshTable({
                    afterAjaxUpdate: function(id, data) {
                      // $('html, body').animate({scrollTop: 0}, 100);
                      $.dzFileStatusTable.init('#'+ id);
                    }
                  });
                  
                }
                else {
                  // Show errors
                  $.dzSlidePanel.showErrors(data.error_msg);

                  // Enable button again
                  $this.prop('disabled', false);
                }
              },
              error: function(xhr, ajaxOptions, thrownError) {
                // Show errors
                $.dzSlidePanel.showErrors('ERROR - Unable to change the status');

                // Enable button again
                $this.prop('disabled', false);
              }
            });
          }
        }
      );
    },

    // Load Krajee Dropzone File Upload
    // -------------------------------
    dropzone: function() {
      var $container = $('#file-status-upload-container');

      // Reset Dropzone messages
      $('#file-status-success-message').hide();
      $('#file-status-success-message').html('');
      $('#file-status-error-message').hide();
      $('#file-status-error-message').html('');

      // ---------------------------------------------------------
      // KRAJEE DROPZONE
      // ---------------------------------------------------------
      var $file_input = $('#file-status-file-input');

      $file_input.fileinput({
        language: window.js_globals.language,
        uploadUrl: $container.data('url'),
        uploadAsync: false,
        showPreview: true,  // needed to show the dropzone
        showUpload: false,
        showRemove: false,
        showCaption: false,
        showZoom: false,

        // allowedFileExtensions: ['pdf','doc','docx'],
        // allowedFileTypes: ['video'],
        maxFileSize: 102400, // 100 MB (104857600 / 1024)
        maxFileCount: 10,
        elErrorContainer: '#file-status-error-message',
        dropZoneEnabled: true,

        browseIcon: '<i class=\"wb wb-folder\"></i>&nbsp;',
        browseClass: 'btn btn-primary btn-block',
        removeIcon: '<i class=\"wb wb-trash text-danger\"></i>',
        removeClass: 'btn btn-default',
        cancelIcon: '<i class=\"wb wb-close\"></i>',
        cancelClass: 'btn btn-default btn-block',
        uploadIcon: '<i class=\"wb wb-upload\"></i>',
        uploadClass: 'btn btn-default',
        zoomIcon: '<i class=\"wb wb-eye\"></i>',
        zoomClass: 'btn btn-xs btn-default',
        uploadRetry: '<i class=\"wb wb-refresh\"></i>',
        dropZoneTitle: 'Drag & drop a file here &hellip;',

        // Dropzone / preview window
        fileActionSettings: {
          removeIcon: '<i class=\"wb wb-trash text-danger\"></i>',
          removeClass: 'btn btn-xs btn-default',
          removeTitle: 'Delete file',
          uploadIcon: '<i class=\"wb wb-upload text-success\"></i>',
          uploadClass: 'btn btn-xs btn-default',
          uploadTitle: 'Upload file',
          zoomIcon: '<i class=\"wb wb-eye\"></i>',
          zoomClass: 'btn btn-xs btn-default',
          uploadRetry: '<i class=\"wb wb-refresh\"></i>',
          indicatorNew: '<i class=\"wb wb-arrow-down text-warning\"></i>',
          indicatorSuccess: '<i class=\"wb wb-check text-success\"></i>',
          indicatorError: '<i class=\"wb wb-warning text-danger\"></i>',
          indicatorLoading: '<i class=\"wb wb-arrow-up text-muted\"></i>',
          indicatorNewTitle: 'Not uploaded yet',
          indicatorSuccessTitle: 'Uploaded',
          indicatorErrorTitle: 'Upload Error',
          indicatorLoadingTitle: 'Uploading ...'
        },

        // function a callback to convert the filename as a slug string eliminating special characters
        slugCallback: function(text) {
          return text;
          // return isEmpty(text) ? '' : String(text).replace(/[\[\]\/\{}:;#%=\(\)\*\+\?\\\^\$\|<>&\"']/g, '_');
        }
      })
      
      // trigger upload method immediately after files are selected
      .on('filebatchselected', function(event, files) {
        $file_input.fileinput('upload');
      })

      // before batch upload event
      // .on('filebatchpreupload', function(event, data, id, index) {
      //  $('#file-status-success-message').html('<h4>Document upload results</h4><ul></ul>').hide();
      // })

      // after batch upload event (success)
      .on('filebatchuploadsuccess', function(event, data) {
        var out = '';
        $.each(data.files, function(key, file) {
          var fname = file.name;
          // out = out + '<li>' + 'Document #' + (key + 1) + ' - "'  +  fname + '" uploaded successfully.' + '</li>';
          out = out + '<li>"'+  fname +'" uploaded successfully.</li>';
        });

        // Show success message
        $.pnotify({
          sticker: false,
          text: '<ul>'+ out + '</ul>',
          type: 'success'
        });

        // Reload file list
        setTimeout(function(){
          $file_input.fileinput('clear');
          $file_input.blur();

          // Hide slidePanel
          $.slidePanel.hide();

          // Reload file list
          $.dzFileStatusTable.refreshTable();
        }, 1000);
      });
    },

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

        case 'accepted':
          que_color = 'green-800';
        break;

        case 'rejected':
          que_color = 'red-800';
        break;

        case 'incomplete':
          que_color = 'purple-800';
        break;
      }

      if ( que_color !== '' ) {
        return $('<span><i class=\'wb-medium-point '+ que_color +'\' aria-hidden=\'true\'></i> '+ item.text +'</span>');
      }

      return $('<span>'+ item.text +'</span>');
    },

    // Status labels
    statusLabel: function(status_type) {
      switch ( status_type )
      {
        case 'pending':
          return 'Pending';
        break;

        case 'accepted':
          return 'Accepted';
        break;

        case 'rejected':
          return 'Rejected';
        break;

        case 'incomplete':
          return 'Incomplete';
        break;

        default:
          return '';
        break;

      }
    },

  };
})(document, window, jQuery);