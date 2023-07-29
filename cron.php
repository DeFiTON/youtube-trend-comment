<?php

require_once __DIR__.'/conf.php';

$TC->jap = $jap;
$TC->parseTrends();;
$TC->exportTasks();