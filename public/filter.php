<?php

//**************Form for searching posts and filtering results*****************
//*****************************************************************************


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

<div id='filter' class='menu'>
	<div id='filtera'>	
			<form id='search-form' action='' method='post'>

				<span id='find-container'>
					<label for='find' class='label'>
						<span class='fa fa-search'></span>
					</label>
					<input type='text' id='find' name='searchterm' class='white-textbox' placeholder='Enter text or tag to search...' size='28'/>
				</span>
				
				<script> setFind("<?php echo $searchTerm; ?>"); </script>
	</div>

	<div id='filterb'>
				<span id='limit-container'>
					<label for='limit' class='label'>
						<span class='fa fa-filter'></span>
					</label>
					<select id='limit' class='white-textbox' name='limit' onchange='this.form.submit()'>
		  			<?php
		  				echo "<option value='5'>&nbsp&nbsp&nbsp5</option>";
		  				echo "<option value='10'>&nbsp&nbsp10</option>";
		  				echo "<option value='50'>&nbsp&nbsp50</option>";
		  				echo "<option value='100'>100</option>";
		  				echo "<option value='500'>500</option>";
		  			?>
					</select>
				</span>

				<script> selectLimit(<?php echo $limit; ?>); </script>

				<input type='submit' id='search-button' name='search-button' value='Search' class='button'>

			</form>

			<form id='reset-form' action='' method='post'>
				<input type='submit' id='reset-button' name='reset-button' value='Reset' class='button'>
				<input type='hidden' name='reset' value='TRUE'>
			</form>
	</div>
</div>