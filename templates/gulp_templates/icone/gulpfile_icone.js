/* jshint node: true, laxcomma: true, esnext: true */
// gulpfile icone
// gulp [--gulpfile __gulpfile.js__]

var gulp = require('gulp')
	//,del = require('del')
	//,replace = require('gulp-replace')
	,rename = require('gulp-rename')
	//,markdown = require('gulp-markdown')
	,file = require('gulp-file')
	//,fs = require("fs")
	,chmod = require('gulp-chmod')
	,svgstore = require('gulp-svgstore')
	,svgmin = require('gulp-svgmin')
	//,addsrc = require('gulp-add-src')
	//,concat = require('gulp-concat')
;


var icon_list = []; // lista delle icone, utilizzate per il file demo

gulp.task('icone', function() {
	return gulp.src(['svg_files/*.svg'/* , '!icone/icona_da_non_minificare.svg' */])
    	.pipe(rename(function (path) {
	        path.basename = path.basename.replace(/icone_/, '');
	        //path.basename = 'icon-' + icon_name;

	        icon_list.push(path.basename);

	        return path;
	    }))
	    .pipe(svgmin(function () {
	        return {
		        // https://github.com/svg/svgo/tree/master/plugins
			    plugins: [
			    	{ cleanupIDs: { remove: true, minify: true } }
			    	, { removeDoctype: true }
			    	, { removeComments: true }
			    	, { removeTitle: true }
			    	, { removeDimensions: true }
			        , { cleanupNumericValues: { floatPrecision: 3  } }
			        , { convertColors: { names2hex: true, rgb2hex: true } }
			        , { removeAttrs: { attrs: ['(fill|stroke|class|style)', 'svg:(width|height)'] } }
			    ]
			    //,js2svg: { pretty: true }
			};
	    }))
	    //.pipe(addsrc(['icone/icona_da_non_minificare.svg']))
	    .pipe(svgstore())
	    //.pipe( replace(/<style>(.*?)<\/style>/g, '') )
	    //.pipe( replace(/<title>(.*?)<\/title>/g, '') )
    	.pipe( rename('icone.svg') )
    	.pipe(chmod(0o755))
    	.pipe(gulp.dest('./'));
});

// il secondo argomento (['icone']) indica una dipendenza
// in questo modo il task viene processato dopo gli altri
gulp.task('icon_list', ['icone'], function() {
	var str = '// lista id icone per demo\n' +
		'// NB: questo file è generato dallo script gulpfile_icone.js, eventuali modifiche saranno sovrascritte\n' +
		'var icon_list = ' + JSON.stringify(icon_list, null, " ") + ';';

	return file('icon_list.js', str, { src: true })
    	.pipe(gulp.dest('./'));
});


gulp.task('default', ['icone', 'icon_list']);
