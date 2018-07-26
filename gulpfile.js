var gulp = require('gulp');
var browserSync = require('browser-sync');

gulp.task('reload', function () {
    browserSync.reload();
});

gulp.task('serve', function () {
    browserSync.init({
        proxy: 'http://127.0.0.1:8000/'
    })
    browserSync({
        server: 'public',
    });

    gulp.watch('templates/**/*.twig', ['reload']);
    gulp.watch('public/js/*.js', ['reload']);
    gulp.watch('public/css/*.css', ['reload']);
})

gulp.task('default', ['serve']);