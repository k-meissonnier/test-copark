var gulp = require('gulp'),
    gutil = require('gulp-util'),
    concat = require('gulp-concat'),
    plumber = require('gulp-plumber'),

    // stylesheet tools
    sass = require('gulp-sass'),
    less = require('gulp-less'),
    autoprefixer = require('gulp-autoprefixer'),
    minify = require('gulp-clean-css'),

    // javascript tools
    uglify = require('gulp-uglify'),
    eslint = require('gulp-eslint'),

    // image tools
    newer = require('gulp-newer');


var minifyIfNeeded = function _minifyIfNeeded(alwaysMinify) {
    return alwaysMinify === true || gutil.env.env === 'production' || gutil.env.env === 'preprod'
        ? minify({processImport: false})
        : gutil.noop();
};

var uglifyIfNeeded = function _uglifyIfNeeded(alwaysUglify) {
    return alwaysUglify === true || process.env.NODE_ENV === 'production' || process.env.NODE_ENV === 'preprod'
        ? uglify()
        : gutil.noop();
};

var path = {
    node: './node_modules/',
    app: {
        resources: './app/Resources/public/app/',
        output: './web/',
    }
};

var file = {
    app: {
        css: [
            path.app.resources + 'scss/main.scss',
        ],
        js: [
            path.node + 'jquery/dist/jquery.min.js',
            path.app.resources + 'js/*.js',
        ],
        font: [
            path.node + 'font-awesome/fonts/*',
        ],
        img: [
            path.app.resources + 'img/*.{jpg,jpeg,png,gif,svg}',
        ],
    },
};

// App stylesheets
gulp.task('app/stylesheet', function() {
    return gulp.src(file.app.css)
        .pipe(plumber({
            errorHandler: function (err) {
                console.log(err);
                this.emit('end');
            }
        }))
        .pipe(sass())
        .pipe(concat('main.css'))
        .pipe(autoprefixer({
            browsers: ['last 2 versions', '> 5%'],
        }))
        .pipe(minifyIfNeeded())
        .pipe(gulp.dest(path.app.output + 'css'))
        ;
});

// App javascripts
gulp.task('app/javascript', function() {
    return gulp.src(file.app.js)
        .pipe(concat('app.js'))
        .pipe(uglifyIfNeeded())
        .pipe(gulp.dest(path.app.output + 'js'));
});

gulp.task('app/image', function () {
    return gulp.src(file.app.img)
        .pipe(newer(path.app.output + 'img'))
        .pipe(gulp.dest(path.app.output + 'img'));
});

// App fonts
gulp.task('app/font', function () {
    return gulp.src(file.app.font)
        .pipe(gulp.dest(path.app.output + 'fonts'));
});

gulp.task('app', [
    'app/image',
    'app/stylesheet',
    'app/font',
    'app/javascript'
]);

// Default task
gulp.task('default', function() {
    gulp.start(['app']);
});

// Watch task
gulp.task('watch', function() {
    gulp.watch(path.app.resources + 'scss/**/*.scss', ['app/stylesheet']);
    gulp.watch(path.app.resources + 'js/*.js', ['app/javascript']);
    gulp.watch('gulpfile.js', ['default']);
});

