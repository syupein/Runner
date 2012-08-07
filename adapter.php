<?php
require_once 'PutTimeLine.php';

$d = new PutTimeLine();
$d->setStart($_GET['st']);
$d->setCount($_GET['count']);
//$d->getTimelineJson($_GET['user']);
$d->testAddRealTime();