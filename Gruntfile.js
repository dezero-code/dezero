module.exports = function(grunt) {

    // Load NPM Tasks
    require('load-grunt-tasks')(grunt);

    // Displays the elapsed execution time of grunt tasks
    require('time-grunt')(grunt);

    // 1. All configuration goes here
    grunt.initConfig({

        // Store your Package file so you can reference its specific data whenever necessary
        pkg: grunt.file.readJSON('package.json'),

        // Configurable paths
        paths: {
            // Dezero dezero
            dezero_assets: 'assets',
            dezero_sass:   'assets/scss',
            dezero_css:    'assets/css',
            dezero_js:     'assets/js'
        },

        // Run: `grunt watch` from command line for this section to take effect
        watch: {
            dezero_css: {
                tasks: ['sass:dezero', 'concat:all_css'],
                files: ['<%= paths.dezero_sass %>/**/*.scss']
            },
        },

        // Javascript
        concat: {
            all_css: {
                src: [
                    '<%= paths.dezero_assets %>/libraries/_remark/global/css/bootstrap.min.css',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/css/bootstrap-extend.min.css',
                    '<%= paths.dezero_assets %>/libraries/_remark/assets/css/site.min.css',
                    '<%= paths.dezero_assets %>/libraries/animsition/animsition.min.css',
                    '<%= paths.dezero_assets %>/libraries/jquery-mmenu/jquery-mmenu.min.css',
                    '<%= paths.dezero_assets %>/libraries/select2/select2.min.css',
                    '<%= paths.dezero_assets %>/libraries/asscrollable/asScrollable.min.css',
                    '<%= paths.dezero_assets %>/libraries/pnotify/jquery.pnotify.min.css',
                    '<%= paths.dezero_assets %>/libraries/bootstrap-datepicker/bootstrap-datepicker.min.css',
                    '<%= paths.dezero_assets %>/libraries/bootstrap-touchspin-4/jquery.bootstrap-touchspin.min.css',
                    '<%= paths.dezero_assets %>/libraries/bootstrap-tokenfield/bootstrap-tokenfield.min.css',
                    '<%= paths.dezero_assets %>/libraries/slidepanel/slidePanel.min.css',
                    '<%= paths.dezero_assets %>/fonts/font-awesome/font-awesome.min.css',
                    '<%= paths.dezero_assets %>/fonts/web-icons/dz-web-icons.min.css',
                    '<%= paths.dezero_css %>/style.min.css'
                ],
                dest: '<%= paths.dezero_css %>/dezero-core.min.css'
            },
            all_js: {
                src: [
                    '<%= paths.dezero_assets %>/libraries/babel-external-helpers/babel-external-helpers.js',
                    '<%= paths.dezero_assets %>/libraries/tether/tether.min.js',
                    '<%= paths.dezero_assets %>/libraries/bootstrap/bootstrap.min.js',
                    '<%= paths.dezero_assets %>/libraries/animsition/animsition.min.js',
                    '<%= paths.dezero_assets %>/libraries/mousewheel/jquery.mousewheel.min.js',
                    '<%= paths.dezero_assets %>/libraries/asscrollbar/jquery-asScrollbar.min.js',
                    '<%= paths.dezero_assets %>/libraries/asscrollable/jquery-asScrollable.min.js',
                    '<%= paths.dezero_assets %>/libraries/jquery-mmenu/jquery.mmenu.min.all.js',
                    '<%= paths.dezero_assets %>/libraries/select2/select2.full.min.js',
                    '<%= paths.dezero_assets %>/libraries/scrollto/jquery.scrollTo.min.js',
                    '<%= paths.dezero_assets %>/libraries/bootbox/jquery.bootbox.min.js',
                    '<%= paths.dezero_assets %>/libraries/pnotify/jquery.pnotify.min.js',
                    '<%= paths.dezero_assets %>/libraries/bootstrap-datepicker/bootstrap-datepicker.min.js',
                    '<%= paths.dezero_assets %>/libraries/bootstrap-datepicker/bootstrap-datepicker.es.min.js',
                    '<%= paths.dezero_assets %>/libraries/bootstrap-touchspin-4/jquery.number.min.js',
                    '<%= paths.dezero_assets %>/libraries/bootstrap-touchspin-4/jquery.bootstrap-touchspin.min.js',
                    '<%= paths.dezero_assets %>/libraries/bootstrap-tokenfield/bootstrap-tokenfield.min.js',
                    '<%= paths.dezero_assets %>/libraries/matchheight/jquery.matchHeight-min.js',
                    '<%= paths.dezero_assets %>/libraries/slidepanel/jquery-slidePanel.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/js/State.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/js/Component.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/js/Plugin.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/js/Base.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/js/Config.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/assets/js/Section/Menubar.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/assets/js/Section/Sidebar.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/assets/js/Section/PageAside.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/assets/js/Section/GridMenu.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/js/config/colors.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/assets/js/config/tour.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/assets/js/Site.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/js/Plugin/tabs.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/js/Plugin/asscrollable.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/js/Plugin/select2.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/js/Plugin/bootstrap-datepicker.min.js',
                    '<%= paths.dezero_assets %>/libraries/_remark/global/js/Plugin/bootstrap-touchspin-4.js',
                    '<%= paths.dezero_assets %>/js/dz.ajaxgrid.js',
                    '<%= paths.dezero_assets %>/js/dz.gridview.js',
                    '<%= paths.dezero_assets %>/js/dz.slidePanel.js',
                    '<%= paths.dezero_assets %>/js/dz.slideTable.js',
                    '<%= paths.dezero_assets %>/js/dz.fileStatusTable.js',
                    '<%= paths.dezero_assets %>/js/scripts.js',
                ],
                dest: '<%= paths.dezero_js %>/dezero-core.js'
            }
        },

        uglify: {
            all_js: {
                compress: {
                    ie8: false,
                    sequences: true,
                    //distperties: true,
                    dead_code: true,
                    drop_debugger: true,
                    comparisons: true,
                    conditionals: true,
                    evaluate: true,
                    booleans: true,
                    loops: true,
                    unused: true,
                    hoist_funs: true,
                    if_return: true,
                    join_vars: true,
                    cascade: true,
                    //negate_iife: true,
                    drop_console: true
                },

                files: {
                    '<%= paths.dezero_js %>/dezero-core.min.js' : ['<%= paths.dezero_js %>/dezero-core.js'],
                },
            },
        },

        // SASS
        sass: {
            dezero: {
                options: {
                    style: 'compressed',
                    sourcemap: 'none'
                },
                files: {
                    // '<%= paths.dezero_css %>/modules/asset.css': '<%= paths.dezero_sass %>/modules/asset.scss',
                    // '<%= paths.dezero_css %>/modules/category.css': '<%= paths.dezero_sass %>/modules/category.scss',
                    // '<%= paths.dezero_css %>/modules/settings.css': '<%= paths.dezero_sass %>/modules/settings.scss',
                    // '<%= paths.dezero_css %>/modules/user.css': '<%= paths.dezero_sass %>/modules/user.scss',
                    // '<%= paths.dezero_css %>/modules/web.css': '<%= paths.dezero_sass %>/modules/web.scss',
                    '<%= paths.dezero_css %>/style.min.css': '<%= paths.dezero_sass %>/style.scss',
                }
            }
        }
    });

    // 3. Where we tell Grunt what to do when we type "grunt" into the terminal
    // > grunt
    grunt.registerTask('default', [
        'watch',
    ]);

    // Watch dezero files
    // > grunt dezero
    grunt.registerTask('dezero', [
        'watch:dezero_css',
    ]);

    // Watch dezero files
    // > grunt sass_dezero
    grunt.registerTask('sass_dezero', [
        'sass:dezero',
    ]);

    // Unify all dezero CSS & Javascript files
    // > grunt all
    grunt.registerTask('all', [
        'concat:all_css',
        'concat:all_js',
        'uglify:all_js'
    ]);
};
