<div class='header-container'>

<div style='float: right; padding-right: 15px;'>

	<div id='nav-menu' style='float: left; padding-left: 10; padding-right: 15px; padding-top: 13px; font-weight: bold;'>
		<br>
			 <div class='edit' style='display: inline-block; padding: 5px; border-right: gray solid 1px;'>
			 		<a style='color: inherit;' href='https://github.com/levans88' target='_blank'>GitHub</a>
			 </div><!--
		
		--><div class='edit' style='display: inline-block; padding: 5px; border-right: gray solid 1px;'>
					<a style='color: inherit;' href='http://modofthemoment.blogspot.com/' target='_blank'>Blog</a>
			 </div><!--
		
		--><div class='edit' style='display: inline-block; padding: 5px; border-right: gray solid 1px;'>
			  	<a style='color: inherit;' href='https://www.youtube.com/user/lennyevans3' target='_blank'>YouTube</a>
			 </div><!--
		
		--><div class='edit' style='display: inline-block; padding: 5px; border-right: gray solid 1px;'>
			  	<a style='color: inherit;' href='http://www.linkedin.com/in/lennyevans/' target='_blank'>LinkedIn</a>
			 </div><!--
		
		--><div class='edit' style='display: inline-block; padding: 5px; border-right: gray solid 1px;'>
			  	<a style='color: inherit;' href='' target='_blank'>Twitter</a>
			 </div><!--

		--><div class='edit' style='display: inline-block; padding: 5px;'>
			  	<a style='color: inherit;' href='mailto:lenny.evans3@gmail.com'>Email</a>
			 </div>
	</div>



	<div style="float: left; padding-right: 10px">
		<br>
		<!--<div style='width: 50px; height: 50px; background: url('img/Lenny.png');border-radius: 50%;'>-->
			<img src="/img/Lenny.png" style="width: 50px; height: 46px; border: lightgray solid 1px; border-radius: 50%;">
		<br>
	</div>

<?php
	
	//$_SESSION["loggedIn"] = FALSE;
	
	if (isset($_POST["logout"]) && isset($_SESSION["username"])) {
		session_start();
		session_destroy();
		header('location:index.php');
	}

	if (isset($_POST["username"]) && isset($_POST["password"]))
	{
		$username = $_POST["username"];
		$password = $_POST["password"];

		if (authenticate($username, $password)) {
			$_SESSION["loggedIn"] = TRUE;
			$_SESSION["username"] = $username;
		}
	}

	if (!isset($_SESSION["loggedIn"])) {
		$_SESSION["loggedIn"] = FALSE;
	}

	if ($_SESSION["loggedIn"] === FALSE) {
		
		echo "<div id='login-menu' style='float: left; margin-top: 3px;'>";
		echo "<br>";
		echo "<form class='login' action='' method='post'>";
			echo "<input type='text' name='username' class='transparent-textbox' placeholder='Username' size='10'><br>";
			echo "<input type='password' name='password' class='transparent-textbox' placeholder='Password' size='10'><br>";
			//echo "<br>";
			echo "<div style='text-align: center;'><input type='submit' class='edit' name='login' value='Login'/></div>";
		echo "</form>";
		echo "</div>";
	}
	else {
		echo "<br>";
		echo "<div style='text-align: right; padding-right: 16px;'>"; //'text-align: center; padding-right: 10px;'>";
		//echo "<br><br><br>";
		echo "<form class='logout' action='' method='post'>";
			//echo "<input type='text' name='username'><br>";
			//echo "<input type='text' name='password'><br>";
			//echo "<br>";
			echo "<input type='submit' class='edit' value='Logout'/>";
			echo "<input type='hidden' name='logout' value='TRUE'>";
		echo "</form>";
		echo "</div>";
	}
echo "</div>";
//echo "<div style='float: left;'>test</div>";
echo "</div>";
?>