// Select2 format functions  ==============================================
function dz_select2_format_result(que_result) {
  return "<p>"+ que_result.id +" - "+ que_result.name +"</p>";
}

function dz_select2_format_selection(que_result) {
  return que_result.id+' - '+que_result.name; // + "<a class='select2-search-choice-detail' href='javascript:void(0);'></a>";
}

function dz_htmlEscape(str) {
    return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
}

function dz_htmlUnescape(value){
    return String(value)
        .replace(/&quot;/g, '"')
        .replace(/&#39;/g, "'")
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&amp;/g, '&');
}

(function(document, window, $) {
  'use strict';

  // Scrollup ===========================================================
  $.fn.dzScrollUp = function() {
    function init() {
      $base.append('<a href="javascript:void(0)" id="dz-scrollup" class="scrollup btn btn-icon btn-round btn-default" style="display:none"><i class="wb-chevron-up icon"></i></a>');

      $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
          $('#dz-scrollup').fadeIn();
        } else {
          $('#dz-scrollup').fadeOut();
        }
      });

      $('#dz-scrollup').on('click', function(e){
      $("html, body").animate({ scrollTop: 0 }, 600);
        e.preventDefault();
      });
    }

    var $base = $(this);

    if ($(this).size() > 0) {
      init();
    }
  };

  // Ajax Modal Buttons  ==============================================
  $.fn.dzAjaxModal = function(options) {
    function init() {
      $base.on('click', function(){
        if ( options.model_id !== undefined ) {
          model_id = options.model_id;
        } else {
          model_id = $(options.selector).val();
        }
        $('#dz-modal-pk-'+options.id).html(model_id);
        $('#dz-modal-body-'+options.id).html('<h3>Cargando datos...</h3>');
        $('#dz-modal-footer-'+options.id+' .dz-button').each(function() {
          $(this).attr('href', $(this).data('url')+model_id);
        });
        $.ajax({
          url: options.url + model_id,
          dataType: 'html',
          success: function(data) {
            $('#dz-modal-body-'+options.id).html(data);
          },
          error: function(request, status, error) {
            alert('Código '+ options.modelClass +' incorrecto. \n\nERROR: '+request.responseText);
          },
          cache: false
        });
      });
    }

    var $base = $(this), model_id;
    if ( $(this).size() > 0 ) {
      init();
    }
  };

  // Delete message ===========================================================
  $.fn.dzAfterDeleteMessage = function(link, success, data) {
    if ( success ) {
      $.pnotify({
        sticker: false,
        text: data,
        type: 'success'
      });
    } else {
      $.pnotify({
        sticker: false,
        text: error,
        type: 'success'
      });
    }
  };

  // Status change button ===========================================================
  $.fn.dzStatusButton = function() {
    function init() {
      $base.on('click', function(e){
        e.preventDefault();
        var $link = $(this);
        bootbox.confirm(
          $link.data('dialog'),
          function(confirmed){
            if ( confirmed ) {
              $('#status-change').val($link.data('value'));
              $('#'+ $link.data('form-submit')).submit();
            }
          }
        );
      });
    }

    var $base = $(this);
    if ($(this).size() > 0) {
      init();
    }
  };

  // Table header fixed ===========================================================
  $.fn.dzHeaderFixed = function() {
    function init() {
      $base.floatThead({
        scrollContainer: function($base){
          return $base.closest('.wrapper');
        }
      });
    }

    var $base = $(this);
    if ( $(this).size() > 0 ) {
      init();
    }
  };

  // Make label clichable ==========================================================
  $.fn.dzLabelClickable = function() {

    // Click label, useful for RADIO and CHECKBOX form elements
    function init() {
      $base.children('label').on('click', function(e){
        e.preventDefault();
        $(this).siblings('input').click();
      });
    }

    var $base = $(this);
    if ( $(this).size() > 0 ) {
      init();
    }
  };

  // Bootstrap Touchspin ==========================================================
  // Special functions for parser number to SPANSIH format
  $.fn.dzCurrencyTouchspin = function(options) {
    function init() {
      if ( ! $.isEmptyObject(options) ) {
        $.extend(settings, options);
      }

      $base.TouchSpin(settings);
    }

    var $base = $(this);

    // Check "data-step" attribute
    var touch_step = 1;
    if ( $base.attr('data-step') ) {
      touch_step = $base.attr('data-step');
    }

    // Check "data-postfix" attribute
    var touch_postfix = '€';
    if ( $base.attr('data-postfix') ) {
      touch_postfix = $base.attr('data-postfix');
    }

    var settings = {
      // CSS classes
      verticalupclass: 'wb-plus',
      verticaldownclass: 'wb-minus',
      buttondown_class: 'btn btn-outline btn-default',
      buttonup_class: 'btn btn-outline btn-default',

      // Touchspin options
      min: 0,
      max: 999999,
      step: touch_step, // 1, // 0.01,
      decimals: 2,
      boostat: 1,
      maxboostedstep: 1,
      postfix: touch_postfix,

      // Parser number to SPANSIH format
      callback_before_calculation: function(v){
        if ( typeof(v) == 'string' && v.includes(",") ) {
          v = v.replace('.', '');
          v = v.replace(',', '.');
          v = parseFloat(v);
        }
        return v;
      },
      callback_after_calculation: function(v){
        return $.number(v, 2, ',', '.');
      }
    };

    if ( $(this).size() > 0 ) {
      init();
    }
  };

  // Fix error with Markdown textarea and languages
  // -------------------------------------------------------------------------------------------
  $.markdownLanguage = function(selector, model_name, attribute_name) {
    // When a TAB is shown, refresh SIMPLEMDE MARKDOWN for other languages
    $(selector).find('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      var $this = $(this);
      var current_language = $(e.target).data('language');
      if ( current_language != js_globals.defaultLanguage ) {
        setTimeout(function() {
          window['simplemde_'+ model_name +'_'+ current_language +'_'+ attribute_name +'_markdown'].codemirror.refresh();
        }, 0);
      }
    });
  };


  // Document ready ===========================================================
  var Site = window.Site;
  $(document).ready(function() {
    Site.run();

    // Scrollup
    // $('body').dzScrollUp();

    // Bootbox
    $('.dz-bootbox-confirm').dzBootbox();

    // Bootbox
    bootbox.addLocale("custom", {
      OK: 'Continue',
      CANCEL: 'Cancel',
      CONFIRM: 'Continue'
    });
    bootbox.setDefaults({locale: "custom"});

    // AJAX Session Timeout - DZ_LOGIN_REQUIRED
    $(document).ajaxComplete(
      function(event, request, options) {
        if (request.responseText == "DZ_LOGIN_REQUIRED") {
          window.location.href = window.js_globals.baseUrl +'/user/login';
        }
      }
    );

  });
})(document, window, jQuery);
