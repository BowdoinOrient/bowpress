var gulp = require("gulp");
var sass = require("gulp-sass");
var minifyCss = require("gulp-clean-css");
var autoprefixer = require("gulp-autoprefixer");

gulp.task("sass", function(done) {
  setTimeout(function() {
    return gulp
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
      .pipe(gulp.dest("./"));
  }, 100);
  done();
});

gulp.task(
  "default",
  gulp.series("sass", function(done) {
    done();
  })
);

gulp.task(
  "watch",
  gulp.series("sass", function() {
    gulp.watch("./sass/**/*.scss", ["sass"]);
  })
);
