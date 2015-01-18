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
    if (!tagList) {

        return false;
    }
    else {
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
        };
    }
}


function addNavMenuHandlers() {
    var navMenu = document.getElementById('nav-menu');
    var navItems = navMenu.getElementsByTagName("div");
    
        for (i = 0; i < navItems.length; i++) {
            var currentNavItem = navItems[i];
            
            var createHoverHandler = 
                function(currentNavItem) 
                {
                    return function() {
                        this.className='edit-hovered';
                        };
                };
        
            var createLeaveHandler = 
                function(row)
                {
                    return function() {
                        this.className='edit';
                    };
                };
        
        currentNavItem.onmouseover = createHoverHandler(currentNavItem);
        currentNavItem.onmouseout = createLeaveHandler(currentNavItem);
    }
}

//function headerSize() {
    //var = document.getElementById('header').style.height;
//}

/*function showLoginOption() {
    var loginOption = document.getElementById('login-option');
    
    if (loginOption.style.display == "none") {
        loginOption.style.display = "block";
    }
    else {
        loginOption.style.display = "none";
    }
}


function showLoginMenu() {
    
    showLoginOption();
    var loginMenu = document.getElementById('login-menu');

    if (loginMenu.style.display == "none") {
        loginMenu.style.display = "block";
    }
    else {
        loginMenu.style.display = "none";
    }
}*/