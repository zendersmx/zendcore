var gulp = require('gulp'), 
	concat = require('gulp-concat'), 
	uglify = require('gulp-uglify'), 
	minifycss = require('gulp-minify-css');

gulp.task('minify', [ 'minify-js', 'minify-css' ]);

gulp.task('minify-js', function() {
	gulp.src('design/assets/js/*.js')
	.pipe(concat('application.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/dist/'))
});

gulp.task('minify-css', function() {
	gulp.src('design/assets/css/*.css')
	.pipe(concat('application.css'))
	.pipe(minifycss())
	.pipe(gulp.dest('design/dist/'))
});