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
$pages->getFileListFromCategoryName(WLM_CATEGORY,1);
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
            continue;
        } else {
            print "Heritage id $idh found in rev {$rev->timestamp} for $title\n"; 
        }
     } else { 
        print "Heritage id $idh for $title\n"; 
    }
    
    $file = $wiki->getFile(substr($title,5)); // get rid of File: in name
    
    $username=$file->getUser();
    $user = new WikiUser($username, $wiki);
    if (!$user->exists()) continue;
    $id_user=updateUser($user);
    
    $monument=getMonument($idh,COUNTRY,LANG);
    if (!$monument) {
        print "ERR Monument $idh not found in monumentsdb \n";
        continue;
    }
    
    $id_monument=updateMonument($monument);
}

/**
 * Looks foor user in database and add it if not present
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
    $monument->name        = $monumentdb->name;   
    $monument->municipality= $monumentdb->municipality;   
    $monument->adm_level   = $monumentdb->adm2;   
    $monument->lat         = $monumentdb->lat;   
    $monument->lon         = $monumentdb->lon;   
    $monument->wikidata    = $monumentdb->wd_item;   
    $monument->commonscat  = $monumentdb->commonscat;   

    $monument->save();
    
    return $monument->id;
    
} 