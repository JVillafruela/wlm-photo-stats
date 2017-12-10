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
$pages->getFileListFromCategoryName(WLM_CATEGORY,5);
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

