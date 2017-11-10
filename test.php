<?php

require __DIR__ . '/vendor/autoload.php';
require 'WikiUser.php';
require 'WikiPages.php';
require 'WikiRevision.php';
require 'WikiRevisions.php';
require 'heritage.php';

$h=new Heritage('fr');
//https://commons.wikimedia.org/wiki/Template:M%C3%A9rim%C3%A9e

$tests=array(
    '{{Mérimée}}',    
    '{{Mérimée|PA00090753}}',    
    '{{Mérimée|PA00090676|IA35024928}}',
    '{{Mérimée|type=inscrit|PA16000036}}',    
    '{{mérimée|type=classé|PA16000036}}',    
    '{{Mérimée|type=classé+inscrit|PA00110393}}'
);

foreach ($tests as $text) {
    $id=$h->getHeritageId($text);
    echo "$text _{$id}_\n";
}

$api_url = 'https://commons.wikimedia.org/w/api.php';

$wiki = new Wikimate($api_url);
$wiki->setDebugMode(true);


$pages = new WikiPages($wiki);
$pages->getFileListFromCategoryName('Images from Wiki Loves Monuments 2017 in France',500);
foreach ($pages->getPageList() as $i => $title) {
    // title is already prefixed by "File:"
    //$page = $wiki->getPage("$title");
    $page=new WikiRevisions("$title",$wiki);  
    $id=$h->getHeritageId($page->getText()); 
    if ($id===FALSE) {
        $revs=$page->getRevisions();
        foreach ($revs as $rev) {
            $id=$h->getHeritageId($rev->content); 
            if ($id !== FALSE) break;
        }
        if ($id===FALSE) { 
            print "Heritage id not found for $title\n";
        } else {
            print "Heritage id $id found in rev {$rev->timestamp} for $title\n"; 
        }
        // ex File:Phalsbourg (Moselle) Place d'Armes 02 MH.jpg
        //print $page->getText() . "\n\n";
     } else { 
        print "Heritage id $id for $title\n"; 
    }
}

die();

$title="Saint-Dié-des-Vosges - poterne ancien château.jpg";
$page = $wiki->getPage("File:$title");
$file = $wiki->getFile($title);
$username=$file->getUser();
echo "DDD exists ". $file->exists(). " \n"; 
echo "DDD $username \n";

$id=$h->getHeritageId($page->getText()); 
echo "DDD mérimée $id\n";
die();


$revs=new WikiRevisions("File:$title",$wiki);  
$revs->getRevisions();
print_r($revs->getFirstRevision());
print_r($revs->getLastRevision());
die();

$user = new WikiUser($username, $wiki);
print "Error " . $user->getError() . " \n";
print "Exists       " . $user->exists() . " \n";
print "Id           " . $user->getId() . " \n";
print "Registration " . $user->getRegistration() . " \n";
$date1 = new DateTime($user->getRegistration());

$debut = new DateTime('2017-09-01T00:00:00Z');
$fin = new DateTime('2017-09-30T23:59:59Z');
if ($date1->getTimestamp() >= $debut->getTimestamp() && $date1->getTimestamp() <= $fin->getTimestamp()) {
    echo "New user $username\n";
    echo $date1->getTimestamp() ."\n";
} else {
    echo "Old user $username\n";
}



die();



