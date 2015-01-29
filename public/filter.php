<div style='overflow: hidden; background-color: #e1e5f0; padding-top: 15px;'>

<div style='float: left; padding-left: 15px;'>
		

	<div style='display: inline-block;'>
		<form class='filter' action='' method='post'>
			<label for='select-limit' style='font-size: 12px; font-weight: bold;'>Show:</label>
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
			<!--<input type='hidden' name='limit' value='$limit'>";-->
		<!--</form>-->
	</div>

	<div style='display: inline-block; padding-left: 30px;'>
		<!--<form class='find' action='' method='post'>-->
			<label for='find' style='font-size: 12px; font-weight: bold;'>Find:</label>
			<input type='text' id='find' name='find' class='transparent-textbox' placeholder='Enter text or tag to search for.' size='28'/>
			<script> setFind("<?php echo $termString; ?>"); </script>
			<input type='submit' value='Go'/>
		</form>
	</div>

</div>

</div>