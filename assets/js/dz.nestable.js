(function(document, window, $) {
  // Refresh 'Nestable Tree' via AJAX
  $.dzNestableResetTree = function(name, data_tree) {
    var $dz_nestable_loading = $('#'+ name +'-loading-tree');
    var $dz_nestable = $('#'+ name +'-nestable-wrapper');
    $dz_nestable.nestable('destroy');
    $dz_nestable_loading.height($dz_nestable.height()+'px').removeClass('hide');
    $dz_nestable.html(data_tree).removeClass('hide');
    $dz_nestable_loading.addClass('hide');
  };

  // Refresh 'Nestable Tree' via AJAX
  $.dzNestableReload = function(ajax_url, nestable_name) {
    $.ajax({
      url: ajax_url,
      dataType: 'json',
      success: function(data) {
        if ( data.result == 'success' ) {
          $.dzNestableResetTree(nestable_name, data.tree);
        }
      },
      error: function(request, status, error) {
        alert('ERROR: '+ request.responseText);
      },
      cache: false
    });
  };

  // Load 'Nestable Tree' widget
  $.fn.dzNestable = function(options) {
    function init() {
      if ( ! $.isEmptyObject(options) ) {
        $.extend(settings, options);
      }

      // Nestable - Readonly mode
      // @see https://css-tricks.com/snippets/jquery/make-an-jquery-hasattr/
      if (typeof attr_readonly !== typeof undefined && attr_readonly !== false && attr_readonly === 'true' ) {
        settings.readOnly = true;
        $base.nestable(settings);
      }

      // Nestable - Edit mode
      else {
        $base.nestable(settings).on('change', function(){
          var que_nestable = $(this).nestable('serialize');
          var $this = $(this);
          $('#'+ $this.data('name') +'-loading-tree').height($this.height()+'px').removeClass('hide');
          $this.addClass('hide');

          $.ajax({
            url: $this.data('url'),
            type: 'post',
            dataType: 'json',
            data: { nestable: que_nestable },
            success: function(data) {
              if ( $('#'+ $this.data('name') +'-grid').size() > 0 ) {
                $.fn.yiiGridView.update($this.data('name') + '-grid');
              }
              $('#'+ $this.data('name') +'-loading-tree').addClass('hide');
              $this.removeClass('hide');
            },
            error: function(request, status, error) {
              alert('ERROR: '+request.responseText);
            },
            cache: false
          });
          // console.log(window.JSON.stringify(que_nestable));
        });
      }
    }

    var $base = $(this);
    
    var settings = {
      maxDepth: 1
    };

    var attr_readonly = $base.attr('data-readonly');

    if ($(this).size() > 0) {
      init();
    }
  };
})(document, window, jQuery);