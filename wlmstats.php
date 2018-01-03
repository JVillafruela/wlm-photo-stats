<?php

/* 
 * Statistics about Wiki Loves Monuments contest.
 *
 * @author Jérôme Villafruela
 * 
 */

require_once 'config.php';
require 'WikiUser.php';
require 'WikiPages.php';
require 'WikiRevision.php';
require 'WikiRevisions.php';
require 'heritage.php';
require 'ExifData.php';

$h=new Heritage(COUNTRY);

$wiki = new Wikimate(API_COMMONS);
$wiki->setDebugMode(true);


$pages = new WikiPages($wiki);
$pages->getFileListFromCategoryName(WLM_CATEGORY,10);
foreach ($pages->getPageList() as $i => $title) {
    // title is already prefixed by "File:"
    //$page = $wiki->getPage("$title");
    $page=new WikiRevisions($title,$wiki);  
    $idh=$h->getHeritageId($page->getText()); 
    if ($idh===FALSE) {
        $revs=$page->getRevisions();
        foreach ($revs as $rev) {
            $idh=$h->getHeritageId($rev->content); 
            if ($idh !== FALSE) break;
        }
        if ($idh===FALSE) { 
            print "Heritage id not found for $title\n";
        } else {
            print "Heritage id $idh found in rev {$rev->timestamp} for $title\n"; 
        }
     } else { 
        print "Heritage id $idh for $title\n"; 
    }
    
    $file = $wiki->getFile(substr($title,5)); // get rid of File: in name
    
    $username=$file->getUser();
    $user = new WikiUser($username, $wiki);
    if (!$user->exists()) {
        print "ERR User $username not found \n";
        continue;
    }
    $id_user=updateUser($user);
    
    if($idh) {
        $monument=getMonument($idh,COUNTRY,LANG);
        if (!$monument) {
            print "ERR Monument $idh not found in monumentsdb \n";
            continue;
        }
        $id_monument=updateMonument($monument);
    } else {
        $id_monument=false;
    }
    
    $id_photo=updatePhoto($page,$file,$id_monument,$id_user);
   
}

/**
 * Looks for user in database and add it if not present
 * 
 * @param WikiUser $wkuser
 * @return integer user id
 */
function updateUser(WikiUser $wkuser) {
    $user = ORM::for_table('user')->where('name', $wkuser->getName())->find_one();  
    if ($user!==false ) return $user->id;
    
    $user = ORM::for_table('user')->create();
    $user->name         = $wkuser->getName();
    $date = new DateTime($wkuser->getRegistration());
    $user->registration = $date->format("Y-m-d H:i:s");
    $user->wpid         = $wkuser->getId();
    $user->team         = 0; 
   
    $user->save();

    return $user->id;
}

/**
 * Looks up for monument in monumentdb
 * @param string $idh identifier 
 * @param string $country
 * @param string $lang
 * @return object monuments_all record or false if not found
 */
function getMonument($idh,$country,$lang) {
    $monument = ORM::for_table('monuments_all','monumentsdb')->find_one( array(
        'country' => $country,
        'lang' => $lang,
        'id' => $idh  
    ));
    
    return $monument;    
        
}

/**
 * Looks up for monument in stats database
 * if not found record is added to db 
 * @param object $monumentdb monuments_all record
 * @return mixed id or false
 */
function updateMonument($monumentdb) {
    $monument = ORM::for_table('monument')->where( array(
        'country' => $monumentdb->country,
        'lang' => $monumentdb->lang,
        'heritage_id' => $monumentdb->id  
    ))->find_one();    
    
    if($monument !== false ) return $monument->id;
    
    $monument = ORM::for_table('monument')->create();
    
    $monument->country     = $monumentdb->country;
    $monument->lang        = $monumentdb->lang;
    $monument->heritage_id = $monumentdb->id;  
    $monument->name        = removeWikiCode($monumentdb->name);   
    $monument->municipality= removeWikiCode($monumentdb->municipality);   
    $monument->adm_level   = $monumentdb->adm2;   
    $monument->lat         = $monumentdb->lat;   
    $monument->lon         = $monumentdb->lon;   
    $monument->wikidata    = $monumentdb->wd_item;   
    $monument->commonscat  = $monumentdb->commonscat;   

    $monument->save();
    
    return $monument->id;
    
} 

/*
 * [[Bibliothèque municipale de Grenoble|Bibliothèque]]<br />(ancienne bibliothèque universitaire)
 * [[Palais des sports de Grenoble|Palais des sports]]
 * [[Palais de justice de Bordeaux#La construction du tribunal de grande instance en 1998|Tribunal de grande instance de Bordeaux]]
 * [[Chapelle Saint-Maurice de Domgermain|Chapelle Saint-Maurice]]<br /><small>chapelle en totalité ainsi que ses peintures murales</small>
 */
function removeWikiCode($text) {
    if (preg_match('/^\[\[(.*)\]\](.*)$/', $text, $matches, PREG_OFFSET_CAPTURE) === 1) {
        $text=$matches[1][0];
    }
    
    if (preg_match('/^(.*)[\|#].*$/', $text, $matches, PREG_OFFSET_CAPTURE) !== 1) 
        return $text;
    
    return $matches[1][0];
}


function updatePhoto(WikiRevisions $page,WikiFile $file,$id_monument,$id_user) {
    $title=$file->getFilename();     
    $photo = ORM::for_table('photo')->where('file', $title)->find_one();  
    if ($photo!==false ) return $photo->id; 
    
    $photo = ORM::for_table('photo')->create();
    $photo->file = $title;
    $photo->wpid = null; // +++
    $photo->user_id = $id_user ? $id_user : null;
    $photo->monument_id = $id_monument ? $id_monument : null;
    $rev= $page->getFirstRevision();
    $date = new DateTime($rev->timestamp);
    $photo->date_wp =  $date->format("Y-m-d H:i:s");
    
    $metadata=$file->getCommonMetadata();
    $exif=new ExifData($metadata);
    $photo->camera_brand = isset($exif->Make) ? $exif->Make :null;
    $photo->camera_model = isset($exif->Model) ? $exif->Model :null;
    $photo->lens = isset($exif->Lens) ? $exif->Lens :null; 
    $photo->software = isset($exif->Software) ? $exif->Software :null; 
    
    if(isset($exif->DateTime)) {
        $date = new DateTime($exif->DateTime);
        $photo->date_exif=$date->format("Y-m-d H:i:s");
    }
        
    $photo->save();
    
    return $photo->id;     
}