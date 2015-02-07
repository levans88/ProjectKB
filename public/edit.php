<?php

//***************Load the editing form and a post if requested****************** 
//******************************************************************************


//variables									
$postTagsArray = array();
$postID = "";
$postContent = "";


if (sessionHas("edit")) {

	//limit will be "1" if $_SESSION has "edit",
	//this is only the number of results to pull from database,
	//not the number to display, that is set in show.php
	$limit = sessionHas("limit");
	
	$postID = sessionHas("postid");

	$postContent = getPostContent($postID, $limit);
	$postContent = $postContent[0];
	$postTagsArray = getPostTags($postID);

	$buttonValue = "Update";
}
else {

	$buttonValue = "Post";
}
							
			echo "<div id='form-container'>";

			echo "<form class='newpost' action='' method='post'>";

			echo "<textarea id='postcontent' name='postcontent' placeholder='Paste or type content here...' rows='4' cols='100'>" . $postContent . "</textarea>";
		  
		  echo "<div id='tag-table'>";
			echo "<ul id='tag-list'>";

				$cats = getCats();
				foreach ($cats as $cat) {
					$tags = getTags($cat, "array");
					echo "<li>";

					foreach($tags as $tag) {
						
						if (in_array($tag, $postTagsArray)) {
							$status = 'selected';
						}
						else {
							$status = 'notselected';
						}

						if ($status == 'selected') {
							$checked = 'checked';
						}
						else {
							$checked = "";
						}
						echo "<div><input type='checkbox' class='tag-checkbox' name='tag_boxes[]' id='$tag' value='$tag' $checked>";
						echo "<label class='$status' for='$tag'>" . $tag . "</label>" . "</div></li>";
					}
				}
				echo "<div style='display: inline-block;'>";
				echo "<div class='new-tags'>new:</div>&nbsp&nbsp<input type='text' class='transparent-textbox' placeholder='ex: tag1;tag2' name='newtags' size='10'>";
				echo "</div>";
				echo "</ul>";
			echo "</div>";
		echo "<input type='submit' name='post' value='$buttonValue'/>";

		if (sessionHas("edit")) {
			echo "&nbsp&nbsp";
			echo "<input type='submit' name='cancel_button' value='Cancel'>";
			echo "<input type='hidden' name='cancel' value='TRUE'>";
		}

		//if we're already editing...
		if (sessionHas("edit")) {
			
			//resubmit "postid" so $_POST will still have it
			echo "<input type='hidden' name='postid' value='$postID'>";

			//only submits "update" to $_POST if "edit" was previously true
			echo "<input type='hidden' name='update' value='TRUE'>";

			echo "</form>";  //close main form here if editing
		}
		else {

			//unset "postid" just in case, we don't need it anyway
			echo "<input type='hidden' name='postid' value='FALSE'>";
			
			//we're not editing (not updating), so if we post from here it will be an "insert"
			echo "<input type='hidden' name='insert' value='TRUE'>";

			echo "</form>";  //close main form here if NOT editing
		}
		//postcontent, tag_boxes, and newtags will be sent from the form regardless


	echo "</div>";

echo "</header>";

?>