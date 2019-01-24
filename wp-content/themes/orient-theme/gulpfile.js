var gulp = require("gulp");
var sass = require("gulp-sass");
var minifyCss = require("gulp-clean-css");
var autoprefixer = require("gulp-autoprefixer");

function style() {
  return (
    gulp
      .src("./sass/style.scss")
      .pipe(sass().on("error", sass.logError))
      .pipe(
        autoprefixer({
          browsers: ["> 2%"],
          cascade: false
        })
      )
      .pipe(
        minifyCss({
          keepSpecialComments: 1
        })
      )
      .pipe(gulp.dest("./"))
  );
}

function watch(){
  gulp.watch("./sass/**/*.scss", style);
}

exports.css = style;
exports.default = watch;