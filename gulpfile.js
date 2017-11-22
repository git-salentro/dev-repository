// Load plugins
var gulp = require( 'gulp' ),
    sass = require( 'gulp-ruby-sass' ),
    minifycss = require( 'gulp-minify-css' ),
    rename = require( 'gulp-rename' ),
    concatCss = require( 'gulp-concat-css' ),
    concat = require( 'gulp-concat' ),
    autoprefixer = require( 'gulp-autoprefixer' ),
    uglify = require('gulp-uglify');


// Styles
gulp.task( 'styles', function() {
  return sass( 'scss/*.scss',  { style: 'expanded' } )
    .pipe( autoprefixer('last 10 versions'))
    .pipe( concatCss( 'style.css' ))
    .pipe( rename( { suffix: '.min' } ))
    .pipe( minifycss() )
    .pipe( gulp.dest( './web/assets/styles' ))
});

// Scripts
gulp.task( 'scripts', function() {
  return gulp.src( 'scripts/*.js' )
    .pipe( concat( 'main.js' ) )
    .pipe( rename( { suffix: '.min' } ))
    .pipe( uglify())
    .pipe( gulp.dest( './web/assets/scripts' ))
});

// Default task
gulp.task( 'default', function() {
    gulp.start( 'styles', 'scripts' );
});

// // Watch
// gulp.task( 'watch', function() {
//
//   // Watch .scss files
//   gulp.watch( 'scss/**/*.scss', [ 'styles' ] );
//
// });
