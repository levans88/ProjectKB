<?php

    /**
     * functions.php
     *
     * Helper functions.
     */

    require_once("constants.php");

    function connectdb() {
        $conn = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        return $conn;
    }

    
    function query_insert($query, $postContent, $tagID, $postID, $tagName, $catID, $postDateTime) {

        $posts_postid = $postID;

        $conn = connectdb();
        $queries = array(
            "INSERT INTO posts (postcontent, postdatetime) VALUES ('$postContent', '$postDateTime')",
            "INSERT INTO posts_tags (posts_postid, tags_tagid) VALUES ('$posts_postid', '$tagID')",
            "INSERT INTO tags VALUES ('$tagName', '$catID')"
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


    function query_select($query, $field, $value, $tagString, $table, $catID) {
        $conn = connectdb();
        $queries = array(
            "SELECT catname FROM categories",
            "SELECT a.tagname, b.catname FROM tags a, categories b WHERE a.catid = b.catid AND b.catname = '$value'",
            "SELECT postid, postcontent FROM posts ORDER BY postid DESC",
            "SELECT tagid FROM tags WHERE tagname IN($tagString)",
            "SELECT t.tagname FROM tags as t, posts as p, posts_tags as pt WHERE pt.tags_tagid = t.tagid AND pt.posts_postid = p.postid AND p.postid = '$value'",
            "SELECT MAX($value) as MAX FROM $table",
            "SELECT MIN($value) as MIN FROM $table",
            "SELECT catid FROM categories WHERE catname = '$value'",
            "SELECT postid, postdatetime FROM posts WHERE postid = '$value'",
            "SELECT postcontent FROM posts WHERE postid = '$value'",
            "SELECT * FROM posts WHERE postid = '$value'",
            "SELECT * FROM posts_tags WHERE posts_postid = '$value'"
            );
        
        $fields = array("catname", "tagname", "postcontent", "tagid", "MAX", "MIN", "catid", "postdatetime", "postid", "posts_postid");
        
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
        $categories = query_select(0, 0, "none", "none", "none", "none");
        return $categories;
    }


    function getTags($cat, $type) {
        $tagsArray = query_select(1, 1, $cat, "none", "none", "none");

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


    function getPostContent($postID) {
        
        $postContent = array();
        
        if (is_numeric($postID)) {
            //get single postcontent field based on postID
            $postContent = query_select(9, 2, $postID, "none", "none", "none");
        }
        else if ($postID == "all") {
            //get all postcontent fields
            $postContent = query_select(2, 2, "none", "none", "none", "none");
        }

        return $postContent;
    }


    function getPostDateTime($postID) {
        $postDateTime = query_select(8, 7, $postID, "none", "none", "none");
        return $postDateTime;
    }


    function getPostTags($postID) {
        $postTags = query_select(4, 1, $postID, "none", "none", "none");
        return $postTags;
    }


    function getMaxID($table) {
        $maxIDArray = query_select(5, 4, "postid", "none", $table, "none");
        $maxID = $maxIDArray[0];
        return $maxID;
    }


    function getMinID($table) {
        $minIDArray = query_select(6, 5, "postid", "none", $table, "none");
        $minID = $minIDArray[0];
        return $minID;
    }


    function setPostContent($postID, $postContent, $postDateTime) {

        //test for existing record to determine update vs insert
        $postExist = query_select(10, 8, $postID, "none", "none", "none");
        if ($postExist) {
            //update
            query_update(0, $postID, $postContent, $postDateTime);
        }
        else
        {
            //insert and note last updated postID
            $lastID = query_insert(0, $postContent, "none", "none", "none", "none", $postDateTime);
            return $lastID;
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
        $tagids = query_select(3, 3, "none", $tagString, "none", "none");
        
        //test for existing records to determine update vs insert
        $tagExist = query_select(11, 9, $postID, "none", "none", "none");

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
            query_insert(1, "none", $tagID, $ID, "none", "none", "none");
        }
    }


    function delPost ($postID) {
        query_delete(0, $postID);
        query_delete(1, $postID);
    }

?>
