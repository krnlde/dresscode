<?php
(version_compare(PHP_VERSION, '5.3.0') >= 0) or die('PHP 5.3+ required in order to run Mocovi.');

require 'bootstrap.php';

$application->getRouter()->handleRequests();

// no code will be executed below this statement, since the parser will exit
// earlier because of the Response::end() method which is eventually called.