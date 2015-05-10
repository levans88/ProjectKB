<?php

//**************Control session/post variables and load resources*************
//****************************************************************************


session_start();

require("../includes/config.php");
//require_once("donotupload.php");


//state setting - if anything is received from $_POST,
//put it in $_SESSION for later and/or redirect,
//then get values from $_SESSION


//if $_POST has "logout", log user out
if (postHas("logout")) {
	//session_start();
	session_unset();
	session_destroy();
	redirect();
}


if (sessionHas("error")) {
	giveSession("login", FALSE);

	//"error" needs to be cleared because certain code below won't execute if it has a value,
	//but certain pages will need the error later to display it
	giveSession("error_forward", sessionHas("error"));

	giveSession("error", FALSE);
	giveSession("update", FALSE);
	giveSession("insert", FALSE);
}

//login using username and password from $_POST (if exist)
if (postHas("login") && !sessionHas("error")) {
	if (postHas("username") || postHas("password")) {
		$username = postHas("username");
		$password = postHas("password");

		//if both username and password are supplied, try to authenticate
		if (postHas("username") && postHas("password")) {
			$authenticated = authenticate($username, $password);

			//if username and password were valid, log the user in
			if ($authenticated) {
				giveSession("loggedin", TRUE);
				giveSession("username", $username);

				//eventually store a key derivative or similar field to
				//re-validate before database writes (inserts, updates, deletes)
			}
			else {
				//set "loggedin" to false and say why
				giveSession("loggedin", FALSE);
				giveSession("error", "Invalid username or password.");
			}
		}
		//if only username is supplied, give error
		elseif (postHas("username") && !postHas("password")) {
			giveSession("error", "Missing password.");
		}
		//if only password is supplied, give error
		else {
			giveSession("error", "Missing username.");
		}
	}
	else {
		giveSession("error", "Missing username and password.");
	}
	redirect();
}


//if $_POST has either "update" or "insert"...
if ((postHas("update") || postHas("insert")) && !postHas("cancel")) {
	
	//we are writing to DB so reset "tagname" and "searchterm" here
	//so that when returning to results page it is a normal view
	//this is currently handled separately on the filter page for "edit" (update) only - MOVE THAT HERE
	giveSession("searchterm", FALSE);
	giveSession("tagname", FALSE);

	//if minimum requirements are met for submission...
	if (postHas("postcontent") && (postHas("tag_boxes") || postHas("newtags"))) {
		
		giveSession("postcontent", postHas("postcontent"));
		giveSession("tag_boxes", postHas("tag_boxes"));

		if (postHas("newtags")) {
			giveSession("newtags", postHas("newtags"));
		}
	}
	else {
		if (postHas("postcontent")) {
			if (!postHas("tag_boxes") && !postHas("newtags")) {
				giveSession("error", "Missing tags.");
				redirect();
			}
		}
		elseif (postHas("tag_boxes") || postHas("newtags")) {
			if (!postHas("postcontent")) {
				giveSession("error", "Missing post content.");
				redirect();
			}
		}
		else {
			giveSession("error", "Missing post content and tags.");
			redirect();
		}
	}
}


//if $_POST has "update" specifically
if (postHas("update") && !postHas("cancel")) {
	giveSession("edit", FALSE);
	giveSession("postid", postHas("postid"));
	giveSession("update", TRUE);
	redirect();
}

//if $_POST has "insert" specifically
if (postHas("insert") && !postHas("cancel")) {
	giveSession("insert", TRUE);
	redirect();
}

//if $_POST has "cancel"
if (postHas("cancel")) {
	giveSession("edit", FALSE);

	//"cancel" is not a state we will stay in
	//giveSession("cancel", TRUE);
	redirect();
}

//if $_POST has "limit", give it to $_SESSION,
//limit will submit "searchterm" too if a searchterm is in the box
//(unless "limit" is supplied from outside the form)
if (postHas("limit")) {
	
	giveSession("edit", FALSE);
	giveSession("limit", postHas("limit"));
	
	//if $_POST also has "searchterm", give that to $_SESSION,
	//and turn off tag filtering
	if (postHas("searchterm")) {
		
		giveSession("searchterm", postHas("searchterm"));
		giveSession("tagname", FALSE);
	}
	redirect();
}

//set default limit in $_SESSION if there isn't one
if (!sessionHas("limit")) {
	giveSession("limit", 5);
}

//if $_POST has "reset"
if (postHas("reset")) {
	giveSession("edit", FALSE);
	giveSession("limit", FALSE);
	giveSession("searchterm", FALSE);
	giveSession("tagname", FALSE);
	redirect();
}

//if $_POST has "tagname", give it to $_SESSION
if (postHas("tagname")) {
	giveSession("edit", FALSE);
	giveSession("searchterm", FALSE);
	giveSession("tagname", postHas("tagname"));
	redirect();
}

//if $_POST has "edit", give it to $_SESSION - 
//DO NOT set "limit" to 1 here for edit.php AND show.php to use,
//do that in those pages, this way $_SESSION will store limit,
//and those pages will override it individually, temporarily, and only
if (postHas("edit")) {
	giveSession("edit", TRUE);
	giveSession("postid", postHas("postid"));
	//giveSession("limit", 1);
	redirect();
}

//if $_POST has "delete" then delete the post
if (postHas("delete")) {
	if (postHas("postid")) {
		giveSession("edit", FALSE);
		delPost(postHas("postid"));
		redirect();
	}
	//if $_POST has "delete" but no "postid", give error
	else {
		giveSession("error", "Missing Post ID.");
		redirect();
	}
}

//adds database post numbers and displays $_POST and $_SESSION arrays,
//will cause filter.php to be taller (no need to fix this)
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<!--<link href='http://fonts.googleapis.com/css?family=Droid+Sans|Cabin|Armata|PT+Sans+Narrow|Arimo|Quicksand|Josefin+Sans|Gloria+Hallelujah|Roboto+Slab|Nunito|Bree+Serif|Architects+Daughter|Amatic+SC|Chewy|Covered+By+Your+Grace|Cinzel|Rock+Salt' rel='stylesheet' type='text/css'>-->
		<link href='http://fonts.googleapis.com/css?family=Droid+Sans|Just+Another+Hand|Sue+Ellen+Francisco|Give+You+Glory|Neucha|Shadows+Into+Light|Gloria+Hallelujah|Just+Me+Again+Down+Here|Indie+Flower|Yanone+Kaffeesatz|Nothing+You+Could+Do|Rancho|Englebert|Covered+By+Your+Grace' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		<link rel="stylesheet" type="text/css" href="css/font-awesome.css" />
		<link rel="stylesheet" type="text/css" href="css/fontello.css" />
		<script type="text/javascript" src="js/scripts.js"></script>
		<title>Lenny Evans - Project-KB</title>
		<link rel="shortcut icon" href="img/database-16.ico">

	</head>

	<body>
			<?php
				require("header.php");
				
				//if logged in, require edit.php - otherwise just close
				//header div because edit.php is inside of it
				if (sessionHas("loggedin")) {
					require("edit.php");
				}

				require("filter.php");
				echo "</div>";	//close 'header'
				require("write.php");
				require("show.php");
		?>

	</div>	<!-- close 'container' -->
	<script>
		addTagMenuHandlers();
	</script>
	</body>
</html>