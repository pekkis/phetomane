<?php

header('Content-Type: text/html');

$name = $_GET['name'] ?? 'nobody';

echo sprintf("hello, %s", $name);
