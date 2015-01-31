<?php

    require_once("constants.php");

    function connectdb() {
        $conn = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        return $conn;
    }

    
    function query_insert($query, $postContent, $tagID, $postID, $tagName, $catID, $postDateTime, $username, $hash) {

        $posts_postid = $postID;

        $conn = connectdb();
        $queries = array(
            "INSERT INTO posts (postcontent, postdatetime) VALUES ('$postContent', '$postDateTime')",
            "INSERT INTO posts_tags (posts_postid, tags_tagid) VALUES ('$posts_postid', '$tagID')",
            "INSERT INTO tags VALUES ('$tagID', '$catID', '$tagName')",
            "INSERT INTO users (username, hash) VALUES ('$username', '$hash')"
            //echo mysqli_insert_id();
            );

        mysqli_query($conn, $queries[$query]);
        $lastID = mysqli_insert_id($conn);
        mysqli_close($conn);
        return $lastID;
    }


    function query_update($query, $postID, $postContent, $postDateTime) {
        $conn = connectdb();
        $queries = array(
            "UPDATE posts SET postcontent = '$postContent', postdatetime = '$postDateTime' WHERE postid = '$postID'"
            );
        mysqli_query($conn, $queries[$query]);
        mysqli_close($conn);
    }


    function query_delete($query, $postID) {
        $conn = connectdb();
        $queries = array(
            "DELETE FROM posts_tags WHERE posts_postid = '$postID'",
            "DELETE FROM posts WHERE postid = '$postID'"
            );
        mysqli_query($conn, $queries[$query]);
        mysqli_close($conn);
    }


    function query_select($query, $field, $value, $tagString, $table, $catID, $limit, $findQueryRemainder) {
        $conn = connectdb();
        $queries = array(
            "SELECT catname FROM categories",
            "SELECT a.tagname, b.catname FROM tags a, categories b WHERE a.catid = b.catid AND b.catname = '$value'",
            "SELECT postid, postcontent FROM posts ORDER BY postid DESC LIMIT $limit",
            "SELECT tagid FROM tags WHERE tagname IN($tagString)",
            "SELECT t.tagname FROM tags as t, posts as p, posts_tags as pt WHERE pt.tags_tagid = t.tagid AND pt.posts_postid = p.postid AND p.postid = '$value'",
            "SELECT MAX($value) as MAX FROM $table",
            "SELECT MIN($value) as MIN FROM $table",
            "SELECT catid FROM categories WHERE catname = '$value'",
            "SELECT postid, postdatetime FROM posts WHERE postid = '$value'",
            "SELECT postcontent FROM posts WHERE postid = '$value'",
            "SELECT * FROM posts WHERE postid = '$value'",
            "SELECT * FROM posts_tags WHERE posts_postid = '$value'",
            "SELECT * FROM posts WHERE postid = (SELECT MIN(postid) FROM posts WHERE postid < '$value')",
            "SELECT postid FROM posts ORDER BY postid DESC LIMIT $limit",
            "SELECT hash FROM users WHERE username = '$value'",
            "SELECT p.postid FROM tags as t, posts as p, posts_tags as pt WHERE p.postid = pt.posts_postid AND pt.tags_tagid = t.tagid AND t.tagname = '$value' LIMIT $limit",
            //partial query, completed by find function
            "SELECT DISTINCT p.postid, p.postcontent FROM tags as t, posts as p, posts_tags as pt
                WHERE p.postid = pt.posts_postid AND pt.tags_tagid = t.tagid AND " . $findQueryRemainder 
            );
        
        $fields = array("catname", "tagname", "postcontent", "tagid", "MAX", "MIN", "catid", "postdatetime", "postid", "posts_postid", "hash");
        
        $result = mysqli_query($conn, $queries[$query]);

        if (mysqli_num_rows($result) > 0) {
            
            //output data of each row
            $arrResults = array();
            while($row = mysqli_fetch_assoc($result)) {
                $arrResults[] = $row[$fields[$field]];
            }
            mysqli_close($conn);
            return $arrResults;
        }
        else return false;
    }


    function getCats() {
        $categories = query_select(0, 0, "none", "none", "none", "none", "none", "none");
        return $categories;
    }


    function getTags($cat, $type) {
        $tagsArray = query_select(1, 1, $cat, "none", "none", "none", "none", "none");

        $tagString = "";
        foreach ($tagsArray as $tag) {
            $tagString .= $tag . "&nbsp&nbsp";
        }

        if ($type === "string") {
            return $tagString;
        }
        else {
            return $tagsArray;
        }
    }


    function getPostIDs($tagName, $limit) {
        $postIDs = array();
        if ($tagName === "none") {
            $postIDs = query_select(13, 8, "none", "none", "none", "none", $limit, "none");
        }
        else {
            $postIDs = query_select(15, 8, $tagName, "none", "none", "none", $limit, "none");
        }
        return $postIDs;
    }

    function getPostContent($postID, $limit) {
        //echo $limit;
        $postContent = array();
        
        //if $postID is a specific ID it will be numeric, if it is "all" as a parameter it will not be
        if (is_numeric($postID)) {
            //get single postcontent field based on postID
            $postContent = query_select(9, 2, $postID, "none", "none", "none", "none", "none");
        }
        else if ($postID == "all") {
            //get all postcontent fields
            $postContent = query_select(2, 2, "none", "none", "none", "none", $limit, "none");
        }
        return $postContent;
    }


    function getPostDateTime($postID) {
        $postDateTime = query_select(8, 7, $postID, "none", "none", "none", "none", "none");
        return $postDateTime;
    }


    function getPostTags($postID) {
        $postTags = query_select(4, 1, $postID, "none", "none", "none", "none", "none");
        return $postTags;
    }


    function getMaxID($table) {
        $value = "";
        if ($table === "posts") {
            $value = "postid";
        }
        else {
            $value = "tagid";
        }
        //echo ", $value is: " . $value;
        $maxIDArray = query_select(5, 4, $value, "none", $table, "none", "none", "none");
        $maxID = $maxIDArray[0];
        return $maxID;
    }


    function getMinID($table) {
        $minIDArray = query_select(6, 5, "postid", "none", $table, "none", "none", "none");
        $minID = $minIDArray[0];
        return $minID;
    }


    function setPostContent($postID, $postContent, $postDateTime) {

        //test for existing record to determine update vs insert
        $postExist = query_select(10, 8, $postID, "none", "none", "none", "none", "none");
        if ($postExist) {
            //update
            query_update(0, $postID, $postContent, $postDateTime);
        }
        else
        {
            //insert and note last updated postID
            $lastID = query_insert(0, $postContent, "none", "none", "none", "none", $postDateTime, "none", "none");
            return $lastID;
        }
        
    }


    function newTags($newTagsString) {
        if ($newTagsString !== "none") {
            $newTagsArray = explode(';', $newTagsString);
            //echo $newTagsString;
            //foreach($newTagsArray as $t) {
              //  echo $t;
            //}
            foreach($newTagsArray as $tagName) {
                //echo "inserting: " . $tagName;
                //temporary over-ride so category is always 9 (none)
                $catID = 9;
                $table = "tags";
                $tagID = getMaxID($table) + 1;
                //echo "tagID: " . $tagID . "end.";
                query_insert(2, "none", $tagID, "none", $tagName, $catID, "none", "none", "none");
            }
        }  
    }


    function setPostTags($postID, $tagsArray, $lastID) {

        //make a string out of the tags array *SPECIFICALLY for INSERT* (like: 'text')
        $tagString = "";
        foreach ($tagsArray as $tag) {
            $tagString .= "'" . $tag . "'" . ", ";
        }

        //ditch the last comma, should have used join/glue
        $tagString = rtrim($tagString, ', ');

        //get the tagid's by passing tagString
        $tagids = query_select(3, 3, "none", $tagString, "none", "none", "none", "none");
        
        //test for existing records to determine update vs insert
        $tagExist = query_select(11, 9, $postID, "none", "none", "none", "none", "none");

        //if there were existing tags, choose which ID to use, the next one or the one most recently updated
        if ($tagExist) {
            $ID = $postID;
            //if updating, delete existing tags first and don't use $lastID
            query_delete(0, $postID);
        }
        else {
            $ID = $lastID;
        }

        //write tag id's to posts_tags, once for each row, using appropriate $ID
        foreach ($tagids as $tagID) {
            query_insert(1, "none", $tagID, $ID, "none", "none", "none", "none", "none");
        }
    }


    function delPost ($postID) {
        query_delete(0, $postID);
        query_delete(1, $postID);
    }


    function getNextPostID($postID) {
        $nextID = query_select(12, 8, $postID, "none", "none", "none", "none", "none");
        $nextID = $nextID[0];
        return $nextID;
    }


    function find($termString, $limit) {
        $findQueryRemainder = "";
        $termStringLength = strlen($termString);
        
        $first = substr($termString, 0, 1);
        $last = substr($termString, ($termStringLength - 1), 1);
        //echo "<br>";
        //echo $first;
        //echo $last;

        //if the first and last characters in $termString AREN'T BOTH a double quote...
        if ($first !== "\"" && $last !== "\"") {
            
            //then split $termString into array values on the space character
            $termArray = explode(' ', $termString);
            
            foreach ($termArray as $termTag) {
                $findQueryRemainder .= "t.tagname LIKE '%" . $termTag . "%' AND ";
            }
            $findQueryRemainder = rtrim($findQueryRemainder, " AND ");

            $findQueryRemainder .= " OR ";

            foreach ($termArray as $termPostContent)
            {
                $findQueryRemainder .= "p.postcontent LIKE '%" . $termPostContent . "%' AND ";
            }
            $findQueryRemainder = rtrim($findQueryRemainder, " AND ");
            //echo "<br>";
            //echo "1";
            //echo "<br>";
            //echo $findQueryRemainder;
        }
        else {
            $termString = ltrim($termString, "\"");
            $termString = rtrim($termString, "\"");
            //echo "<br>";
            //echo $termString;
            //echo "<br>";
            
            $findQueryRemainder = "t.tagname LIKE '%" . $termString . "%' OR p.postcontent LIKE '%" . $termString . "%'";
            //echo "<br>";
            //echo "2";
            //echo "<br>";
            //echo $findQueryRemainder;
        }
        $findQueryRemainder .= " LIMIT $limit";
        $postIDs = query_select(16, 8, "none", "none", "none", "none", $limit, $findQueryRemainder);
        //query_select($query, $field, $value, $tagString, $table, $catID, $limit, $findQueryRemainder)

        return $postIDs;
    }
        //"SELECT DISTINCT p.postid, p.postcontent FROM tags as t, posts as p, posts_tags as pt
        //WHERE p.postid = pt.posts_postid AND pt.tags_tagid = t.tagid AND ...t.tagname LIKE '%$termString%' ... OR ... p.postcontent LIKE '%robotics%'




    function setUserPass($username, $hash) {
        query_insert(3, "none", "none", "none", "none", "none", "none", $username, $hash);
    }


    function authenticate($username, $password) {
        
        //echo $username . $password;
        //if hashing the password with its hash as the salt returns the same hash...
        $hashAsSaltArray = query_select(14, 10, $username, "none", "none", "none", "none", "none");
        $hashAsSalt = $hashAsSaltArray[0];

        $hash = crypt($password, $hashAsSalt);
        
        //echo "hash is:   " . $hash . "<br>";
        //echo "hashAsSalt is:   " . $hashAsSalt;
        
        if ($hash === $hashAsSalt) {
            
            return TRUE;
        }
        else {

            return FALSE;
        }
    }


    function findAllSubStrings($haystack, $needle) {
        $s = 0;
        $i = 0;
    
        while(is_integer($i)) {
 
            $i = stripos($haystack, $needle, $s);
 
            if(is_integer($i)) {
                $aStrPos[] = $i;
                $s = $i + strlen($needle);
            }
        }
 
        if(isset($aStrPos)) {
            return $aStrPos;
        }
        else {
            return false;
        }
    }


    function autoFormatLinks($postContent) {

        //auto format links in posts, find "http://" and "https://" and return their start positions in arrays
        $startHttp = findAllSubStrings($postContent, "http://");
        $startHttps = findAllSubStrings($postContent, "https://");
        
        //make sure variables for http and https link positions are in one array
        $linkPositions = array();

        if ($startHttp) {
            foreach ($startHttp as $sH) {
                $linkPositions[] = $sH;
            }
        }

        if ($startHttps) {
            foreach ($startHttps as $sHs) {
                $linkPositions[] = $sHs;
            }
        }

        //if there are links in the content that were returned in $linkPositions, sort them ascendingly
        //other wise return $postContent unchanged
        if ($linkPositions) {
            sort($linkPositions);
        }
        else {
            return $postContent;
        }
            
        //get the substring at each link position from starting position until end of $postContent, store in array
        $preLinkStrings = array();
        
        foreach ($linkPositions as $lp) {
            $preLinkString = substr($postContent, $lp);
            $preLinkStrings[] = $preLinkString;
        }

        //extract link substring from each $preLinkString, store in new array
        $linkStrings = array();
        
        foreach ($preLinkStrings as $pls) {

            //TRUE means get everyghing before the character in quotes
            $linkString = stristr($pls, " ", TRUE);

            //if a space was found as the end of a link...
            if ($linkString) {
                
                //make sure that space wasn't part of a '<br />' tag indicating a new line
                if (strpos($linkString, '<br')) {
                    
                    //if the space was inside a '<br />' tag, remove the tag characters that were acquired before the space
                    $linkString = rtrim($linkString, '<br');
                }

                //if the link was at the end of a sentence, check for and remove "."
                if (substr($linkString, -1) === ".") {
                    $linkString = rtrim($linkString, ".");
                }
                $linkStrings[] = $linkString;
            }
            else {
                //if no spaces and no '<br />' tags were found, the entire string is a link and = $pls
                $linkString = $pls;
                if ($linkString) {
                    if (substr($linkString, -1) === ".") {
                        $linkString = rtrim($linkString, ".");
                    }
                    $linkStrings[] = $linkString;
                }
            }
        }

        //place all link parameters in a links array (position, the link itself, and its length)
        $links = array();
        for ($i = 0; $i < sizeof($linkStrings); $i++) {
            $links[$i] = array('position' => $linkPositions[$i], 
                                  'link' => $linkStrings[$i], 
                                'length' => strlen($linkStrings[$i]));
        }

        //assemble array to merge link tags and post text together
        $postContentArray = array();
        
        $startOpenLinkTag = "<a href='";
        $finishOpenLinkTag = "' target='_blank'>";
        $closeLinkTag = "</a>";
        
        $offset = 0;
        $c = 0;
        $linkCount = count($links);
        $text = "";

        foreach ($links as $link) {
            $c += 1;

            //capture text between links starting at $offset
            //the next parameter is the number of characters which = (link position - $offset)
            $text = substr($postContent, $offset, ($link['position'] - $offset));

            //add any text at the start of the content
            $postContentArray[] = $text;

            //add open tag to array
            $postContentArray[] = $startOpenLinkTag;
            
            //add link to tag
            $postContentArray[] = $link['link'];
            
            //finish opening link tag
            $postContentArray[] = $finishOpenLinkTag;
            
            //add link text
            $postContentArray[] = $link['link'];
            
            //close link tag
            $postContentArray[] = $closeLinkTag;

            //$offset keeps growing as: $text length + $link length
            $offset += (strlen($text) + $link['length']);

            if ($c === $linkCount) {
                $finishText = substr($postContent, $offset);
                $postContentArray[] = $finishText;
            }
        }

        $postContent = "";
        foreach ($postContentArray as $pc) {
            $postContent .= $pc;
        }

        return $postContent;
    }

    //auto format search terms in posts
    function autoFormatTerms($postContent, $termString) {

        //find terms and return starting positions in an array
        $termPositions = array();
        $termPositions[] = findAllSubStrings($postContent, $termString);

        //if there are links in the content that were returned in $linkPositions, sort them ascendingly
        //other wise return $postContent unchanged
        if ($termPositions) {
            sort($termPositions);
        }
        else {
            return $postContent;
        }

        //place position and length parameters for all terms in one array
        //$terms = array();
        $termStringLength = strlen($termString);

        //for ($i = 0; $i < sizeof($termPositions); $i++) {
            //$terms[$i] = array('position' => $termPositions[$i],
            //                       'term' => $termString, 
            //                     'length' => $termStringLength);
        //}

        //assemble array to merge terms and post text together
        $postContentArray = array();
        $openSpanTag = "<span class='highlight'>";
        $closeSpanTag = "</span>";
        
        $offset = 0;
        $c = -1;
        $termCount = count($termPositions) - 1;
        $text = "";

        foreach ($termPositions as $tp) {
            $c += 1;

            //capture text between terms starting at $offset
            //the next parameter is the number of characters which = (term position - $offset)

            //echo $postContent . $offset . $term['position'];
            //echo $postContent . $offset;
            $text = substr($postContent, $offset, ($tp[$c] - $offset));

            //add any text at the start of the content
            $postContentArray[] = $text;

            //add open tag to array
            $postContentArray[] = $openSpanTag;
            
            //add term to array
            $postContentArray[] = $termString;
            
            //add close tag to array
            $postContentArray[] = $closeSpanTag;

            //$offset keeps growing as: $text length + $term length
            $offset += (strlen($text) + $termStringLength);

            if ($c === $termCount) {
                $finishText = substr($postContent, $offset);
                $postContentArray[] = $finishText;
            }
        }

        $postContent = "";
        foreach ($postContentArray as $pc) {
            $postContent .= $pc;
        }

        return $postContent;
    }

?>
