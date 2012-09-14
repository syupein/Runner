<?php
require_once 'PutTimeLine.php';

$d = new PutTimeLine();
if (isset($_GET['st']) && isset($_GET['count']) && isset($_GET['user']) ) {
	$d->setStart($_GET['st']);
	$d->setCount($_GET['count']);
	$d->getStream($_GET['user']);
	//$d->testAddRealTime();
} else {
	$d->testAddRealTime();
}