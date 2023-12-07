(function(document, window, $) {
  
  // SlidePanel global object
  $.dzSlidePanel = {
    $panel: null,
    options: null,
    afterLoad: $.noop,

    // Start scrolling
    // -------------------------------------------------------------------------------------------
    startScroll: function() {
      this.$panel.find('.' + this.options.classes.base + '-scrollable').asScrollable({
        namespace: 'scrollable',
        contentSelector: '>',
        containerSelector: '>'
      });
    },

    // Show errors
    // -------------------------------------------------------------------------------------------
    showErrors: function(error_msg) {
      this.$panel.find('#slidepanel-errors').removeClass('hide').html(error_msg);
      this.$panel.find('.' + this.options.classes.base + '-scrollable').asScrollable('scrollTo', 'vertical', '1');
    }
  };

  // Load custom SlidePanel widget
  $.fn.dzSlidePanel = function(options) {
    function init() {
      if ( ! $.isEmptyObject(options) ) {
        $.extend(settings, options);
      }

      // 20/05/2021 - MULTIPLE INSTANCES
      // Save "afterLoad" for multiple instances
      if ( settings.hasOwnProperty('afterLoad') ) {
        $.dzSlidePanel.afterLoad = settings.afterLoad;
      }

      // 07/12/2023 - URL with EXTRA PARAMETERS
      // @see https://developer.mozilla.org/en-US/docs/Web/API/URL
      var base_url = $base.attr('href');
      if ( settings.hasOwnProperty('urlParams') && settings.urlParams !== null ) {
        base_url = new URL(base_url);
        $.each(settings.urlParams, function(url_param_key, url_param_value) {
          base_url.searchParams.append(url_param_key, url_param_value);
        });

        base_url = base_url.toString();
      }

      $.slidePanel.show({
        url: base_url,
        settings: {
          method: 'GET'
        }
      }, settings);
    }

    var $base = $(this);
    
    var settings = {
      closeSelector: '.slidePanel-close',
      mouseDragHandler: '.slidePanel-handler',
      loading: {
        template: function template(options) {
          return '<div class="' + options.classes.loading + '">\n<div class="loader loader-default"></div>\n</div>';
        },
        showCallback: function showCallback(options) {
          this.$el.addClass(options.classes.loading + '-show');
        },
        hideCallback: function hideCallback(options) {
          this.$el.removeClass(options.classes.loading + '-show');
        }
      },
      template: function(options) {
        return '<div id="' + (options.id !== undefined ? options.id : 'dz-slidepanel') + '" class="' + options.classes.base + ' ' + options.classes.base + '-' + options.direction + '">' +
          '<div class="' + options.classes.base + '-scrollable"><div>' +
          '<div class="' + options.classes.content + '"></div>' +
          '</div></div>' +
          '<div class="' + options.classes.base + '-handler"></div>' +
          '</div>';
      },
      beforeShow: function() {
        var self = this;

        // 20/05/2021 - MULTIPLE INSTANCES
        // Reload "afterLoad" function. Needed for multiple SlidePanel instances
        self.options.afterLoad = $.dzSlidePanel.afterLoad;

        $.dzSlidePanel.$panel = self.$panel;
        $.dzSlidePanel.options = self.options;
      },
    };

    if ($(this).size() > 0) {
      settings.id = $(this).data('panel');
      init();
    }
  };
})(document, window, jQuery);
