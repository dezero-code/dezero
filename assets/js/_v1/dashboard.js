(function(document, window, $) {
	$(document).ready(function() {

    // Event listener function when a graphic bar is drawn
    function on_draw_chart(data) {
      if (data.type === 'bar') {
        // $("#ecommerceRevenue .ct-labels").attr('transform', 'translate(0 15)');
        var parent = new Chartist.Svg(data.element._node.parentNode);
        parent.elem('line', {
          x1: data.x1,
          // x2: data.x2,
          y1: data.y2,
          y2: 0,
          "class": 'ct-bar-fill'
        });

        // Show values of column bars
        // @see https://github.com/gionkunz/chartist-js/issues/281
        var barHorizontalCenter, barVerticalCenter, label, value;
        barHorizontalCenter = data.x1 + (data.element.width() * .5);
        // barVerticalCenter = data.y1 + (data.element.height() * -1) + 10;
        barVerticalCenter = data.y1 + (data.element.height() * -1) - 5;
        value = data.element.attr('ct:value');
        if (value !== '0') {
          label = new Chartist.Svg('text');
          label.text(value);
          label.addClass("ct-barlabel");
          label.attr({
            x: barHorizontalCenter,
            y: barVerticalCenter,
            'text-anchor': 'middle'
          });
          return data.group.append(label);
        }
      }
    };

    // Datepicker
    var date_options = {
      autoclose: true,
      clearBtn: true,
      format: 'dd/mm/yyyy',
      language: 'es'
    };
    $('#chart-from-date').datepicker(date_options);
    $('#chart-to-date').datepicker(date_options);
    $('#compare-from-date').datepicker(date_options);
    $('#compare-to-date').datepicker(date_options);

    var $chart_graphic = $('#vilars-graphic-bar');
    var $chart_loader = $('#chart-loader');
    var $chart_date_type = $('#chart-date-type');
    var $chart_filter_btn = $('#chart-filter-btn');

    var $compare_graphic = $('#vilars-compare-bar');
    var $compare_loader = $('#compare-loader');
    var $compare_date_type = $('#compare-date-type');
    var $compare_filter_btn = $('#compare-filter-btn');

    // BARH CHART
    var chart_options = {
      axisX: {
        showGrid: false
      },
      axisY: {
        showGrid: true,
        scaleMinSpace: 30
      },
      height: 220,
      seriesBarDistance: 10
    };

    var chart_graphic = new Chartist.Bar('#vilars-graphic-bar', {
      labels: [],
      series: [ [] ]
    }, chart_options);

    var compare_graphic = new Chartist.Bar('#vilars-compare-bar', {
      labels: [],
      series: [ [] ]
    }, chart_options);

    chart_graphic.on('draw', on_draw_chart);
    compare_graphic.on('draw', on_draw_chart);

    // Date type
    $chart_date_type.children('.btn').on('click', function(e){
      e.preventDefault();
      $chart_date_type.children('.btn').removeClass('active');
      $(this).addClass('active');
      $chart_filter_btn.click();
    });
    $compare_date_type.children('.btn').on('click', function(e){
      e.preventDefault();
      $compare_date_type.children('.btn').removeClass('active');
      $(this).addClass('active');
      $compare_filter_btn.click();
    });
  
    // APLICAR -> Filter by date
    $chart_filter_btn.on('click', function(e){
      e.preventDefault();
      var $this = $(this);
      var date_type = 'day';
      if ( $chart_date_type.children('.active').eq(0).size() > 0 ) {
        date_type = $chart_date_type.children('.active').eq(0).data('type');
      }

      $chart_graphic.removeClass('hide').addClass('hide');
      $chart_loader.removeClass('hide');

      // Get new data via AJAX
      $.ajax({
        url: $this.attr('href'),
        type: 'POST',
        cache: false,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        data: JSON.stringify({
          'from_date': $('#chart-from-date').val(),
          'to_date': $('#chart-to-date').val(),
          'date_type': date_type
        }),
        success: function(data) {
          $chart_loader.removeClass('hide').addClass('hide');
          $chart_graphic.removeClass('hide');
          var needed_width = 0;

          if ( data.error_code == 0 ) {
            // Adjunt bar chart width if there are a lot of labels to show (HORIZONTAL SCROLL)
            $chart_graphic.css('width', 'auto');
            switch ( date_type ) {
              case 'day':
                needed_width = data.labels.length * 45;
              break;

              default:
                needed_width = data.labels.length * 100;
                
              break;
            }
            
            if ( needed_width > $chart_graphic.width() ) {
              $chart_graphic.css('width', needed_width + 'px');
            }

            // Update bar chart
            chart_graphic.update({
              labels: data.labels,
              series: data.series
            });
          } else {
            alert('ERROR '+ data.error_code +': '+ data.error_msg);
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          $chart_loader.removeClass('hide').addClass('hide');
          $chart_graphic.removeClass('hide');
          console.log('Unable to filter chart');
        }
      });
    });

    // APLICAR -> Filter by date
    $compare_filter_btn.on('click', function(e){
      e.preventDefault();
      var $this = $(this);
      var date_type = 'day';
      if ( $compare_date_type.children('.active').eq(0).size() > 0 ) {
        date_type = $compare_date_type.children('.active').eq(0).data('type');
      }

      $('#compare-help').removeClass('hide').addClass('hide');
      $compare_graphic.removeClass('hide').addClass('hide');
      $compare_loader.removeClass('hide');

      // Get new data via AJAX
      $.ajax({
        url: $this.attr('href'),
        type: 'POST',
        cache: false,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        data: JSON.stringify({
          'from_date': $('#compare-from-date').val(),
          'to_date': $('#compare-to-date').val(),
          'date_type': date_type
        }),
        success: function(data) {
          $compare_loader.removeClass('hide').addClass('hide');
          $compare_graphic.removeClass('hide');

          // Scroll down to bottom of page
          $('html, body').animate({scrollTop:$(document).height()}, 'slow');

          var needed_width = 0;
          if ( data.error_code == 0 ) {
            // Adjunt bar chart width if there are a lot of labels to show (HORIZONTAL SCROLL)
            $compare_graphic.css('width', 'auto');
            switch ( date_type ) {
              case 'day':
                needed_width = data.labels.length * 45;
              break;

              default:
                needed_width = data.labels.length * 100;
                
              break;
            }
            
            if ( needed_width > $compare_graphic.width() ) {
              $compare_graphic.css('width', needed_width + 'px');
            }

            // Update bar of comparison chart
            compare_graphic.update({
              labels: data.labels,
              series: data.series
            });
          } else {
            alert('ERROR '+ data.error_code +': '+ data.error_msg);
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          $compare_loader.removeClass('hide').addClass('hide');
          $compare_graphic.removeClass('hide');
          console.log('Unable to filter comparison chart');
        }
      });
    });

    // Apply current filter when page is loading for the first time
    $chart_filter_btn.click();

    // Click on COMPARE chart button
    $('#compare-chart-btn').on('click', function(e){
      e.preventDefault();
      $('#compare-chart').removeClass('hide');
      $(this).removeClass('hide').addClass('hide');

      // Get same data type from original chart
      if ( $chart_date_type.children('.active').eq(0).size() > 0 ) {
        var current_date_type = $chart_date_type.children('.active').eq(0).data('type');
        $compare_date_type.children('.btn').removeClass('active');
        $compare_date_type.children('.btn-'+ current_date_type).addClass('active');
      }

      // Scroll down to bottom of page
      $('html, body').animate({scrollTop:$(document).height()}, 'slow');
    });
  });
})(document, window, jQuery);