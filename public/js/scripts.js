
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

/* KEEP */
/*function addNavMenuHandlers() {
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
}*/


/* KEEP */
/*function changeImage()
{
    var h = document.getElementById("header");
    h.backgroundImage = "url('../img/BooksPhoto.png')";
    x++;

    if(x >= images.length){
        x = 0;
    } 

    fadeImg(h.backgroundImage, 100, true);
    //setTimeout("changeImage()", 6000);
}

function fadeImg(el, val, fade){
    if(fade === true){
        val--;
    }else{
        val ++;
    }

    if(val > 0 && val < 100){
        el.style.opacity = val / 100;
        setTimeout(function(){fadeImg(el, val, fade);}, 10);
    }
}

var images = [],
x = 0;

images[0] = "../img/BooksPhoto.png";
images[1] = "../img/Watermelon.png";*/
//images[2] = "image3.jpg";
//setTimeout("changeImage()", 6000);*/