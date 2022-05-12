$(function () {
  // Ajax Form Button  ==============================================
  $.fn.dzAjaxFormButton = function(options) {
    function init() {
      $base.on('click', function(e){
        // $('body').modalmanager('loading');
        $('#dz-modal-title', $modal).html("&nbsp;");
        $('#dz-modal-subtitle').html("");
        $('#dz-modal-body', $modal).html("<p>Loading</p>");
        var que_width = 860;
        if ( typeof($(this).data('width')) != 'undefined' ) {
          if ( $(this).data('width') == 'full' ) {
            var full_width = $(window).width();
            que_width = full_width - Math.round(full_width*0.05);
          } else {
            que_width = $(this).data('width');
          }
        }
        else if ( typeof($modal.data('width')) != 'undefined' ) {
          if ( $modal.data('width') == 'full' ) {
            var full_width = $(window).width();
            que_width = full_width - Math.round(full_width*0.05);
          } else {
            que_width = $modal.data('width');
          }
        }

        $.ajax({
          url: $(this).data('url'),
          dataType: settings.dataType,
          success: function(data) {
            $('#dz-modal-title', $modal).html(data.title);
            if ( data.hasOwnProperty('subtitle') ) {
              $('#dz-modal-subtitle', $modal).html(data.subtitle);
            }
            $('#dz-modal-body', $modal).html(data.content);
            var modal_replace = true;
            if ( data.hasOwnProperty('modal') && data.modal.hasOwnProperty('replace') ) {
              modal_replace = data.modal.replace;
            }
            $modal.modal({
              show: true,
              replace: modal_replace,
              modalOverflow: true,
              focusOn: 'input:first',
              width: que_width
              // maxWidth: $(window).height() - 95
            }).width(que_width);
            if ( data.hasOwnProperty('modal') && data.modal.hasOwnProperty('zindex') ) {
              $modal.parent().css('z-index', parseInt($modal.parent().css('z-index')) + data.modal.zindex);
            }
            if ( data.hasOwnProperty('modal') && data.modal.hasOwnProperty('hide-submit') ) {
              $("#dz-modal-footer", $modal).find(".btn-primary").hide();
            }
            if ( typeof($base.data('hide-submit')) != 'undefined' ) {
              $("#dz-modal-footer", $modal).find(".btn-primary").hide();
            }
            settings.afterModalSuccess(data);
          },
          error: function(request, status, error) {
            alert('dzAjaxFormButton error.\n\nERROR: '+request.responseText);
          },
          cache: false
        });
        e.stopPropagation();
      });
    }

    var $base = $(this);
    var settings = {
      dataType: 'json',
      modal_id: 'dz-modal-ajax-grid',
      afterModalSuccess: function(data) {
        //$.dzAjaxGridRefresh(grid_id);
      }
    };
    if ( ! $.isEmptyObject(options) ) {
      $.extend(settings, options);
    }
    if ( $(this).size() > 0 ) {
      var grid_id = $(this).data('grid_id');
      var $modal = $("#"+ settings.modal_id);
      init();
    }
  };

  // Ajax Grid Refresh  ==============================================
  $.dzAjaxGridRefresh = function(que_grid_id) {
    if ( $('#'+que_grid_id+'-url').length > 0 ) {
      $.fn.yiiGridView.update(que_grid_id, {
        url: $('#'+que_grid_id+'-url').val()
      });
    } else {
      $.fn.yiiGridView.update(que_grid_id);
    }
  };

  // Ajax Grid Modal Link (details)  ==============================================
  $.fn.dzAjaxGridModalLink = function() {
    function init() {
      $base.on('click', function(e){
        var que_width = 860;
        if ( typeof($(this).data('width')) != 'undefined' ) {
          if ( $(this).data('width') == 'full' ) {
            var full_width = $(window).width();
            que_width = full_width - Math.round(full_width*0.05);
          } else {
            que_width = $(this).data('width');
          }
        }
        $modal.data('width', que_width).width(que_width);
        // $('body').modalmanager('loading');
        $('.dz-modal-title', $modal).html($(this).data('title'));
        $('.dz-modal-subtitle', $modal).html($(this).data('subtitle'));
        $('.dz-modal-body', $modal).html("<p>Loading...</p>");
        $('.dz-modal-button', $modal).hide();
        if ( typeof($(this).data('view_url')) != 'undefined' ) {
          $('.dz-modal-view-button', $modal).attr('href', $(this).data('url').replace('/teaser','/view')).show();
        }
        $.ajax({
          url: $(this).data('url'),
          dataType: 'html',
          success: function(data) {
            $('.dz-modal-body', $modal).html(data);
            $modal.modal({
              show: true,
              replace: true,
              modalOverflow: true,
              width: que_width
            }).width(que_width);
          },
          error: function(request, status, error) {
            alert('dzAjaxGridModalLink error.\n\nERROR: '+request.responseText);
          },
          cache: false
        });
        e.stopPropagation();
      });
    }

    var $base = $(this);
    if ( $(this).size() > 0 ) {
      var grid_id = $(this).data('grid_id');
      var $modal = $("#dz-modal-"+ grid_id);
      var que_width = 860;
      if ( typeof($modal.data('width')) != 'undefined' ) {
        que_width = $modal.data('width');
      }
      $modal.data('modal-overflow', 1).data('width', que_width);
      init();
    }
  };

  // Ajax Grid Modal Form Link  ==============================================
  $.fn.dzAjaxGridModalFormLink = function() {
    function init() {
      $base.on('click', function(e){
        var que_width = 860;
        if ( typeof($(this).data('width')) != 'undefined' ) {
          if ( $(this).data('width') == 'full' ) {
            var full_width = $(window).width();
            que_width = full_width - Math.round(full_width*0.05);
          } else {
            que_width = $(this).data('width');
          }
        }

        // $('body').modalmanager('loading');
        $('.dz-modal-title', $modal).html($(this).data('title'));
        $('.dz-modal-subtitle', $modal).html($(this).data('subtitle'));
        $('.dz-modal-body', $modal).html("<p>Loading</p>");
        $('.dz-modal-button', $modal).hide();
        if ( typeof($(this).data('submit-button')) != 'undefined' ) {
          $('.dz-modal-submit-button', $modal).text($(this).data('submit-button'));
        }
        if ( typeof($(this).data('hide-submit')) == 'undefined' ) {
          $('.dz-modal-submit-button', $modal).show();
        }
        $.ajax({
          url: $(this).data('url'),
          dataType: 'json',
          success: function(data) {
            if ( data.hasOwnProperty('message') ) {
              $.pnotify({
                sticker: false,
                text: data.message,
                type: 'error',
                hide: false
              });
            }
            if ( data.hasOwnProperty('title') ) {
              $('.dz-modal-title', $modal).html(data.title);
            }
            if ( data.hasOwnProperty('subtitle') ) {
              $('.dz-modal-subtitle', $modal).html(data.subtitle);
            }
            $('.dz-modal-body', $modal).html(data.content);
            $modal.modal({
              show: true,
              replace: true,
              modalOverflow: true,
              focusOn: 'input:first',
              width: que_width
              // maxWidth: $(window).height() - 95
            }).width(que_width);
          },
          error: function(request, status, error) {
            alert('dzAjaxGridModalFormLink error.\n\nERROR: '+request.responseText);
          },
          cache: false
        });
        e.stopPropagation();
      });
    }

    var $base = $(this);
    if ( $(this).size() > 0 ) {
      if ( typeof($base.data('grid_id')) != 'undefined' ) {
        var grid_id = $(this).data('grid_id');
        var $modal = $("#dz-modal-"+ grid_id);
      }
      if ( typeof($base.data('modal')) != 'undefined' ) {
        var $modal = $("#"+$(this).data('modal'));
      }
      var que_width = 860;
      if ( typeof($modal.data('width')) != 'undefined' ) {
        que_width = $modal.data('width');
      }
      $modal.data('modal-overflow', 1).data('width', que_width);
      init();
    }
  };

  // dzAjaxFormSubmit ==============================================
  $.fn.dzAjaxFormSubmit = function(options) {
    function init() {
      $base.find('.btn-close').on('click', function(){
        $modal.modal("hide");
      });
    }

    // Show response for AjaxGridView
    function show_response(data) {
      if ( data.result == 'error' ) {
        $base.parent().parent().html(data.content);
        if ( data.hasOwnProperty('message') ) {
          $.pnotify({
            sticker: false,
            text: data.message,
            type: 'error',
            hide: false
          });
        }
      }
      else if ( data.result == 'success' ) {
        // Custom "success" function
        if ( options.hasOwnProperty('success') ) {
          options.success(data);
        }

        // Default "success" function
        else {
          $.pnotify({
            sticker: false,
            text: data.message,
            type: 'success'
          });
          if ($modal.length > 0) {
            $modal.modal("hide");
            $.dzAjaxGridRefresh($base.data('grid_id'));
          }
        }
      }
      else if ( data.result == 'replace' ) {
        $('#dz-modal-ajax-grid').find(".modal-body").html(data.content);
        $.dzAjaxGridRefresh($base.data('grid_id'));
      }
    }

    var $base = $(this);
    if ( options.action == 'init' )
    {
      var settings = {
        modal_id: 'dz-modal-ajax-grid'
      };
      if ( $(this).size() > 0 ) {
        if ( ! $.isEmptyObject(options) ) {
          $.extend(settings, options);
        }
        var $modal = $("#"+ settings.modal_id);
        init();
      }
    }
    else if ( options.action == 'submit' )
    {
      var settings = {
        dataType: 'json',
        success: show_response
      };
      var $modal = $base.parent().parent().parent();
      $(this).ajaxSubmit(settings);
    }
  };


  // This function hijacks regular form submission and turns it into ajax submission with jquery form plugin.
  $.dzAjaxAfterValidate = function(form, data, hasError) {
    if (!hasError) {
      var settings = {
        action: 'submit'
      };
      if ( ! $.isEmptyObject(data) ) {
        $.extend(settings, data);
      }
      $(form).dzAjaxFormSubmit(settings);
    }
    return false;
  };
});