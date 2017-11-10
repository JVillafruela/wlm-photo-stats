<?php

class WikiPages {

    protected $wikimate = null;
    protected $error = null;
    // ids of pages 
    protected $pagelist = array();

    public function __construct($wikimate) {
        $this->wikimate = $wikimate;
    }

    // /w/api.php?action=query&format=json&list=categorymembers&cmtitle=Category%3AImages%20from%20Wiki%20Loves%20Monuments%202017%20in%20France&cmprop=ids%7Ctitle&cmtype=file&cmlimit=max&cmsort=timestamp&cmdir=ascending
    public function getFileListFromCategoryName($name,$limit=null) {
        $contName = 'cmcontinue';
        $iter = 0;
        $data = array(
            'list' => 'categorymembers',
            'cmprop' => 'ids|title',
            'cmtype' => 'file',
            'cmlimit' => 'max',
            'cmsort' => 'timestamp',
            'cmdir' => 'ascending',
            'cmtitle' => "Category:$name",
        );

        do {
            $r = $this->wikimate->query($data); // Run the query
            $iter++;
            // Check for errors
            if (isset($r['error'])) {
                $this->error = $r['error']; // Set the error if there was one
                return false;
            } else {
                $this->error = null; // Reset the error status
            }

            if (!array_key_exists('query', $r)) {
                return false;
            }

            // Add the results to the output file list.
            /*
              {
              "pageid": 62061982,
              "ns": 6,
              "title": "File:Vieux pont d'Albi 02.jpg"
              },
             */
            foreach ($r['query']['categorymembers'] as $member) {
                $title = $member['title'];
                $id = $member['pageid'];
                //echo "DDD $id $title \n";
                $this->pagelist[] = $title;
                if (!is_null($limit) && count($this->pagelist)==$limit) return true;
            }

            if (isset($r['error'])) {
                $this->error = $r['error'];
                return false;
            } else {
                $this->error = null;
            }

            $continue = isset($r['continue']) && isset($r['continue'][$contName]);
            if ($continue) {
                $data[$contName] = $r['continue'][$contName];
            }
        } while ($continue);

        return true;
    }
    
    
    public function getPageList() {
        return $this->pagelist;
    }

}
