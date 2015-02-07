<?php

//**************Setup the header layout, nav menu, and login form*************** 
//******************************************************************************

?>


<div class="container">
			
<header id='header'>

<div class='header-container'>

<div style='float: left; padding-left: 25px; padding-top: 28px; padding-bottom: 0px;'>
	
	<div style='float: left; padding-top: 10px;'>
		<img src="/img/books.png" style="width: 80px;">
	</div>

	<div style='float: right;'>
		<div style='font-size: 26px; font-weight: bold; padding-left: 5px; padding-top: 5px;'>
			ProjectKB
		</div>

		<div style='padding-left: 10px; padding-top: 2px; font-size: 12px; font-style: italic;'>
			An active personal project for learning and archiving.<br>
			Please see progress and issues on GitHub <a href='https://github.com/levans88/ProjectKB' target='_blank'>here</a>.

		</div>
	</div>
	
</div>

<div style='clear: both;'></div>

<div id='right-side' style='float: right; padding-right: 15px; padding-top: 15px; padding-right: 25px; padding-bottom: 0px; margin-bottom: 0px;'>

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

		--><!--<div class='edit' style='display: inline-block; padding: 5px; border-right: gray solid 1px;'>
			  	<a style='color: inherit;' href='' target='_blank'>Resume</a>
			 </div>--><!--

		--><div class='edit' style='display: inline-block; padding: 5px;'>
			  	<a style='color: inherit;' href='mailto:lenny.evans3@gmail.com'>Email</a>
			 </div>
	</div>



	<div style="float: left; padding-right: 10px">
		<br>
			<img src="/img/Lenny.jpg" style="height: 48px; width: 50px; border-radius: 50%;">
		<br>
	</div>

<?php

if (!sessionHas("loggedin")) {		
	echo "<div id='login-menu' style='float: left; margin-top: 3px; padding-bottom: 0px;'>";
		echo "<br>";
		echo "<form class='login' action='' method='post'>";
			echo "<input type='text' name='username' class='transparent-textbox' placeholder='Username' size='10'><br>";
			echo "<input type='password' name='password' class='transparent-textbox' placeholder='Password' size='10'><br>";
			echo "<div style='text-align: center;'><input type='submit' class='edit' name='login' value='Login'/></div>";
		echo "</form>";
	echo "</div>";
	}
	else {
		echo "<br>";
		echo "<div style='text-align: right; padding-right: 16px;'>"; 
			echo "<form class='logout' action='' method='post'>";
				echo "<input type='submit' class='edit' value='Logout'/>";
				echo "<input type='hidden' name='logout' value='TRUE'>";
			echo "</form>";
		echo "</div>";
	}

	echo "</div>";	//close right-side
	echo "</div>";	//close header-container
	echo "<div id='error' style='clear: both; width: 100%; text-align: center; color: red; font-size: 14px; padding-bottom: 15px; margin: 0 auto;'>";
		echo SessionHas("error_forward");
		giveSession("error_forward", FALSE);
	echo "</div>";	//close error div
?>