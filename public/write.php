<?php

//*********************Insert or update posts in database**********************
//*****************************************************************************


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
    }

    //get any new tags that were entered
    if (sessionHas("newtags")) {
        $newTagsString = sessionHas("newtags");

        $newTagsArray = array();
        $newTagsArray = newTags($newTagsString);

        //combine new tags with existing selected tags
        foreach($newTagsArray as $nt) {
            $tagsArray[] = $nt;
        }
    }

    //add new tags to the database if there were any
    newTags($newTagsArray);

    //get current date and time
    $postDateTime = date("Y-m-d H:i:s");
    
    //write post as insert or update
    setPost($postID, $postContent, $postDateTime, $tagsArray);

    //clear "update" and "insert" from $_SESSION
    giveSession("update", FALSE);
    giveSession("insert", FALSE);
}

?>