<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use IrfanTOOR\TemplateEngine;

$te = new TemplateEngine(['base_path' => __DIR__ . '/']);
echo $te->processFile('example.tplt');
