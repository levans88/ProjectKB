/**
 * scripts.js
 *
 * Global JavaScript, if any.
 */

function addRowHandlers() {
    var table = document.getElementById("post-table");
    var rows = table.getElementsByTagName("tr");


    for (i = 0; i < rows.length; i++) {
        var currentRow = table.rows[i];
        
        var createHoverHandler = 
            function(row) 
            {
              return function() {
              	//this.style.backgroundColor='#FFFFF5';
              	this.className='hovered';
              	
              	//var item = document.getElementById('pencil');
              	//item.className = (item.className=='hidden')?'unhidden':'hidden';
              };
            };
        
        var createLeaveHandler = 
        		function(row)
        		{
        			return function() {
        				//this.style.backgroundColor='white';
        				this.className='not-hovered';
        			}
        		}
        
        currentRow.onmouseover = createHoverHandler(currentRow);
        currentRow.onmouseout = createLeaveHandler(currentRow);
    }
}


function addTagMenuHandlers() {
    var tagList = document.getElementById("tag-list");
    var labels = tagList.getElementsByTagName("label");


    for (i = 0; i < labels.length; i++) {
        var currentLabel = labels[i];
        
        var createClickHandler = 
            function(currentLabel) 
            {
              return function() {
                //this.style.backgroundColor='#FFFFF5';
                if (this.className === 'notselected') {
                    this.className='selected';
                }
                else if (this.className === 'selected') {
                    this.className='notselected';
                };
                
                //var item = document.getElementById('pencil');
                //item.className = (item.className=='hidden')?'unhidden':'hidden';
              };
            };
        
        currentLabel.onclick = createClickHandler(currentLabel);
        //currentDiv.onmouseout = createLeaveHandler(currentDiv);
    }
}
/*function addRowHandlers() {
    var table = document.getElementById("post-table");
    var rows = table.getElementsByTagName("tr");
    for (i = 0; i < rows.length; i++) {
        var currentRow = table.rows[i];
        var createClickHandler = 
            function(row) 
            {
                return function() { 
                                        var cell = row.getElementsByTagName("td")[0];
                                        var id = cell.innerHTML;
                                        alert("id:" + id);
                                 };
            };

        currentRow.onclick = createClickHandler(currentRow);
    }
}*/
