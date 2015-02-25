

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
                    if (this.className === 'notselected') {
                        this.className='selected';
                    }
                    else if (this.className === 'selected') {
                        this.className='notselected';
                    };
                  };
                };
            
            currentLabel.onclick = createClickHandler(currentLabel);
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
                function(currentNavItem)
                {
                    return function() {
                        this.className='edit';
                    };
                };
        
        currentNavItem.onmouseover = createHoverHandler(currentNavItem);
        currentNavItem.onmouseout = createLeaveHandler(currentNavItem);
    }
}


function selectLimit(limit)
{    
    var selectBox = document.getElementById('limit');
    selectBox.value = limit;
}


function setFind(termString)
{
    var findBox = document.getElementById('find');
    findBox.value = termString;
}