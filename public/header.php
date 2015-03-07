<?php

//*************Setup the header layout, nav menu, and login form**************
//****************************************************************************

?>


<div class="container">
			
<div id='header'><!--header gets closed in index.php-->

<div id='left-side'>
		
		<div id='title-name'>
			Project-KB
		</div>

		<div id='title-desc'>
			An active personal project for learning and note taking.<br>
			Please see progress and current issues on GitHub <a href='https://github.com/levans88/ProjectKB' target='_blank'>here</a>.
		</div>
	
</div>

<div id='right-side'>

	<div id='nav-menu'>
		<a class='nav-menu-item' href='https://github.com/levans88' target='_blank'>GitHub</a>
		<a class='nav-menu-item' href='http://blog.lennyevans.net/' target='_blank'>Blog</a>
		<a class='nav-menu-item' href='https://www.youtube.com/user/lennyevans3' target='_blank'>YouTube</a>
		<a class='nav-menu-item' href='http://www.linkedin.com/in/lennyevans/' target='_blank'>LinkedIn</a>
		<a class='nav-menu-item' href='http://www.twitter.com/lennyevans88' target='_blank'>Twitter</a>
		<a class='nav-menu-item' href='mailto:lenny.evans3@gmail.com'>Email</a>
	</div>

	<div id='logo-container'>
			<img id='logo-image' src="/img/LennySquareSmall.png">
	</div>

<?php

if (!sessionHas("loggedin")) {		
	echo "<div id='login-menu'>";
		echo "<br>";
		echo "<form class='login' action='' method='post'>";
			echo "<input type='text' name='username' class='white-textbox' placeholder='Username' size='11'><br>";
			echo "<input type='password' name='password' class='white-textbox' placeholder='Password' size='11'><br>";
			echo "<div style='text-align: center;'><input type='submit' id='login-button' name='login' class='button' value='Login'/></div>";
		echo "</form>";
	echo "</div>";
	}
	else {
		echo "<br>";
		echo "<div id='logout-menu'>"; 
			echo "<form class='logout' action='' method='post'>";
				echo "<input type='submit' class='button' value='Logout'/>";
				echo "<input type='hidden' name='logout' value='TRUE'>";
			echo "</form>";
		echo "</div>";
	}

	echo "</div>";	//close right-side

	echo "<div id='error-container'>";
		if (SessionHas("error_forward")) {
			echo "<span class='error-message'>" . SessionHas("error_forward") . "</span>";
		}
		else {
			echo "&nbsp";
		}
		giveSession("error_forward", FALSE);
	echo "</div>";	//close error div

?>