<?php

//**************Form for searching posts and filtering results****************
//****************************************************************************


	//variables
	$searchTerm = "";

	//if not editing then get "limit" from $_SESSION
	if (!sessionHas("edit")) {
		if (sessionHas("limit")) {
			$limit = sessionHas("limit");
		}
		if (sessionHas("searchterm")) {
			$searchTerm = sessionHas("searchterm");
		}
	}

	//if we are editing, override $limit to 1 but "limit" to the default
	//so when leaving "edit", the default number of results are displayed
	else {
		$limit = 1;
		
		//comment this out to retain "limit" when returning from "edit"
		//giveSession("limit", FALSE);

		//override AND remove "searchterm" from $_SESSION so results aren't filtered
		//when leaving "edit", same thing for "tagname"
		giveSession("searchterm", FALSE);
		giveSession("tagname", FALSE);
	}

?>

<div style='display: inline-block; clear: both; width: 100%; background-color: #F5F5F5; border-bottom: 1px solid #eee; border-top: 1px solid gray; padding-top: 15px; padding-bottom: 15px;'>

	<div style='float: left; display: inline-block; padding-left: 30px;'>
		
			<form action='' method='post' style='float: left; display: inline-block;'>
				<label for='limit' style='font-size: 12px; font-weight: bold;'>Show:</label>
				<select id='limit' name='limit' class='transparent-textbox' onchange='this.form.submit()'>
	  			<?php
	  				echo "<option value='5'>5</option>";
	  				echo "<option value='10'>10</option>";
	  				echo "<option value='25'>25</option>";
	  				echo "<option value='50'>50</option>";
	  				echo "<option value='100'>100</option>";
	  			?>
				</select>

				<script> selectLimit(<?php echo $limit; ?>); </script>

				<label for='find' style='font-size: 12px; font-weight: bold; -webkit-text-size-adjust: none; padding-left: 20px;'>Find:</label>
				<input type='text' id='find' name='searchterm' class='transparent-textbox' placeholder='Enter text or tag to search...' size='28'/>
				<script> setFind("<?php echo $searchTerm; ?>"); </script>

				<div style='display: inline-block; padding-top: 1px; padding-left: 20px;'></div>

				<input type='submit' name='search_button' value='Go' style='font-size: 13px;'>

				<div style='display: inline-block; padding-top: 1px; padding-right: 5px;'></div>
			</form>

			<form action='' method='post' style='display: inline-block;'>
				<input type='submit' name='reset_button' value='Reset' style='font-size: 13px;'>
				<input type='hidden' name='reset' value='TRUE'>
			</form>

	</div>

</div>