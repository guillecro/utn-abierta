var gulp = require('gulp');
var less = require('gulp-less');
var cleancss = require('gulp-clean-css');
var csscomb = require('gulp-csscomb');
var rename = require('gulp-rename');
var LessPluginAutoPrefix = require('less-plugin-autoprefix');

var autoprefix= new LessPluginAutoPrefix({ browsers: ["last 4 versions"] });

gulp.task('watch', function() {
    gulp.watch(['./node_modules/spectre.css/src/**/*.less','./web/less/**/*.less'], ['build']);
    //gulp.watch('./**/*.less', ['docs']);
});

gulp.task('build', function() {
    gulp.src('./web/less/*.less')
        .pipe(less({
            plugins: [autoprefix]
        }))
        .pipe(csscomb())
        .pipe(gulp.dest('./web/css'))
        .pipe(cleancss())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('./web/css'));
});

// gulp.task('docs', function() {
//     gulp.src('./docs/src/*.less')
//         .pipe(less({
//             plugins: [autoprefix]
//         }))
//         .pipe(csscomb())
//         .pipe(gulp.dest('./docs/css'));
//     gulp.src('./*.less')
//         .pipe(less({
//             plugins: [autoprefix]
//         }))
//         .pipe(csscomb())
//         .pipe(gulp.dest('./docs/dist'))
//         .pipe(cleancss())
//         .pipe(rename({
//             suffix: '.min'
//         }))
//         .pipe(gulp.dest('./docs/dist'));
// });

gulp.task('default', ['build']);
