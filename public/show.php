<?php

//******************Display posts to the page***************
//**********************************************************


//variables
$limit = 0;
$tagName = "";
//$postIDs = array();

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
	$data = find($searchTerm, $limit);
}
elseif (sessionHas("edit")) {
	$data = getPosts(sessionHas("postid"), "none", $limit);
}
else {
	$data = getPosts("any", $tagName, $limit);
}

echo "<div id='post-table'>";


//loop through posts
if ($data) {
	foreach ($data as $post) {

		$postID = $post[0];

		//get the post's content and convert new lines to line breaks
		$postContent = nl2br($post[1]);	
		
		//get the post's tags
		$postTagsString = $post[3];

		//if there were no tags then provide empty string
		if (!($postTagsString)) {
			$postTagsString = "";
		}

		//convert $postTagsString to an array
		$postTags = explode(",", $postTagsString);

		//get the post's date/time
		$postDateTime = $post[2];
		
		//remove time from post's date/time and format the date
		$postDateArray = explode(' ', $postDateTime);
		$postDate = $postDateArray[0];
		$postDateParts = explode('-', $postDate);
		$postDate = $postDateParts[1] . "-" . $postDateParts[2] . "-" . $postDateParts[0];

		//and now display everything
		echo "<div class='post-row'>";
		echo "<div class='post-date-time'>" . $postDate . "</div>";
		
		//***keep*** - not currently implemented
		//$postTitle = autoPostTitle($postContent);
		//echo "<div class='post-title'>" . $postTitle . "</div>";

		if ($devMode) {
			echo "<div style='float: right;'>";
			echo $post[0];
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
					echo "<input type='submit' class='tag-bottom-submit' value='*'>";
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