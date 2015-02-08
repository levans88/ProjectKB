<?php

//******************Display posts to the page***************
//**********************************************************


//variables
$limit = 0;
$tagName = "";
$postIDs = array();

//if not editing then get "limit" from $_SESSION
if (!sessionHas("edit")) {
	if (sessionHas("limit")) {
		$limit = sessionHas("limit");
	}
}
//if we are editing, override $limit to 1 but don't change $_SESSION
else {
	$limit = 1;
}

if (sessionHas("tagname")) {
	$tagName = sessionHas("tagname");
}
else {
	$tagName = "none";
}

if (sessionHas("searchterm")) {
	$searchTerm = sessionHas("searchterm");
	$postIDs = find($searchTerm, $limit);
}
elseif (sessionHas("edit")) {
	$postIDs[0] = sessionHas("postid");
}
else {
	$postIDs = getPostIDs($tagName, $limit);
}

echo "<div id='post-table'>";


//loop through posts
if ($postIDs) {
	foreach ($postIDs as $postID) {
		$postContent = getPostContent($postID, $limit);
		$postContent = nl2br($postContent[0]);	//then replace new lines with breaks

		$postTagString = "";							//initialize $postTagString
		$postTags = getPostTags($postID); //get tags based on the post id
		$postDateTimeArray = getPostDateTime($postID);	//get date and time
		$postDateTime = $postDateTimeArray[0];
		
		//remove time
		$postDateArray = explode(' ', $postDateTime);
		$postDate = $postDateArray[0];

		//format date
		$postDateParts = explode('-', $postDate);
		$postDate = $postDateParts[1] . "-" . $postDateParts[2] . "-" . $postDateParts[0];

		if (!($postTags)) {				//if there were no tags
			$postTagString = "";		//then provide empty string
		}

		//and now display everything
		echo "<div class='post-row'>";
		echo "<div class='post-date-time'>" . $postDate . "</div>";
		//$postTitle = autoPostTitle($postContent);
		//echo "<div class='post-title'>" . $postTitle . "</div>";

		if ($devMode) {
			echo "<div style='float: right;'>";
			echo $postID;
			echo "</div>";
		}

		echo "<br>";

		$postContent = autoFormatLinks($postContent);

		echo "<div class='post-content'>" . $postContent . "</div><br>";

		echo "<div class='tag-bottom-menu'>";
		foreach ($postTags as $tagName) {

			echo "<div class='tag-bottom'>";
				echo "<form action='' method='post'>";
					echo "<input type='submit' class='tag-bottom-submit' name='tagname_button' value='$tagName'>";
					echo "<input type='hidden' name='tagname' value='$tagName'>";
				echo "</form>";
			echo "</div>";
		}
			echo "<div class='tag-bottom'>";
				echo "<form action='' method='post'>";
					echo "<input type='submit' class='tag-bottom-submit' style='font-size:1.5em; vertical-align: middle;' value='*'>";
					echo "<input type='hidden' name='tagname' value='none'>";
				echo "</form>";
			echo "</div>";	//close 'tag-bottom'
		echo "</div>";	//close 'tag-bottom-menu'

		
		
		if (sessionHas("loggedin")) {
		echo "<div class='post-edit-delete'>";	
				echo "<div id='edit'>";
					echo "<form action='' method='post'>";
					echo "<input type='submit' class='edit' name='edit_button' value='Edit'>";
					
					//provides hidden $postID value to pass to $_POST
					echo "<input type='hidden' name='postid' value='$postID'>";
					echo "<input type='hidden' name='edit' value='TRUE'>";
					echo "</form>";
				echo "</div>";

				echo "<div id='delete'>";
					echo "<form action='' method='post'>";
					echo "<input type='submit' class='edit' name='delete_button' value='Delete'>";

					//provides hidden $postID value to pass to $_POST
					echo "<input type='hidden' name='postid' value='$postID'>";
					echo "<input type='hidden' name='delete' value='TRUE'>";

					echo "</form>";
				echo "</div>";
		echo "</div>";	//close 'post-edit-delete'
		}
		
		echo "</div>";	//close 'post-row'
 
	}
}
else {
	//if no results were returned...
	echo "<br><div style='padding-left: 35px; padding-bottom: 15px; font-size: 14px;'>No data available.</div>";
}
echo "</div>";	//close 'post-table'

?>