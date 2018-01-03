<?php

require __DIR__ . '/vendor/autoload.php';
require 'WikiUser.php';
require 'WikiPages.php';
require 'WikiRevision.php';
require 'WikiRevisions.php';
require 'heritage.php';
require 'MonumentsDb.php';
require 'ExifData.php';
require 'LocationService.php';



$ls=new LocationService('fr');
$data=$ls->getAdm2(43.775811,4.831386);
echo "$data \n";
die();

echo removeWikiCode("[[Bibliothèque]]") . "\n";
die();

$mdb=new MonumentsDb();
$mdb->setDebugMode(true);
$result=$mdb->searchById('PA00090753', 'fr');
print_r($result);


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



$title="Saint-Dié-des-Vosges - poterne ancien château.jpg"; //Canon EOS 6D 
//$title ="Appartement_Diane_Ancy-le-Franc_12.jpg"; // Canon PowerShot S120 
//$title="Village martyr d'Oradour-sur-Glane 10.jpg"; // iPhone 6s 
//$title="Chapelle_de_la_Vieille_Charité_Marseille_2017.jpg"; // Canon PowerShot G9 X 
//$title="Vue de la façade du château du Rivau depuis le conservatoire des légumes.jpg"; // DMC-FZ1000 
//$title="Aulnay 3.jpg"; // Canon PowerShot G16 
$file = $wiki->getFile($title);
$metadata=$file->getCommonMetadata();
$exif=new ExifData($metadata);
echo "$exif->Make $exif->Model \n";
if (isset($exif->Lens )) echo "$exif->Lens \n";
if (isset($exif->Software )) echo "$exif->Software \n";
die();


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

function removeWikiCode($text) {
    echo "DDD1 $text \n";
    if (preg_match('/^\[\[(.*)\]\](.*)$/', $text, $matches, PREG_OFFSET_CAPTURE) === 1) {
        $text=$matches[1][0];
    }
    
    echo "DDD2 $text \n";
    if (preg_match('/^(.*)[\|#].*$/', $text, $matches, PREG_OFFSET_CAPTURE) !== 1) 
        return $text;
    
    return $matches[1][0];
}
