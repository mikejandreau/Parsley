/**
 * Gulp with WordPress.
 *
 * 1. Live reloads browser with BrowserSync.
 * 2. CSS: Sass to CSS, error check, Autoprefix, sourcemaps, minify, merge media queries.
 * 3. JS: Concatenates & uglifies Vendor and Custom JS files.
 * 4. Images: Minifies PNG, JPEG, GIF and SVG images.
 * 5. Watches files for changes in CSS or JS.
 * 6. Watches files for changes in PHP.
 * 7. Corrects the line endings.
 * 8. InjectCSS instead of browser page reload.
 * 9. Generates .pot file for i18n and l10n.
 *
 * Based on Ahmad Awais excellent Gulp workflow
 */

// Project configuration for gulp tasks

// START Editing Project Variables
var project                 = 'Parsley'; // Project Name.
var projectURL              = 'dev4'; // Project URL. Could be something like localhost:8888.
var productURL              = './'; // Theme/Plugin URL. Leave it like it is, since our gulpfile.js lives in the root folder.

// Translation related
var text_domain             = 'parsley'; // Your textdomain here.
var destFile                = 'parsley.pot'; // Name of the transalation file.
var packageName             = 'parsley'; // Package name.
var bugReport               = 'https://www.mikejandreau.net/contact/'; // Where can users report bugs.
var lastTranslator          = 'First Last <your_email@email.com>'; // Last translator Email ID.
var team                    = 'WPTie <your_email@email.com>'; // Team's Email ID.
var translatePath           = './languages' // Where to save the translation files.

// Style related
var styleSRC                = './assets/sass/style.scss'; // Path to main .scss file.
var styleDestination        = './'; // Places compiled CSS file in root folder, could also be './assets/css/' or some other folder, just remember to update file path in functions.php

// Admin Style related
var styleAdminSRC           = './assets/sass/login-style.scss'; // Path to main .scss file.
var styleAdminDestination   = './assets/css/'; // Path to place compiled admin CSS file

// JavaScript related
// var scriptSRC             = './assets/js/vendor/*.js'; // Path to JS folder if you don't care about concat order
var scriptSRC             = [
                              // './assets/js/vendor/jquery-2.2.4.js', // jQuery is optional
                              './assets/js/vendor/class-helpers.js', // Pure JS class toggling
                              './assets/js/vendor/skip-link-focus-fix.js', // WP skip link
                              './assets/js/vendor/prism.js', // syntax highlighter for code blocks (optional, has associated SASS file for styles/themes)
                              './assets/js/custom/*.js' // menu-controls.js, scroll-to-top.js, etc.
                            ]; // Path to JS vendor and custom files in order.
var scriptDestination     = './assets/js/'; // Path to save the compiled JS file.
var scriptFile            = 'scripts'; // Compiled JS file name.

// Images
var imagesSRC               = './assets/img/raw/**/*.{png,jpg,gif,svg}'; // Source folder of unoptimized images
var imagesDestination       = './assets/img/'; // Destination folder of optimized images

// Watch file paths
var styleWatchFiles         = './assets/sass/**/*.scss'; // Path to all *.scss files inside css folder and inside them
var styleAdminWatchFiles    = ['./assets/sass/base/*.scss', './assets/sass/login-style.scss'] ; // Path to admin SCSS file
var scriptJSWatchFiles      = ['./assets/js/vendor/*.js', './assets/js/custom/*.js']; // Path to all JS files.
var projectPHPWatchFiles    = './**/*.php'; // Path to all PHP files.

// Browsers we care about for autoprefixing
const AUTOPREFIXER_BROWSERS = [
  'last 2 versions',
  '> 1%',
  'ie >= 9',
  'ie_mob >= 10',
  'ff >= 30',
  'chrome >= 34',
  'safari >= 7',
  'opera >= 23',
  'ios >= 7',
  'android >= 4',
  'bb >= 10'
];
// STOP Editing Project Variables

