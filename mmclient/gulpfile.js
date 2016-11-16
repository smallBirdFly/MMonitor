var gulp = require('gulp');
var browserSync = require('browser-sync');
var uglify = require('gulp-uglify');
var minifycss = require('gulp-minify-css');
var concat = require('gulp-concat');
var del = require('del');

//JS 处理
gulp.task('js', function(){
    gulp.src('./static/js/**/*.js')
        .pipe(uglify().on('error', function(e){console.log(e);}))
        .pipe(gulp.dest('./web/static/js/'));
});

//CSS
gulp.task('css', function(){
    gulp.src('./static/css/**/*.css')
        .pipe(minifycss())
        .pipe(gulp.dest('./web/static/css/'));
});

//图片
gulp.task('img', function(){
    gulp.src('./static/img/**/*')
        .pipe(gulp.dest('./web/static/img/'));
});

//其它静态文件
gulp.task('static', function(){
    gulp.src('./static/lib/**/*')
        .pipe(gulp.dest('./web/static/lib'));
});


//清空已发布的内容
gulp.task('clean', function(){
    return del('./web/static/**/*');
});

gulp.task('build', ['clean'], function(){
    gulp.run('static', 'css', 'js', 'img');
});

//开发环境
gulp.task('dev', ['clean'], function(){
    gulp.run('static', 'css', 'js', 'img');

    gulp.watch('./static/lib/**/*', function(){
        gulp.run('static');
    });

    gulp.watch('./static/js/**/*', function(){
        gulp.run('js');
    });

    gulp.watch('./static/css/**/*',function(){
        gulp.run('css');
    });

    gulp.watch('./static/img/**/*', function(){
        gulp.run('img');
    });
});

gulp.task('default', ['dev']);
