<?php

//*************Setup the header layout, nav menu, and login form**************
//****************************************************************************

?>


<div class="container">
			
<div id='header'><!--header gets closed in index.php-->

	<div id='nav-menu'>

		<a class='nav-menu-item' href='https://github.com/levans88' target='_blank'>
			<span class='nav-menu-icon'>
				<span class='fa fa-github-square'></span>
			</span>
			<span class='nav-menu-text'>GitHub</span>
		</a>
		
		<a class='nav-menu-item' href='http://blog.lennyevans.net/' target='_blank'>
			<span class='nav-menu-icon'>
				<span class='icon-blogger'></span>
			</span>
			<span class='nav-menu-text'>Blog</span>
		</a>

		<a class='nav-menu-item' href='https://www.youtube.com/user/lennyevans3' target='_blank'>
			<span class='nav-menu-icon'>
				<span class='fa fa-youtube'></span>
			</span>
			<span class='nav-menu-text'>YouTube</span>
		</a>

		<a class='nav-menu-item' href='http://www.linkedin.com/in/lennyevans/' target='_blank'>
			<span class='nav-menu-icon'>
				<span class='fa fa-linkedin'></span>
			</span>
			<span class='nav-menu-text'>LinkedIn</span>
		</a>

		<a class='nav-menu-item' href='http://www.twitter.com/lennyevans88' target='_blank'>
			<span class='nav-menu-icon'>
				<span class='fa fa-twitter'></span>
			</span>
			<span class='nav-menu-text'>Twitter</span>
		</a>

		<a id='nav-menu-item-email' class='nav-menu-item' href='mailto:lenny.evans3@gmail.com'>
			<span class='nav-menu-icon'>
				<span class='icon-mail'></span>
			</span>
			<span class='nav-menu-text'>Email</span>
		</a>

	</div>


	<?php
		if (!sessionHas("loggedin")) {		
			echo "<div id='login-menu'>";
				echo "<form id='login' action='' method='post'>";
					echo "<input type='text' id='username' name='username' class='white-textbox' placeholder='Username' size='11'>";
					echo "<input type='password' id='password' name='password' class='white-textbox' placeholder='Password' size='11'>";
					echo "<input type='submit' id='login-button' name='login' class='button' value='Login'/>";
				echo "</form>";
			echo "</div>";

			echo "<div id='title'>";
				echo "<div id='title-name'>";
					//echo "PR<div id='logo-container'><img id='logo-image' src='/img/LennySquareSmall.png'></div>JECT-KB";
				echo "PR";
				echo "<span id='title-cog'>";
					echo "<span class='fa fa-cog'></span>";
				echo "</span>";
				echo "JECT-KB";
				echo "</div>";

				echo "<div id='title-desc'>";
					echo "An active personal project for learning and note taking.<br>";
						echo "Please see progress and current issues on GitHub <a href='https://github.com/levans88/ProjectKB' target='_blank'>here</a>.";
				echo "</div>";
			echo "</div>";
			}
			else {
				echo "<div id='logout-menu'>"; 
					echo "<form class='logout' action='' method='post'>";
						echo "<input type='submit' id='logout-button' class='button' value='Logout'/>";
						echo "<input type='hidden' name='logout' value='TRUE'>";
					echo "</form>";
				echo "</div>";
			}
	?>

<?php

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