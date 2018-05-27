<?php
require_once "../lib/Mobile-Detect-2.8.31/Mobile_Detect.php";
$detect = new Mobile_Detect;
echo "{$detect->isMobile()}";
exit();