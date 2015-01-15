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
						//********************************so when i hit edit -OR- *delete*, this code is executed on postback*******************
							$postTagsArray = array();
							$postID = "";
							$postContent = "";
							
							if (isset($_POST["postID"])) {
								$postID = $_POST["postID"];
								if (isset($_POST["delete"])) {
									delPost($postID);
									//$postContent = "";
									$_POST = array();
								}
								else {
									//find postcontent and tags for post by postID
									$postContent = getPostContent($postID);
									$postContent = $postContent[0];
									$postTagsArray = getPostTags($postID);
								}
							}
							else {
								//$postContent = "Type or paste to add new content.";
								$postContent = "";
							}

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
    			}
    			$postDateTime = date("Y-m-d H:i:s");
    			$lastID = setPostContent($postID, $postContent, $postDateTime);
    			setPostTags($postID, $tagsArray, $lastID);
    		}

    		echo "<table name='post-table' id='post-table'>";
    		//echo "<ul>";

				
				//******************this is for displaying posts to table***************    		
				$postID = getMaxID("posts");
    		$postContent = getPostContent("all");

    		//loop through posts
	    	if ($postContent) {	
	    		foreach ($postContent as $post) {
	    			$post = nl2br($post);
	    			$postTagString = "";
	    			$postTags = getPostTags($postID);
	    			$postDateTimeArray = getPostDateTime($postID);
	    			$postDateTime = $postDateTimeArray[0];

	    			if (!($postTags)) {
	    				$postTagString = "";
	    			}
	    			else {

		    			//loop through tags of each post
		    			foreach ($postTags as $postTag) {
		    				$postTagString .= "<div class='tag-bottom'>" . $postTag . "</div>";
		    			}
						}
	    			
	    			$postTagString = rtrim($postTagString, ', ');

	    			echo "<tr id='post-row'>";
	    			//echo "<li>";
	    			echo "<td class='post-column-posts'>";
	    			echo "<div class='post-date-time'>" . $postDateTime . "</div><br>";
	    			echo "<div class='post-content'>" . $post . "</div><br>";
	    			echo "<div class='post-tags'>" . $postTagString . "</div>";

	    			echo "<div class='post-edit-delete'>";
						
						echo "<div id='edit'>";
						echo "<form action='' method='post'>";
	    			echo "<input type='submit' class='edit' value='Edit'>";
	    			//provides hidden $postID value to pass to $_POST
	    			echo "<input type='hidden' name='postID' value='$postID'>";
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
	    			$postID -= 1;  
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