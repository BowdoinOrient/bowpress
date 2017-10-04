var gulp = require('gulp');
var sass = require('gulp-sass');
var minifyCss = require('gulp-clean-css');
var autoprefixer = require('gulp-autoprefixer');

gulp.task('default', function() {


});

gulp.task('sass', function () {
  setTimeout(function() {
  return gulp.src('./sass/style.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['> 2%'],
      cascade: false
    }))
    .pipe(minifyCss({
        keepSpecialComments: 1
    }))
    .pipe(gulp.dest('./'));
  }, 100);
});

gulp.task('watch', ['sass'], function () {
    gulp.watch('./sass/**/*.scss', ['sass'] );
});
