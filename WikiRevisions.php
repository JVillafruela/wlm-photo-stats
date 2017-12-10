<?php
require_once 'WikiRevision.php';

class WikiRevisions extends WikiPage {
// var @array    
    var $revisions; 
    var $pageid;

    public function __construct($title, $wikimate) {
        parent::__construct($title, $wikimate);
        $this->revisions = null;
        $this->pageid =null;
    }

    // /w/api.php?action=query&format=json&prop=revisions&titles=File%3ASaint-Di%C3%A9-des-Vosges%20-%20poterne%20ancien%20ch%C3%A2teau.jpg&rvprop=ids|timestamp|flags|comment|user|content&rvlimit=max&rvdir=older
    public function getRevisions($refresh=false) {
        if ($this->invalid) return null;
        if (!$refresh && is_array($this->revisions)) return $this->revisions;

        $data = array(
            'action'  => 'query',
            'prop'    => 'revisions',
            'rvprop'  => 'ids|timestamp|flags|comment|user|content',
            'rvlimit' => 'max',
            'rvdir'   => 'older',
            'titles'  => $this->title
        );

        $r = $this->wikimate->query($data); // Run the query
        // Check for errors
        if (isset($r['error'])) {
            $this->error = $r['error']; // Set the error if there was one
            return false;
        } else {
            $this->error = null; // Reset the error status
        }

        // Get the last max (50) revisions 
        $revs = array_pop($r['query']['pages']); 
        unset($r, $data);   
        //print_r($revs);
        $this->pageid=$revs['pageid'];
        foreach($revs['revisions'] as $i => $rev) {
            $wikirev=new WikiRevision();
            $wikirev->id=$rev['revid'];
            $wikirev->user=$rev['user'];
            $wikirev->timestamp=$rev['timestamp'];
            $wikirev->comment=$rev['comment'];
            $wikirev->content=$rev['*'];
            $this->revisions[]=$wikirev;            
        }              
        
/*
        $revs : Array
(
    [pageid] => 62061749
    [ns] => 6
    [title] => File:Saint-Dié-des-Vosges - poterne ancien château.jpg
    [revisions] => Array
        (
            [0] => Array
                (
                    [revid] => 257077642
                    [parentid] => 257052690
                    [user] => VIGNERON
                    [timestamp] => 2017-09-01T09:12:20Z
                    [comment] => -[[Category:Buildings in Saint-Dié-des-Vosges]]; -[[Category:Monuments historiques in Vosges (castles)]]; +[[Category:Château de Saint-Dié-des-Vosges]] using [[Help:Gadget-HotCat|HotCat]]
                    [contentformat] => text/x-wiki
                    [contentmodel] => wikitext
                    [*] => =={{int:filedesc}}==
{{Information
|description={{Mérimée|type=inscrit|PA00107276}}
|date=2016-10-02 15:25:28
|source={{own}}
|author=[[User:Pymouss|Pymouss]]
|permission=
|other versions=
}}
{{Location|48.288437|6.950755}}
{{Wikimedia France - Sterenn}}

=={{int:license-header}}==
{{self|cc-by-sa-4.0}}

{{Wiki Loves Monuments 2017|fr}}

[[Category:Files by Pymouss]]
[[Category:Uploaded via Campaign:wlm-fr]]
[[Category:Château de Saint-Dié-des-Vosges]]
                )
         */
        
        return $this->revisions;
    }
    
    public function getLastRevision()  {
        if (is_null($this->revisions)) return null;
        return $this->revisions[0];
    }

    public function getFirstRevision()  {
        if (is_null($this->revisions)) return null;
        $nb=count($this->revisions);
        return $this->revisions[$nb-1];
    }

    public function getUser() {
        if (is_null($this->revisions)) return null;
        return $this->revisions[0]->$user;
    }

}
