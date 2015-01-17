<?php
require("../includes/config.php"); 
//echo '<pre>';
//print_r($_POST);
//echo '</pre>';
?>
	<html>
		<head>
			<link rel="stylesheet" type="text/css" href="css/styles.css" />
			<script type="text/javascript" src="js/scripts.js"></script>
			<title>Title</title>
		</head>

		<body>
			<div class="container">
			<header>
				<h1>Title</h1>

					<form class="newpost" action="" method="post">
						
						<?php
						//********************************this part is for loading the header and loading a post (and tags) if editing**********
						//********************************so when i hit post, edit -OR- *delete*, this code is executed on postback*******************					
							$postTagsArray = array();
							$postID = "";
							$postContent = "";
							
							if (isset($_POST["postID"])) {
								
								//get the set post id
								$postID = $_POST["postID"];

								if(isset($_POST["edit"])) {
								
								//get its contents and tags
								$postContent = getPostContent($postID);
								$postContent = $postContent[0];
								$postTagsArray = getPostTags($postID);
								}
								
								//if delete is also set, then delete the post instead
								if (isset($_POST["delete"])) {
									delPost($postID);
									//echo $postID;
									//echo "blaaaaaaaaaaaa";
								}
							}
							//else {
							//	$postContent = "";
							//}

							echo "<textarea id='pastebox' name='pastebox' rows='8' cols='100'>" . $postContent . "</textarea>";
						  
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

								echo "</ul>";
							echo "</div>";

						echo "<input type='submit' name='post' value='Post'/>";
						//***************************this is another hidden input so that i can add (keep) postID in $_POST ...IF we were editing... *****
						//***************************this makes sure the same postid gets edited and we're not doing an insert****************************
							if ($postID == TRUE) {
								//echo "postID was set/true";
								echo "<input type='hidden' name='postID' value='$postID'>";
							}
						?>
						<br>
					</form>
			</header>
			<div id='post-container'>
			<?php

				//*********************this is for writing posts to database****************************************************
				//*********************this is where i need to receive postID in order to update instead of add to DB***********
				if (isset($_POST["pastebox"])) {
					
					if (isset($_POST["postID"])) {
						$postID = $_POST["postID"];
					}
					else {
						$postID = "none";
					}

					$postContent = $_POST["pastebox"];
    			$postContent = htmlentities($postContent, ENT_QUOTES);
    			$tagsArray = array();
    			
    			if (!empty($_POST['tag_boxes'])) {
    				foreach($_POST['tag_boxes'] as $selected) {
    					$tagsArray[] = $selected;
    				}
    			
    				$postDateTime = date("Y-m-d H:i:s");
    				$lastID = setPostContent($postID, $postContent, $postDateTime);
    				setPostTags($postID, $tagsArray, $lastID);
    			}
    			else {
    				echo "<br>";
    				echo "<p style='color: red;'>No tags selected.<p>";
    			}
    		}

    		echo "<table name='post-table' id='post-table'>";
    		//echo "<ul>";

				
				//******************this is for displaying posts to table***************
				//**********************************************************************    		
				//$postID = getMaxID("posts");
    		//$postContent = getPostContent("all");
    		$postIDs = getPostIDs();
    		//loop through posts
	    	if ($postIDs) {										//if i got anything from getPostContent
	    		foreach ($postIDs as $postID) {
	    			//echo "postContent[3]: " . $postContent[3];
	    			$postContent = getPostContent($postID);
	    			$postContent = nl2br($postContent[0]);							//then replace new lines with breaks
	    			$postTagString = "";							//initialize $postTagString
	    			$postTags = getPostTags($postID); //get tags based on the post id
	    			$postDateTimeArray = getPostDateTime($postID);	//get date and time
	    			$postDateTime = $postDateTimeArray[0];

	    			if (!($postTags)) {				//if there were no tags
	    				$postTagString = "";		//then provide empty string
	    			}
	    			else {

		    			//otherwise loop through tags of each post
		    			foreach ($postTags as $postTag) {
		    				$postTagString .= "<div class='tag-bottom'>" . $postTag . "</div>";  //and concatenate a string of tags including <div>'s
		    			}
						}
	    			
	    			$postTagString = rtrim($postTagString, ', ');  //trim off the trailing commas

	    			//and now display everything
	    			echo "<tr id='post-row'>";
	    			//echo "<li>";
	    			echo "<td class='post-column-posts'>";
	    			//echo $postID;
	    			echo "<div class='post-date-time'>" . $postDateTime . "</div><br>";
	    			echo "<div class='post-content'>" . $postContent . "</div><br>";
	    			echo "<div class='post-tags'>" . $postTagString . "</div>";

	    			echo "<div class='post-edit-delete'>";
						
						echo "<div id='edit'>";
						echo "<form action='' method='post'>";
	    			echo "<input type='submit' class='edit' value='Edit'>";
	    			//provides hidden $postID value to pass to $_POST
	    			echo "<input type='hidden' name='postID' value='$postID'>";
	    			echo "<input type='hidden' name='edit' value='TRUE'>";
	    			echo "</form>";
	    			echo "</div>";

	    			echo "<div id='delete'>";
	    			echo "<form action='' method='post'>";
	    			echo "<input type='submit' class='delete' value='Delete'>";
	    			//provides hidden $postID value to pass to $_POST
	    			echo "<input type='hidden' name='postID' value='$postID'>";
	    			echo "<input type='hidden' name='delete' value='TRUE'>";
						echo "</form>";
	    			echo "</div>";
	    			echo "</div>";
	    			
	    			echo "</td>";
	    			//echo "</li>";
	    			echo "</tr>";

	    			//loop through postcontent in reverse since getPostContent() results are sorted opposite
	    			//$postID -= 1;  
	    		}
	    	}
	    	else {
	    		echo "<br><div>No data available.</div>";
	    	}
    		//echo "</ul>";
    		echo "</table>";
			?>
		</div>
		</div>
		<script>
			addTagMenuHandlers();
			addRowHandlers();
		</script>
		</body>
	</html>