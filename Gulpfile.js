(function () {
    'use strict';

    var gulp     = require('gulp'),
        composer = require('gulp-composer'),
        readme   = require('gulp-readme-to-markdown');

    var config = {
        readme: {
            screenshot_ext: ['jpg', 'png'],
            extract: {
                'changelog': 'CHANGELOG',
                'Frequently Asked Questions': 'FAQ'
            }
        }
    };

    /**
     * composer
     */
    gulp.task('composer', function () {
        return composer();
    });

    /**
     * readme
     */
    gulp.task('readme', function () {
        return gulp.src(['README.txt'])
            .pipe(readme(config.readme))
            .pipe(gulp.dest('.'));
    });

    /**
     * default
     */
    gulp.task('default', ['composer', 'readme']);

})();