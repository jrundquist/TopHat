<?php

global $__profiler;
$__profiler->start();

router::route();

$__profiler->stop();
$__profiler->display();


exit;
?>
