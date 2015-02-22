<?php

//*********************Manage helper functions and all database code**********************
//****************************************************************************************


    require_once("constants.php");

    function postHas($variable) {

        if (isset($_POST[$variable])) {
            
            if ($_POST[$variable] == TRUE) {
                
                $value = $_POST[$variable];
                
                return $value;
            }
        }
        else {
            return FALSE;
        }
    }


    function sessionHas($variable) {

        if (isset($_SESSION[$variable])) {
            
            if ($_SESSION[$variable] == TRUE) {
                
                $value = $_SESSION[$variable];
                
                return $value;
            }
        }
        else {
            return FALSE;
        }
    }


    function giveSession($variable, $value) {
        $_SESSION[$variable] = $value;
    }


    function query($type, $query, $field, $value, $tagString, $table, $catID, $limit, $progQuery) {
        $conn = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);

        if (!$conn) {
            echo "Failed to connect to database.";
            return;
        }

        //query type is "select"
        $select = array(
                
                array(
                    //get all categories and tags
                    "query" => "SELECT c.catname, GROUP_CONCAT(DISTINCT t.tagname ORDER BY t.tagname) as tags 
                                FROM categories as c, tags as t 
                                WHERE c.catid = t.catid 
                                GROUP BY c.catname 
                                ORDER BY c.catname ASC",

                    "paramTypes" => "none",
                        "params" => "none"
                ),
                array(
                    //get all information for each post up to $limit (id, content, datetime, and tags)
                    "query" => "SELECT p.postid, p.postcontent, p.postdatetime, GROUP_CONCAT(DISTINCT t.tagname ORDER BY t.tagname) AS tags 
                                FROM tags as t, posts as p, posts_tags as pt 
                                WHERE pt.tags_tagid = t.tagid AND pt.posts_postid = p.postid 
                                GROUP BY p.postid 
                                ORDER BY p.postdatetime DESC
                                LIMIT ?",

                    "paramTypes" => "i",
                        "params" => $limit
                ),
                array(
                    //get all information for a *specific* post (id, content, datetime, and tags)
                    "query" => "SELECT p.postid, p.postcontent, p.postdatetime, GROUP_CONCAT(DISTINCT t.tagname ORDER BY t.tagname) AS tags 
                                FROM tags as t, posts as p, posts_tags as pt 
                                WHERE pt.tags_tagid = t.tagid AND pt.posts_postid = p.postid AND p.postid = ? 
                                GROUP BY p.postid 
                                ORDER BY p.postdatetime DESC
                                LIMIT ?",

                    "paramTypes" => "ii",
                        "params" => array($value, $limit)
                ),
                array(
                    //get posts that have a specific tag
                    "query" => "SELECT p.postid, p.postcontent, p.postdatetime, GROUP_CONCAT(DISTINCT t.tagname ORDER BY t.tagname) AS tags 
                                FROM tags as t, posts as p, posts_tags as pt 
                                WHERE pt.tags_tagid = t.tagid AND pt.posts_postid = p.postid AND p.postid IN (
                                    SELECT p.postid FROM posts AS p, posts_tags AS pt, tags AS t 
                                    WHERE p.postid = pt.posts_postid AND t.tagid = pt.tags_tagid AND t.tagname = ?
                                    )
                                GROUP BY p.postid 
                                ORDER BY p.postdatetime DESC
                                LIMIT ?",

                    "paramTypes" => "si",
                        "params" => array($value, $limit)
                ),
                array(
                    //see if specific post exists by postid
                    "query" => "SELECT postid FROM posts WHERE postid = ?",

                    "paramTypes" => "i",
                        "params" => $value
                ),
                array(
                    //verify hash in users table (for logging in)
                    "query" => "SELECT hash FROM users WHERE username = ?",

                    "paramTypes" => "s",
                        "params" => $value
                )
        );

        $insert = array(

                array(
                    //insert new tags
                    "query" => "INSERT INTO tags (catid, tagname) VALUES ('9', ?)",
                    "paramTypes" => "s",
                        "params" => $value
                    ),
                array(
                    //insert postid and tagid into posts_tags table
                    "query" => "INSERT INTO posts_tags (posts_postid, tags_tagid) VALUES (?, ?)",
                    "paramTypes" => "ii",
                        "params" => array($field, $value) //****TEMP DIRTY HACK! REPLACE $field !!!****
                    ),
                array(
                    //insert new post into posts table (insert $postContent and $postDateTime)
                    "query" => "INSERT INTO posts (postcontent, postdatetime) VALUES (?, ?)",
                    "paramTypes" => "ss",
                        "params" => array($field, $value) //****TEMP DIRTY HACK! REPLACE $field !!!****
                    ),
                array(
                    //insert $username and $hash into users table (create new user)
                    "query" => "INSERT INTO users (username, hash) VALUES (?, ?)",
                    "paramTypes" => "ss",
                        "params" => array($field, $value) //****TEMP DIRTY HACK! REPLACE $field !!!****
                    )
        );

        $update = array(

                array(
                    //update posts table - $postContent, $postDateTime, $postID
                    "query" => "UPDATE posts SET postcontent = ?, postdatetime = ? WHERE postid = ?",
                    "paramTypes" => "ssi",
                        "params" => array($field, $value, $table)   //****TEMP DIRTY HACK! REPLACE $field **AND** $table !!!****
                    )
        );

        $delete = array(

                array(
                    //delete a post from posts_tags
                    "query" => "DELETE FROM posts_tags WHERE posts_postid = ?",
                    "paramTypes" => "i",
                        "params" => $value
                    ),
                array(
                    //delete a post from posts
                    "query" => "DELETE FROM posts WHERE postid = ?",
                    "paramTypes" => "i",
                        "params" => $value
                    )
        );


        //choose appropriate array of queries based on $type
        if ($type === "select") {
            $type = $select;
        }
        elseif ($type === "insert") {
            $type = $insert;
        }
        elseif ($type === "update") {
            $type = $update;
        }
        elseif ($type === "delete") {
            $type = $delete;
        }
        elseif ($type === "prog") {
            $type = $progQuery;
        }
        else {
            echo "Invalid query type.";
            return;
        }


        //subtype - //****TEMP DIRTY HACK! DO NOT USE $query FIELD FOR THIS !!!****
        $subType = "";
        if ($query === "select") {
            $subType = "select";
        }

        //prepare the statement
        if ($type !== $progQuery) {
            
            $stmt = $conn->prepare($type[$query]["query"]);
            
            if (!$stmt) {
                echo "Failed to prepare statement.";
                return;
            }
            
            //get parameters to bind
            $paramTypes = $type[$query]["paramTypes"];
            $params = $type[$query]["params"];
            
            //if $params is an array...
            if (is_array($params)) {
                
                //create array containing a reference to the $paramTypes string
                $r_paramsAndTypes[] = & $paramTypes;
                
                //add references to each parameter to the above array,
                //cannot be done using "foreach"
                for ($i = 0; $i < sizeof($params); $i++) {
                    $r_paramsAndTypes[] = & $params[$i];
                }

                //bind parameters
                call_user_func_array(array($stmt, 'bind_param'), $r_paramsAndTypes);   //$stmt->bind_param($paramTypes, $paramString);
            }
            //if $params is NOT an array...
            elseif (!is_array($params) && $params !== "none") {

                $stmt->bind_param($paramTypes, $params);

            }
        }
        //if $type is "$progQuery"...
        else {
            //escape the find query's string first,
            //then prepare a statement with no parameters
            $stmt = $conn->real_escape_string($progQuery);
            $stmt = $conn->prepare($progQuery);
        }
        
        //execute the query
        $stmt->execute();

        if ($type === $select || $subType === "select") {

            $result = $stmt->get_result();

            //make sure $resultsArray is initialized (empty)
            $resultsArray = array();
            
            //get data from each row and store it in $results array
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                $resultsArray[] = $row;
            }

            $stmt->close();
            $conn->close();

            return $resultsArray;
        }
        elseif ($type === $insert || $subType === "insert") {
            $lastID = $conn->insert_id;
            return $lastID;
        }
    }

    //get all categories and tags
    function getCats() {
        $data = query("select", 0, "none", "none", "none", "none", "none", "none", "none");

        if ($data) {
            return $data;
        }
    }


    function getPosts($postID, $tagName, $limit) {
        
        $data = array();
        
        //if $postID is a specific ID it will be numeric, if it is "any" as a parameter it will not be
        if (is_numeric($postID)) {

            //get all data for a *specific* post
            $data = query("select", 2, "none", $postID, "none", "none", "none", 1, "none");
        }
        else if ($postID == "any") {

            if ($tagName === "none") {
                
                //get all data for any posts up to $limit
                $data = query("select", 1, "none", "none", "none", "none", "none", $limit, "none");
            }
            else {
                //if there is a tag name specified, get posts with that tag only
                $data = query("select", 3, "none", $tagName, "none", "none", "none", $limit, "none");
            }

        }
        
        return $data;


    }


    function setPost($postID, $postContent, $postDateTime, $tagsArray) {
        
        $lastID = "none";

        if ($postID !== "none") {
            //make sure postid exists before attempting update
            $postExist = query("select", 4, "none", $postID, "none", "none", "none", "none", "none");

            //if post exists then update it
            if ($postExist) {
                query("update", 0, $postContent, $postDateTime, "none", $postID, "none", "none", "none");
            }
        }
        else {
            //post does not exist, so insert it and note last updated postid
            $lastID = query("insert", 2, $postContent, $postDateTime, "none", "none", "none", "none", "none");
        }

        //set tags for post including any new ones
        setPostTags($postID, $tagsArray, $lastID);
    }


    function newTags($newTags) {
        if (is_string($newTags)) {

                //remove spaces
                $newTags = str_replace(' ', '', $newTags);

                //remove trailing semi-colon if it exists
                $newTags = rtrim($newTags, ';');

                //convert to lowercase
                $newTags = strtolower($newTags);

                //
                giveSession("newTagsString", $newTags);

                //convert new tags to an array
                $newTagsArray = explode(';', $newTags);

                return $newTagsArray;
        }   
        elseif (is_array($newTags)) {
            foreach($newTags as $tagName) {
                query("insert", 0, "none", $tagName, "none", "none", "none", "none", "none");
            }
        }
    }


    function setPostTags($postID, $tagsArray, $lastID) {

        //variables
        $tagString = "";
        $tagIDs = array();

        //determine $postID
        if ($postID === "none") {
            $postID = $lastID;
        }

        //make a string out of the tags array (like: 'test1', 'test2')
        foreach ($tagsArray as $tag) {
            $tagString .= "'" . $tag . "'" . ", ";
        }

        //remove last comma
        $tagString = rtrim($tagString, ', ');

        //get the tagid's for the tag names
        $getTagIDsQuery = "SELECT tagid FROM tags WHERE tagname IN ($tagString)";

        $subType = "select";
        $tagIDs = query("prog", $subType, "none", "none", "none", "none", "none", "none", $getTagIDsQuery);
        
        //delete existing entries in posts_tags for postid (delete tags)
        query("delete", 0, "none", $postID, "none", "none", "none", "none", "none");

        //write replacement postid's and tagid's to posts_tags (write replacement tags)
        foreach ($tagIDs as $tagID) {
            query("insert", 1, $postID, $tagID[0], "none", "none", "none", "none", "none");
        }
    }


    function delPost ($postID) {
        query("delete", 0, "none", $postID, "none", "none", "none", "none", "none");
        query("delete", 1, "none", $postID, "none", "none", "none", "none", "none");
    }


    function find($termString, $limit) {
        
        $findQueryRemainder = "";
        $termStringLength = strlen($termString);
        
        $first = substr($termString, 0, 1);
        $last = substr($termString, ($termStringLength - 1), 1);

        //if the first and last characters in $termString AREN'T BOTH a double quote...
        if ($first !== "\"" && $last !== "\"") {
            
            //then split $termString into array values on the space character
            $termArray = explode(' ', $termString);
            
            //build query to look in tagname field
            $findQueryRemainder .= "t.tagname IN (SELECT tagname FROM tags WHERE ";
            foreach ($termArray as $termTag) {
                $findQueryRemainder .= "tagname LIKE " . "'%" . $termTag . "%'" . " OR ";
                //$findQueryRemainder .= "t.tagname LIKE '%" . $termTag . "%' AND ";

            }
            $findQueryRemainder = rtrim($findQueryRemainder, " OR ");

            $findQueryRemainder .= ")" . " OR ";

            //build query to look in postcontent field
            foreach ($termArray as $termPostContent)
            {
                $findQueryRemainder .= "p.postcontent LIKE '%" . $termPostContent . "%' AND ";
            }
            $findQueryRemainder = rtrim($findQueryRemainder, " AND ");

        }
        //if the first and last characters ARE BOTH double quotes...
        else {
            $termString = ltrim($termString, "\"");
            $termString = rtrim($termString, "\"");            
            $findQueryRemainder = "t.tagname LIKE '%" . $termString . "%' OR p.postcontent LIKE '%" . $termString . "%'";

        }
        $findQueryRemainder .= " ORDER BY p.postdatetime DESC LIMIT $limit";
        
        //finish building the find query
        $findQuery = "SELECT DISTINCT p.postid FROM tags as t, posts as p, posts_tags as pt
                WHERE p.postid = pt.posts_postid AND pt.tags_tagid = t.tagid AND " . $findQueryRemainder;

        //run the find query, get resulting post id's
        $subType = "select";
        $postIDs = query("prog", $subType, "none", "none", "none", "none", "none", $limit, $findQuery);


        if ($postIDs) {
            //build a list of post id's to query for, will look like: '412','534','302'
            $idString = "";
            
            foreach ($postIDs as $id) {
                $idString .= "'" . $id[0] . "'" . ",";
            }
            $idString = rtrim($idString, ",");

            //build query for second part of find,
            //can't parameterize due to variable number of post ID's
            $findQuerySecondPass = 
                "SELECT p.postid, p.postcontent, p.postdatetime, GROUP_CONCAT(DISTINCT t.tagname ORDER BY t.tagname) AS tags 
                FROM tags as t, posts as p, posts_tags as pt 
                WHERE pt.tags_tagid = t.tagid AND pt.posts_postid = p.postid AND p.postid IN ($idString) 
                GROUP BY p.postid 
                ORDER BY p.postdatetime DESC
                LIMIT $limit";

            //finally get post data for the post id's that the find query retrieved earlier
            $subType = "select";
            $data = query("prog", $subType, "none", "none", "none", "none", "none", $limit, $findQuerySecondPass);

            return $data;
        }
        else {
            return FALSE;
        }
    }

    //***keep*** - not currently implemented...
    //function setUserPass($username, $hash) {
    //    query("insert", 3, $username, $hash, "none", "none", "none", "none", "none");
    //}


    function authenticate($username, $password) {
        
        //if hashing the password with its hash as the salt returns the same hash...
        $hashAsSalt = query("select", 5, "none", $username, "none", "none", "none", "none", "none");
        $hashAsSalt = $hashAsSalt[0][0];
        $hash = crypt($password, $hashAsSalt);

        if ($hash === $hashAsSalt) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    function redirect() {
        header ('HTTP/1.1 303 See Other');
        header ('Location: ./'); 
    }


    function findAllSubStrings($haystack, $needle) {
        $s = 0;
        $i = 0;
    
        while(is_integer($i)) {
 
            $i = stripos($haystack, $needle, $s);
 
            if(is_integer($i)) {
                $stringPos[] = $i;
                $s = $i + strlen($needle);
            }
        }
 
        if(isset($stringPos)) {
            return $stringPos;
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

    
    //***keep*** - not implemented currently

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


    //***keep*** - not implemented currently
    //function autoPostTitle ($postContent) {
        
        //if (strpos($postContent, '<br'))
        ////get up to first 50 characters of first line of $postContent (unless '<br />' is encountered)
        //$postTitleCut = substr($postContent, 0, 50);

        //if first 50 characters contains http:// or https://
        //if (stripos($postTitleCut, "http://") || stripos($postTitleCut, "https://")) {

            ////start and end title before link, or if link is at the start, the title is the link
            ////...find end of link...
        //}
        //else {
            ////
        //}

    //}

?>
