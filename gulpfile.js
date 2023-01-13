const gulp = require('gulp'),
    autoprefixer = require('autoprefixer'),
    composer = require('gulp-uglify/composer'),
    concat = require('gulp-concat'),
    cssnano = require('cssnano'),
    footer = require('gulp-footer'),
    format = require('date-format'),
    header = require('gulp-header'),
    postcss = require('gulp-postcss'),
    rename = require('gulp-rename'),
    replace = require('gulp-replace'),
    sass = require('gulp-sass')(require('sass')),
    uglifyjs = require('uglify-js'),
    uglify = composer(uglifyjs, console),
    pkg = require('./_build/config.json');

const banner = '/*!\n' +
    ' * <%= pkg.name %> - <%= pkg.description %>\n' +
    ' * Version: <%= pkg.version %>\n' +
    ' * Build date: ' + format("yyyy-MM-dd", new Date()) + '\n' +
    ' */';
const year = new Date().getFullYear();

const scriptsMgr = function () {
    return gulp.src([
        'source/js/mgr/backupmodx.js',
        'source/js/mgr/helper/util.js'
    ])
        .pipe(concat('backupmodx.min.js'))
        .pipe(uglify())
        .pipe(header(banner + '\n', {pkg: pkg}))
        .pipe(gulp.dest('assets/components/backupmodx/js/mgr/'))
};
gulp.task('scripts', gulp.series(scriptsMgr));

const sassMgr = function () {
    return gulp.src([
        'source/sass/mgr/backupmodx.scss'
    ])
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([
            autoprefixer()
        ]))
        .pipe(gulp.dest('source/css/mgr/'))
        .pipe(concat('backupmodx.css'))
        .pipe(postcss([
            cssnano({
                preset: ['default', {
                    discardComments: {
                        removeAll: true
                    }
                }]
            })
        ]))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(footer('\n' + banner, {pkg: pkg}))
        .pipe(gulp.dest('assets/components/backupmodx/css/mgr/'))
};
gulp.task('sass', gulp.series(sassMgr));

const imagesMgr = function () {
    return gulp.src('./source/img/**/*.+(png|jpg|gif|svg)')
        .pipe(gulp.dest('assets/components/backupmodx/img/'));
};
gulp.task('images', gulp.series(imagesMgr));

const bumpCopyright = function () {
    return gulp.src([
        'core/components/backupmodx/model/backupmodx/backupmodx.class.php',
        'core/components/backupmodx/src/BackupMODX.php',
    ], {base: './'})
        .pipe(replace(/Copyright 2015(-\d{4})? by/g, 'Copyright ' + (year > 2015 ? '2015-' : '') + year + ' by'))
        .pipe(gulp.dest('.'));
};
const bumpVersion = function () {
    return gulp.src([
        'core/components/backupmodx/src/BackupMODX.php',
    ], {base: './'})
        .pipe(replace(/version = '\d+\.\d+\.\d+[-a-z0-9]*'/ig, 'version = \'' + pkg.version + '\''))
        .pipe(gulp.dest('.'));
};
const bumpWidget = function () {
    return gulp.src([
        'source/js/mgr/backupmodx.js',
    ], {base: './'})
        .pipe(replace(/&copy; 2015(-\d{4})?/g, '&copy; ' + (year > 2015 ? '2015-' : '') + year))
        .pipe(gulp.dest('.'));
};
gulp.task('bump', gulp.series(bumpCopyright, bumpVersion, bumpWidget));

gulp.task('watch', function () {
    // Watch .js files
    gulp.watch(['./source/js/**/*.js'], gulp.series('scripts'));
    // Watch .scss files
    gulp.watch(['./source/sass/**/*.scss'], gulp.series('sass'));
    // Watch *.(png|jpg|gif|svg) files
    gulp.watch(['./source/img/**/*.(png|jpg|gif|svg)'], gulp.series('images'));
});

// Default Task
gulp.task('default', gulp.series('bump', 'scripts', 'sass', 'images'));
