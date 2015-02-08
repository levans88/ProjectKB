<?php

//*********************Insert or update posts in database***********************
//******************************************************************************


//variables
$postID = "";
$newTagsString = "";
$newTagsArray = array();
$postContent = "";
$tagsArray = array();


//if session has "update" or "insert" there will be a write to the database
if ((sessionHas("update") || sessionHas("insert")) && !sessionHas("error")) {

    //operation will be an update using specific $postID
    if (sessionHas("update")){
        $postID = sessionHas("postid");
    }
    else {
        //operation will be an insert (no specific $postID)
        $postID = "none";
    }

    $postContent = sessionHas("postcontent");
    $postContent = htmlentities($postContent, ENT_QUOTES);

    if (sessionHas("tag_boxes")) {
        $tagsArray = sessionHas("tag_boxes");
        foreach($tagsArray as $selected) {
            
            //put selected tags into an array
            $tagsArray[] = $selected;
        }
    }

    if (sessionHas("newtags")) {
        $newTagsString = sessionHas("newtags");
        $newTagsString = rtrim($newTagsString, '; ');
        $newTagsArray = explode(';', $newTagsString);

        foreach($newTagsArray as $nt) {
            
            //combine new tags with existing selected tags
            $tagsArray[] = $nt;
        }
    }
    else {
        $newTagsString = "none";
    }

    $postDateTime = date("Y-m-d H:i:s");
    $lastID = setPostContent($postID, $postContent, $postDateTime);
    newTags($newTagsString);
    setPostTags($postID, $tagsArray, $lastID);

    //clear "update" and "insert" from $_SESSION
    giveSession("update", FALSE);
    giveSession("insert", FALSE);
    //giveSession("limit", FALSE);
}

?>