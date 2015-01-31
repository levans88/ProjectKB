<?php
session_start();
//session_destroy();
require("../includes/config.php");
//require_once("donotupload.php");

$devMode = FALSE;
if ($devMode === TRUE) {
	echo '<pre>';
	echo "POST";
	print_r($_POST);
	echo "<br>";
	echo "SESSION";
	print_r($_SESSION);
	echo '</pre>';
}

?>
	<html>
		<head>
			<link rel="stylesheet" type="text/css" href="css/styles.css" />
			<script type="text/javascript" src="js/scripts.js"></script>
			<title>Lenny Evans</title>
		</head>

		<body>
			<div class="container">
			<header id='header'>
				<?php
					require("header.php");

					if (isset($_POST["limit"])) {
						$limit = $_POST["limit"];
					}
					//else if (isset($_SESSION["limit"])) {
						//$limit = $_SESSION["limit"];
					//}
					else {
						$limit = 5;
					}
					//$_POST["limit"] = $limit;
					//$_SESSION["limit"] = $limit;


					if (isset($_POST["find"])) {
						$termString = $_POST["find"];
					}
					//else if (isset($_SESSION["find"])) {
						//$termString = $_SESSION["find"];
					//}
					else {
						$termString = FALSE;
					}
					//$_SESSION["find"] = $termString;


					if ($_SESSION["loggedIn"] === TRUE) {
						echo "<div id='form-container'>";
						echo "<form class='newpost' action='' method='post'>";
						
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
								$limit = 1;

								//$_POST["limit"] = 1;
								//$_SESSION["limit"] = 1;
								//echo $limit;
								$postContent = getPostContent($postID, $limit);
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
							//echo "<br><br>";
							echo "<textarea id='pastebox' name='pastebox' placeholder='Paste or type content here...' rows='4' cols='100'>" . $postContent . "</textarea>";
						  
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
								echo "<div class='new-tags'>new:</div>&nbsp&nbsp<input type='text' class='transparent-textbox' placeholder='ex: tag1;tag2' name='newTags' size='10'>";
								echo "</div>";
								echo "</ul>";
							echo "</div>";
						
							if (isset($_POST["edit"])) {
								$buttonValue = "Update";
							}
							else {
								$buttonValue = "Post";
							}

						echo "<input type='submit' name='post' value='$buttonValue'/>";
						

						//***************************this is another hidden input so that i can add (keep) postID in $_POST ...IF we were editing... *****
						//***************************this makes sure the same postid gets edited and we're not doing an insert****************************
							if ($postID == TRUE) {
								//echo "postID was set/true";
								echo "<input type='hidden' name='postID' value='$postID'>";
							}
						
						//echo "<br>";
					echo "</form>";
					echo "</div>";
				}
			echo "</header>";
			require("filter.php");
			if (isset($_SESSION["loggedIn"])) {
				if ($_SESSION["loggedIn"] === TRUE) {
					echo "<div id='post-container-tall'>";
				}
				else {
					echo "<div id='post-container-short'>";
				}
			}
			else {
				echo "<div id='post-container-short'>";
			}
			
			

				//*********************this is for writing posts to database****************************************************
				//*********************this is where i need to receive postID in order to update instead of add to DB***********
				if (isset($_POST["pastebox"])) {
					
					if (isset($_POST["postID"])) {
						if (!isset($_POST["delete"])) {
							$postID = $_POST["postID"];
						}
						else {
							$postID = "none";
						}
					}

					$newTagsString = "";
					$newTagsArray = array();
					if(isset($_POST["newTags"])) {
						$newTagsString = rtrim($_POST["newTags"], '; ');
						$newTagsArray = explode(';', $newTagsString);
					}
					else {
						$newTags = "none";
					}

					$postContent = $_POST["pastebox"];
    			$postContent = htmlentities($postContent, ENT_QUOTES);
    			$tagsArray = array();
    			
    			if (!empty($_POST['tag_boxes'])) {
    				foreach($_POST['tag_boxes'] as $selected) {
    					$tagsArray[] = $selected;
    					//echo "tag_boxes test";
    				}
    			}

    			if (!empty($_POST["newTags"])) {
    				foreach($newTagsArray as $t) {
    					$tagsArray[] = $t;
    					//echo "new tags test";
    				}
    			}
    				
    			if (!empty($tagsArray)) {
    				$postDateTime = date("Y-m-d H:i:s");
    				$lastID = setPostContent($postID, $postContent, $postDateTime);
    				newTags($newTagsString);
    				//echo $newTagsString;
            //foreach($newTagsArray as $t) {
              //  echo $t;
            //}
    				setPostTags($postID, $tagsArray, $lastID);
    			}
    			else {
    				//echo "<br>";
    				echo "<p style='color: red;'>No tags selected.<p>";
    			}
    		}
    		//echo "<ul>";

				
				//******************this is for displaying posts to table***************
				//********************************************************************** 
				echo "<table name='post-table' id='post-table'>";   		
				//$postID = getMaxID("posts");
    		//$postContent = getPostContent("all");
				//$tagName = "";
    		
    		//$postIDs = array();
    		//if there is a tagName set, get it, else set it to "none"
				if (isset($_POST["tagName"])) {
	    			$tagName = $_POST["tagName"];
	    		}
	    		else if (isset($_SESSION["tagName"])) {
	    			$tagName = $_SESSION["tagName"];
	    		}
	    		else {
	    			$tagName = "none";
	    		}
	    		$_SESSION["tagName"] = $tagName;


				//if we are editing and there is a postID set to be edited... (makes sure we're not deleting instead)
				if (isset($_POST["postID"])) {
					if (isset($_POST["edit"])) {
						$postIDs[0] = $_POST["postID"];
					}
				}
				else if ($termString) {		//if there are any search terms in $_POST, perform the search
						$postIDs = find($termString, $limit);
				}
				//otherwise, allow for retrieving posts based on a previously set tag name
				else {
	    		$postIDs = getPostIDs($tagName, $limit);
		    }
	    	
  			if (!isset($postIDs)) {
  				if ((!isset($_POST['delete'])) || ($_POST['delete'] === FALSE)) {
  					$postIDs[0] = $postID;
  				}
  				else {
  					$postIDs = getPostIDs($tagName, $limit); 
  				}
  				
  			}
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

	    			//if (!isset($_POST['edit'])) {
	    				//$limit = 5;
	    			//}

	    			//and now display everything
	    			echo "<tr id='post-row'>";
	    			echo "<td class='post-column-posts'>";
	    			echo "<div class='post-date-time'>" . $postDate . "</div>";

	    			if ($devMode) {
	    				echo "<div style='float: right;'>";
	    				echo $postID;
	    				echo "</div>";
	    			}

	    			echo "<br>";

	    			$postContent = autoFormatLinks($postContent);
	    			//if (isset($_POST['find'])) {
	    				//$postContent = autoFormatTerms($postContent, $termString);
	    			//}

	    			echo "<div class='post-content'>" . $postContent . "</div><br>";
	    			
	    			echo "<div id='tag-bottom-menu'>";
	    			foreach ($postTags as $tagName) {
	    				echo "<div class='tag-bottom'>";
	    				echo "<form action='' method='post'>";
	    				echo "<input type='submit' class='tag-bottom-submit' value='$tagName'>";
	    				echo "<input type='hidden' name='tagName' value='$tagName'>";
	    				//echo "<input type='hidden' name='limit' value='$limit'>";
	    				echo "</form>";
	    				echo "</div>";
	    			}
		    			echo "<div class='tag-bottom'>";
		    			echo "<form action='' method='post'>";
		    			echo "<input type='submit' style='font-style:normal; font-size:1.5em' class='tag-bottom-submit' value='*'>";
		    			echo "<input type='hidden' name='tagName' value='none'>";
		    			//echo "<input type='hidden' name='limit' value='$limit'>";
		    			
		    			echo "</form>";
		    			echo "</div>";
	    			echo "</div>";

	    			echo "<div class='post-edit-delete'>";
						
						if ($_SESSION["loggedIn"] === TRUE) {
							
							echo "<div id='edit'>";
								echo "<form action='' method='post'>";
		    				echo "<input type='submit' class='edit' value='Edit'>";
		    				//provides hidden $postID value to pass to $_POST
		    				echo "<input type='hidden' name='postID' value='$postID'>";
		    				echo "<input type='hidden' name='edit' value='TRUE'>";
		    				//echo "<input type='hidden' name='limit' value='$limit'>";
		    				echo "</form>";
		    				echo "</div>";

		    				echo "<div id='delete'>";
		    				echo "<form action='' method='post'>";
		    				echo "<input type='submit' class='delete' value='Delete'>";
		    				//provides hidden $postID value to pass to $_POST
		    				echo "<input type='hidden' name='postID' value='$postID'>";
		    				echo "<input type='hidden' name='delete' value='TRUE'>";
		    				//echo "<input type='hidden' name='limit' value='$limit'>";
								echo "</form>";
		    				echo "</div>";
		    			echo "</div>";
		    		}
		    		else {
		    			echo "<br><br>";
		    		}
	    			
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
			//addRowHandlers();
			addNavMenuHandlers();
		</script>
		</body>
	</html>