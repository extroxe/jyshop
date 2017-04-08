// lazyload config

angular.module('app')
    /**
   * jQuery plugin config use ui-jq directive , config the js and css files that required
   * key: function name of the jQuery plugin
   * value: array of the css js file located
   */
  .constant('JQ_CONFIG', {
      easyPieChart:   ['/source/admin/vendor/jquery/charts/easypiechart/jquery.easy-pie-chart.js'],
      sparkline:      ['/source/admin/vendor/jquery/charts/sparkline/jquery.sparkline.min.js'],
      plot:           ['/source/admin/vendor/jquery/charts/flot/jquery.flot.min.js',
                          '/source/admin/vendor/jquery/charts/flot/jquery.flot.resize.js',
                          '/source/admin/vendor/jquery/charts/flot/jquery.flot.tooltip.min.js',
                          '/source/admin/vendor/jquery/charts/flot/jquery.flot.spline.js',
                          '/source/admin/vendor/jquery/charts/flot/jquery.flot.orderBars.js',
                          '/source/admin/vendor/jquery/charts/flot/jquery.flot.pie.min.js'],
      slimScroll:     ['/source/admin/vendor/jquery/slimscroll/jquery.slimscroll.min.js'],
      sortable:       ['/source/admin/vendor/jquery/sortable/jquery.sortable.js'],
      nestable:       ['/source/admin/vendor/jquery/nestable/jquery.nestable.js',
                          '/source/admin/vendor/jquery/nestable/nestable.css'],
      filestyle:      ['/source/admin/vendor/jquery/file/bootstrap-filestyle.min.js'],
      slider:         ['/source/admin/vendor/jquery/slider/bootstrap-slider.js',
                          '/source/admin/vendor/jquery/slider/slider.css'],
      chosen:         ['/source/admin/vendor/jquery/chosen/chosen.jquery.min.js',
                          '/source/admin/vendor/jquery/chosen/chosen.css'],
      TouchSpin:      ['/source/admin/vendor/jquery/spinner/jquery.bootstrap-touchspin.min.js',
                          '/source/admin/vendor/jquery/spinner/jquery.bootstrap-touchspin.css'],
      wysiwyg:        ['/source/admin/vendor/jquery/wysiwyg/bootstrap-wysiwyg.js',
                          '/source/admin/vendor/jquery/wysiwyg/jquery.hotkeys.js'],
      dataTable:      ['/source/admin/vendor/jquery/datatables/jquery.dataTables.min.js',
                          '/source/admin/vendor/jquery/datatables/dataTables.bootstrap.js',
                          '/source/admin/vendor/jquery/datatables/dataTables.bootstrap.css'],
      vectorMap:      ['/source/admin/vendor/jquery/jvectormap/jquery-jvectormap.min.js',
                          '/source/admin/vendor/jquery/jvectormap/jquery-jvectormap-world-mill-en.js',
                          '/source/admin/vendor/jquery/jvectormap/jquery-jvectormap-us-aea-en.js',
                          '/source/admin/vendor/jquery/jvectormap/jquery-jvectormap.css']/*,
      footable:       ['/source/admin/vendor/jquery/footable/footable.all.min.js',
                          '/source/admin/vendor/jquery/footable/footable.core.css']*/
      }
  )
  // oclazyload config
  .config(['$ocLazyLoadProvider', function($ocLazyLoadProvider) {
      // We configure ocLazyLoad to use the lib script.js as the async loader
      $ocLazyLoadProvider.config({
          debug:  false,
          events: true,
          modules: [
              {
                  name: 'ngGrid',
                  files: [
                      '/source/admin/vendor/modules/ng-grid/ng-grid.min.js',
                      '/source/admin/vendor/modules/ng-grid/ng-grid.min.css',
                      '/source/admin/vendor/modules/ng-grid/theme.css'
                  ]
              },
              {
                  name: 'ui.select',
                  files: [
                      '/source/admin/vendor/modules/angular-ui-select/select.min.js',
                      '/source/admin/vendor/modules/angular-ui-select/select.min.css'
                  ]
              },
              {
                  name:'angularFileUpload',
                  files: [
                    '/source/admin/vendor/modules/angular-file-upload/angular-file-upload.min.js'
                  ]
              },
              {
                  name:'ui.calendar',
                  files: ['/source/admin/vendor/modules/angular-ui-calendar/calendar.js']
              },
              {
                  name: 'ngImgCrop',
                  files: [
                      '/source/admin/vendor/modules/ngImgCrop/ng-img-crop.js',
                      '/source/admin/vendor/modules/ngImgCrop/ng-img-crop.css'
                  ]
              },
              {
                  name: 'angularBootstrapNavTree',
                  files: [
                      '/source/admin/vendor/modules/angular-bootstrap-nav-tree/abn_tree_directive.js',
                      '/source/admin/vendor/modules/angular-bootstrap-nav-tree/abn_tree.css'
                  ]
              },
              {
                  name: 'toaster',
                  files: [
                      '/source/admin/vendor/modules/angularjs-toaster/toaster.js',
                      '/source/admin/vendor/modules/angularjs-toaster/toaster.css'
                  ]
              },
              {
                  name: 'textAngular',
                  files: [
                      '/source/admin/vendor/modules/textAngular/textAngular-sanitize.min.js',
                      '/source/admin/vendor/modules/textAngular/textAngular.min.js'
                  ]
              },
              {
                  name: 'vr.directives.slider',
                  files: [
                      '/source/admin/vendor/modules/angular-slider/angular-slider.min.js',
                      '/source/admin/vendor/modules/angular-slider/angular-slider.css'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular',
                  files: [
                      '/source/admin/vendor/modules/videogular/videogular.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.controls',
                  files: [
                      'vendor/modules/videogular/plugins/controls.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.buffering',
                  files: [
                      '/source/admin/vendor/modules/videogular/plugins/buffering.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.overlayplay',
                  files: [
                      '/source/admin/vendor/modules/videogular/plugins/overlay-play.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.poster',
                  files: [
                      '/source/admin/vendor/modules/videogular/plugins/poster.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.imaads',
                  files: [
                      '/source/admin/vendor/modules/videogular/plugins/ima-ads.min.js'
                  ]
              }
          ]
      });
  }])
;