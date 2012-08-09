<?php
require_once 'PutTimeLine.php';

$d = new PutTimeLine();
if (isset($_GET['st']) && isset($_GET['count']) && isset($_GET['user']) ) {
	$d->setStart(htmlspecialchars($_GET['st'], ENT_QUOTES));
	$d->setCount(htmlspecialchars($_GET['count'], ENT_QUOTES));
	$d->getTimelineJson(htmlspecialchars($_GET['user'], ENT_QUOTES));
	//$d->testAddRealTime();
} else {
	$d->testAddRealTime();
}