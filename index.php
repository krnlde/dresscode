<?php
(version_compare(PHP_VERSION, '5.3.0') >= 0) or die('PHP 5.3+ required in order to run mocovi. Your version is PHP '.PHP_VERSION);
(class_exists('XSLTProcessor')) or die('Class XSLTProcessor is required in order to run mocovi.');

require 'bootstrap.php';

$application->Router->handleRequests();

// ATTENTION: no code will be executed below this statement, since the parser
// will exit earlier because of the Response::end() method which is eventually
// called.