// Load gulp plugins and assign semantic names
var gulp         = require('gulp'); // Gulp

// CSS plugins
var sass         = require('gulp-sass'); // Gulp pluign for Sass compilation.
var minifycss    = require('gulp-uglifycss'); // Minifies CSS files.
var autoprefixer = require('gulp-autoprefixer'); // Autoprefixing magic.
var mmq          = require('gulp-merge-media-queries'); // Combine matching media queries into one media query definition.

// JS plugins
var jshint       = require('gulp-jshint'); // Checks JS for errors
var concat       = require('gulp-concat'); // Concatenates JS files
var uglify       = require('gulp-uglify'); // Minifies JS files

// Image realted plugins
var imagemin     = require('gulp-imagemin'); // Minify PNG, JPEG, GIF and SVG images with imagemin.

// Utility plugins
var rename       = require('gulp-rename'); // Renames files E.g. style.css -> style.min.css
var lineec       = require('gulp-line-ending-corrector'); // Consistent Line Endings for non UNIX systems.
var filter       = require('gulp-filter'); // Enables you to work on a subset of the original files by filtering them using globbing.
var sourcemaps   = require('gulp-sourcemaps'); // Maps code in a compressed file
var notify       = require('gulp-notify'); // Sends message notification to you
var browserSync  = require('browser-sync').create(); // Reloads browser and injects CSS. Time-saving synchronised browser testing.
var reload       = browserSync.reload; // For manual browser reload.
var wpPot        = require('gulp-wp-pot'); // For generating the .pot file.
var sort         = require('gulp-sort'); // Recommended to prevent unnecessary changes in pot-file.

// BROWSERSYNC
// Live reload, CSS/JS injection, and localhost tunneling - http://www.browsersync.io/docs/options/
gulp.task( 'browser-sync', function() {
  browserSync.init( {
    proxy: projectURL,    // Project URL
    open: true,           // 'true' automatically opens BrowserSync live server, 'false' does not
    injectChanges: true,  // Inject CSS changes, comment it to reload browser for every CSS change
    // port: 7000,        // Use a specific port (instead of the one auto-detected by Browsersync)
  } );
});

// STYLE TASK
// Compile SCSS, add vendor prefixes, minify, save to root directory
gulp.task('styles', function () {
  gulp.src( styleSRC )
  .pipe( sourcemaps.init() )
  .pipe( sass( {
    errLogToConsole: true,
      // outputStyle: 'compact',
      // outputStyle: 'compressed',
      // outputStyle: 'nested',
      outputStyle: 'expanded',
      precision: 10
    } ) )
  .on('error', console.error.bind(console))
  .pipe( sourcemaps.write( { includeContent: false } ) )
  .pipe( sourcemaps.init( { loadMaps: true } ) )
  .pipe( autoprefixer( AUTOPREFIXER_BROWSERS ) )
  .pipe( sourcemaps.write ( './' ) ) // Write sourcemap to same folder
  .pipe( lineec() ) // Consistent Line Endings for non UNIX systems
  .pipe( gulp.dest( styleDestination ) )
  .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files
  .pipe( mmq( { log: true } ) ) // Merge Media Queries only for .min.css version
  .pipe( browserSync.stream() ) // Reloads style.css if that is enqueued
  .pipe( rename( { suffix: '.min' } ) )
  .pipe( minifycss( {
    maxLineLen: 10
  }))
  .pipe( lineec() ) // Consistent Line Endings for non UNIX systems
  .pipe( gulp.dest( styleDestination ) )
  .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files
  .pipe( browserSync.stream() ) // Reloads style.min.css if that is enqueued
  .pipe( notify( { message: 'TASK: "styles" Completed! 💯', onLast: true } ) )
});

