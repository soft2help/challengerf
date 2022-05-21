const gulp = require('gulp');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const sourcemaps = require('gulp-sourcemaps');
const pug = require('gulp-pug');
const imagemin = require('gulp-imagemin');
const browserSync = require('browser-sync').create();


//scss to css
function style() {
  return gulp.src('theme/assets/scss/custom/*.scss', { sourcemaps: true })
      .pipe(sass({
         outputStyle: 'compressed'
      }).on('error', sass.logError))
      .pipe(autoprefixer('last 2 versions'))
      .pipe(gulp.dest('theme/assets/css', { sourcemaps: '.' })) 
      .pipe(browserSync.reload({stream: true}));
}



// pug to html
function html(){
  console.log("HTML");
  return gulp.src('theme/assets/pug/pages/challenge/*.pug')
    .pipe(pug({
      pretty: true  
    }))
    .on('error', console.error.bind(console))
  .pipe(gulp.dest('theme/challenge'))
  .pipe(browserSync.reload({
    stream: true
  }));    
}



// Watch function
function watch() {
  // browserSync.init({  
  //   proxy:'http://localhost:8080/theme/starter-kit/teste.html',
  //   port: 8080
  // });
  browserSync.init({
    server: {
        baseDir: "./",
        middleware: function (req, res, next) {
          res.setHeader('Access-Control-Allow-Origin', '*');
          next();
        }
    }
  });
  gulp.watch('theme/assets/scss/**/*.scss', style);
  gulp.watch('theme/assets/pug/pages/challenge/*.pug', html);
  gulp.watch('theme/assets/challenge/*.css').on('change', browserSync.reload);
  gulp.watch('theme/assets/css/*.css').on('change', browserSync.reload);
}

exports.style = style;
exports.html = html;
exports.watch = watch;

const build = gulp.series(watch);
gulp.task('default', build, 'browser-sync');