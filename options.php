<?php

$options = array
	(	'database' => array
		(	'host'		=> '127.0.0.1'
		,	'port'		=> 3306
		,	'user'		=> 'root'
		,	'password'	=> ''
		,	'name'		=> ''
		)
	,	'name' => '127.0.0.1' // enter a domain here to redirect to another application instead of using $_SERVER['SERVER_NAME'] - the default.
	// ,	'default' => '127.0.0.1'
	);

// $_GET['debug'] = true; // @debug

$options['java_path'] = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'C:\Program Files\Java\jre7\bin\java.exe' : '/usr/bin/java');
$options['yuicompressor_path'] = __DIR__.'/lib/vendor/yuicompressor.jar';