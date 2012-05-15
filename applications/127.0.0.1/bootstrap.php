<?php

$this->defaultRoute = 'home';

// initialize DB Connections or something else.

$xml = new \DomDocument();
$xml->load($this->getPath()->getPath().DIRECTORY_SEPARATOR.'translation.xml');
\Mocovi\Translator::addTranslationsFromXml($xml);