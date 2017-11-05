<?php

require __DIR__ . '/vendor/autoload.php';
require 'WikiUser.php';
require 'WikiPages.php';

$api_url = 'https://commons.wikimedia.org/w/api.php';

$wiki = new Wikimate($api_url);
$wiki->setDebugMode(true);

$user = new WikiUser('Lemessin', $wiki);
print "Error " . $user->getError() . " \n";
print "Exists       " . $user->exists() . " \n";
print "Id           " . $user->getId() . " \n";
print "Registration " . $user->getRegistration() . " \n";
$date1 = new DateTime($user->getRegistration());

$debut = new DateTime('2017-09-01T00:00:00Z');
$fin = new DateTime('2017-09-30T23:59:59Z');
if ($date1->getTimestamp() >= $debut->getTimestamp() && $date1->getTimestamp() <= $fin->getTimestamp()) {
    echo "New user\n";
} else {
    echo "Old user\n";
}
echo $date1->getTimestamp();

$pages = new WikiPages($wiki);
$pages->getFileListFromCategoryName('Images from Wiki Loves Monuments 2017 in France');

