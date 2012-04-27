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
	);

$_GET['debug'] = true; // @debug