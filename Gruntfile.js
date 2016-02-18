module.exports = function (grunt) {

  var pkg = grunt.file.readJSON('package.json');

  var distFiles =  [
    '**',
    '!**/.*',
    '!apigen.neon',
    '!assets/**',
    '!bin/**',
    '!build/**',
    '!bootstrap.php',
    '!CHANGELOG.md',
    '!composer.json',
    '!composer.lock',
    '!Gruntfile.js',
    '!node_modules/**',
    '!package.json',
    '!phpdoc.dist.xml',
    '!phpunit.xml',
    '!phpunit.xml.dist',
    '!README.md',
    '!svn/**',
    '!tests/**',
  ];

  // Project configuration
  grunt.initConfig({

    pkg: pkg,

    exec: {
      'install-wp-tests': {
        cmd: './bin/install-wp-tests.sh wordpress_unit_tests external external 192.168.50.4',
        exitCode: [0, 255]
      }
    },

    phpunit: {
      tests: {
        dir: 'tests/'
      },
      options: {
        bin: 'vendor/bin/phpunit',
      }
    },

    checktextdomain: {
      options: {
        text_domain:    'redmine-embed',
        correct_domain: false,
        keywords:       [
          '__:1,2d',
          '_e:1,2d',
          '_x:1,2c,3d',
          'esc_html__:1,2d',
          'esc_html_e:1,2d',
          'esc_html_x:1,2c,3d',
          'esc_attr__:1,2d',
          'esc_attr_e:1,2d',
          'esc_attr_x:1,2c,3d',
          '_ex:1,2c,3d',
          '_n:1,2,4d',
          '_nx:1,2,4c,5d',
          '_n_noop:1,2,3d',
          '_nx_noop:1,2,3c,4d',
          ' __ngettext:1,2,3d',
          '__ngettext_noop:1,2,3d',
          '_c:1,2d',
          '_nc:1,2,4c,5d'
        ]
      },
      files: {
        src: [
          'lib/**/*.php',
          'redmine-embed.php',
          'uninstall.php'
        ],
        expand: true
      }
    },

    makepot: {
      target: {
        options: {
          cwd:         '',
          domainPath:  '/languages',
          potFilename: 'redmine-embed.pot',
          mainFile:    'redmine-embed.php',
          include:     [],
          exclude:     [
            'assets/',
            'bin/',
            'build/',
            'languages/',
            'node_modules',
            'release/',
            'svn/',
            'tests/',
            'tmp',
            'vendor'
          ],
          potComments: '',
          potHeaders:  {
            poedit:                  true,
            'x-poedit-keywordslist': true,
            'language':              'en_US',
            'report-msgid-bugs-to':  'https://github.com/goblindegook/redmine-embed',
            'last-translator':       'Luís Rodrigues <hello@goblindegook.net>',
            'language-Team':         'Luís Rodrigues <hello@goblindegook.net>'
          },
          type:            'wp-plugin',
          updateTimestamp: true,
          updatePoFiles:   true,
          processPot:      null
        }
      }
    },

    clean: {
      main: [
        'build',
      ]
    },

    // Copy the plugin to build directory
    copy: {
      main: {
        expand: true,
        src:    distFiles,
        dest:   'build/redmine-embed'
      }
    },

    compress: {
      main: {
        options: {
          mode:    'zip',
          archive: './build/redmine-embed-<%= pkg.version %>.zip'
        },
        expand: true,
        src:    distFiles,
        dest:   '/redmine-embed/'
      }
    },

    wp_deploy: {
      deploy: {
        options: {
          plugin_slug: 'redmine-embed',
          build_dir:   'build/redmine-embed',
          assets_dir:  'assets',
          svn_url:     'https://plugins.svn.wordpress.org/redmine-embed'
        }
      }
    },

  });

  // Load tasks
  require('load-grunt-tasks')(grunt);

  // Register tasks
  grunt.registerTask('test', [
    'composer:install',
    'exec:install-wp-tests',
    'phpunit',
  ]);

  grunt.registerTask('pot', [
    'checktextdomain',
    'makepot',
  ]);

  grunt.registerTask('build', [
    'composer:install:no-dev',
    'composer:dump-autoload:optimize:no-dev',
    'clean',
    'copy',
    'compress',
    'composer:install',
    'composer:dump-autoload:optimize',
  ]);

  grunt.registerTask('deploy', [
    'pot',
    'build',
    'wp_deploy',
  ]);

  grunt.util.linefeed = '\n';
};