// LOGIN SCREEN STYLE TASK
// Compile SCSS, add vendor prefixes, minify, save to assets/css directory
gulp.task('login-styles', function () {
  gulp.src( styleAdminSRC )
  .pipe( sourcemaps.init() )
  .pipe( sass( {
    errLogToConsole: true,
      // outputStyle: 'compact',
      // outputStyle: 'compressed',
      // outputStyle: 'nested',
      outputStyle: 'expanded',
      precision: 10
    } ) )
  .on('error', console.error.bind(console))
  .pipe( sourcemaps.write( { includeContent: false } ) )
  .pipe( sourcemaps.init( { loadMaps: true } ) )
  .pipe( autoprefixer( AUTOPREFIXER_BROWSERS ) )
  .pipe( sourcemaps.write ( './' ) ) // Write sourcemap to same folder
  .pipe( lineec() ) // Consistent Line Endings for non UNIX systems
  .pipe( gulp.dest( styleAdminDestination ) )
  .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files
  .pipe( mmq( { log: true } ) ) // Merge Media Queries only for .min.css version
  .pipe( browserSync.stream() ) // Reloads style.css if that is enqueued
  .pipe( rename( { suffix: '.min' } ) )
  .pipe( minifycss( {
    maxLineLen: 10
  }))
  .pipe( lineec() ) // Consistent Line Endings for non UNIX systems
  .pipe( gulp.dest( styleAdminDestination ) )
  .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files
  .pipe( browserSync.stream() ) // Reloads style.min.css if that is enqueued
  .pipe( notify( { message: 'TASK: "login-styles" Completed! 💯', onLast: true } ) )
});

// SCRIPTS TASK
// Get JS source files, error check, concat, rename, minify, save to JS folder
gulp.task( 'scripts', function() {
  gulp.src( scriptSRC )
  .pipe(jshint())
  .pipe(jshint.reporter('jshint-stylish'))
  .pipe( concat( scriptFile + '.js' ) )
  .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
  .pipe( gulp.dest( scriptDestination ) )
  .pipe( rename( {
    basename: scriptFile,
    suffix: '.min'
  }))
  .pipe( uglify() )
  .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
  .pipe( gulp.dest( scriptDestination ) )
  .pipe( notify( { message: 'TASK: "scripts" Completed! 💯', onLast: true } ) );
});

// IMAGES TASK
// Optimize images in assets/img/raw and save to assets/img - runs once, run 'gulp images' to do it again
gulp.task( 'images', function() {
  gulp.src( imagesSRC )
  .pipe( imagemin( {
    progressive: true,
    optimizationLevel: 3, // 0-7 low-high
    interlaced: true,
    svgoPlugins: [{removeViewBox: false}]
  } ) )
  .pipe(gulp.dest( imagesDestination ))
  .pipe( notify( { message: 'TASK: "images" Completed! 💯', onLast: true } ) );
});

// WP POT
// Translation file generator
gulp.task( 'translate', function () {
  return gulp.src( projectPHPWatchFiles )
  .pipe(sort())
  .pipe(wpPot( {
    domain        : text_domain,
    destFile      : destFile,
    package       : packageName,
    bugReport     : bugReport,
    lastTranslator: lastTranslator,
    team          : team
  } ))
  .pipe(gulp.dest(translatePath))
  .pipe( notify( { message: 'TASK: "translate" Completed! 💯', onLast: true } ) )
});

// WATCH TASK
// Watch files for changes and reload
gulp.task( 'default', ['styles', 'login-styles', 'scripts', 'images', 'browser-sync'], function () {
  gulp.watch( projectPHPWatchFiles, reload ); // Reload on PHP file changes.
  gulp.watch( styleWatchFiles, [ 'styles' ] ); // Reload on SCSS file changes.
  gulp.watch( styleAdminWatchFiles, [ 'login-styles' ] ); // Reload on SCSS file changes.
  gulp.watch( scriptJSWatchFiles, [ 'scripts', reload ] ); // Reload on scripts file changes.
});
