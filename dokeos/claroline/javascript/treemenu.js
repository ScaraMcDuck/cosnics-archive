/**
 * cssTreeMenu
 * Author: E. Vlieg - Flydesign.nl
 * Tweaked by Tim De Pauw for use with Dokeos
 * Cannot handle multiple tree menus on a single page--yet
 */


/**
 * Create the + and - items in the menu and find the selected node
 */
onload = function(){
	// Find the selected node and open all the parent menus
	var tms = getElementByClassName('li', 'treeMenuSelect');
	makeMenu(getElementByClassName('ul', 'treeMenu'));
	if(tms){
		openParentNode(tms);
	}
}

/**
 * Save the last state so we can show the current state the next time
 */
onunload = function(){
	saveState();
}

var aTreeMenu = new Array();
var makeMenuParentsOpenMenu = true;

/**
 * Save the last state in a cookie with the format "i-i-i"
 * Where i is an integer which matches the number of the submenu that is currently open
 */
function saveState(){
	var aCookie = new Array();
	for(var i = 0; i < aTreeMenu.length; i++){
		if(aTreeMenu[i].className.indexOf("itemOpen") != -1)
			aCookie[aCookie.length] = i;
	}
	var sCookie = "treeMenuState="+escape(aCookie.join("-"));

    document.cookie = sCookie;
}

/**
 * Run through the given list and check if a li node contains a ul node.
 * If this is true, create a clickable node to expand the ul
 * @param object oTree
 */
function makeMenu(oTree){
	var oChildren = oTree.childNodes;
	var bLast = false;
	var aLastState = getCookie("treeMenuState").split("-");

	// Iterate through every child
	for(var i=oChildren.length-1; i >= 0; i--){
		// Create a new submenu when the li element contains a ul element
		if(oChildren[i].nodeName == "LI" && hasSubmenu(oChildren[i])){
			// If this is the last node, give it a different class
			var sClassName = (arrayContains(aLastState, aTreeMenu.length))? " itemOpen" : " itemClose";
			if(!bLast){
				oChildren[i].className += sClassName + "End";
				bLast = true;
			} else
				oChildren[i].className += sClassName;

			aTreeMenu[aTreeMenu.length] = oChildren[i];

			// If the boolean is set and the href of the firstChild A is '#'
			// the item opens and closes the menu
			if(makeMenuParentsOpenMenu && oChildren[i].firstChild.nodeName == "A"){
				if(oChildren[i].firstChild.href == location.href.replace("#","")+"#"){
					oChildren[i].firstChild.href="javascript:void(0);";
					oChildren[i].firstChild.onclick = function(event){
						if(!event){
							event = window.event;
							oObj = event.srcElement.parentNode;
						} else
							oObj = event.target.parentNode;
						event.cancelBubble = true;
						switchClassName(oObj);
					};
				}
			}

			// Register the event handler for this node
			oChildren[i].onclick = function(event){
				if(!event){
					event = window.event;
					oObj = event.srcElement;
				} else
					oObj = event.target;
				event.cancelBubble = true;
				switchClassName(oObj);
			};
		} else if(oChildren[i].nodeName == "LI") {
			oChildren[i].className = "item " + oChildren[i].className;
			// If this is the last node, give it an extra class
			if(!bLast){
				oChildren[i].className += " endItem";
				bLast = true;
			}
		}
	}
}

/**
 * Switch the class name of an object
 * @param object oObj
 */
function switchClassName(oObj){
	if(oObj.className.indexOf("itemOpen") != -1){
		oObj.className = oObj.className.replace("itemOpen", "itemClose");
	} else if(oObj.className.indexOf("itemClose") != -1) {
		oObj.className = oObj.className.replace("itemClose", "itemOpen");
	}
}

/**
 * Checks if a list object contains a ul object
 * @param object oList
 * @return boolean
 */
function hasSubmenu(oList){
	var oMenuChildren = oList.childNodes;
	var bHasList = false;

	// Iterate through all the child nodes and search for a ul tag
	for(var j = 0; j < oMenuChildren.length; j++){
		if(oMenuChildren[j].nodeName == "UL") {
			makeMenu(oMenuChildren[j]);
			bHasList = true;
		}
	}
	return bHasList;
}

/**
 * Finds the parent menu in which this item is placed and opens the menu
 * @param object oItem
 */
function openParentNode(oItem){
	if(oItem.parentNode.nodeName == "UL" && oItem.parentNode.parentNode.nodeName == "LI"){
		oMenu = oItem.parentNode.parentNode;
		oMenu.className = oMenu.className.replace("itemClose", "itemOpen");
		openParentNode(oMenu);
	}
}

/**
 * Returns the value of the cookie with the given name
 * @param string name
 * @return string
 */
function getCookie(name) {
    var cookies = document.cookie.split(";");
    for (var i = 0; i < cookies.length; i++) {
        var a = cookies[i].split("=");
        if (a.length == 2) {
            if (a[0] == name) {
                return unescape(a[1]);
            }
        }
    }
    return "";
}

/**
 * Checks if the needle exists in the haystack
 * @param array aSrc
 * @param string sNeedle
 * @return boolean
 */
function arrayContains(aHayStack, sNeedle){
    for (var i = 0; i < aHayStack.length; i++) {
        if (aHayStack[i] == sNeedle)
        	return true;
    }
    return false;
}

/**
 * Returns all elements of the given type and class name
 * @param string type
 * @param string className
 * @return array
 */
function getElementByClassName(type, className) {
	var e = document.getElementsByTagName(type);
	for (var i = 0; i < e.length; i++) {
		var el = e[i];
		var classNames = el.className.split(/\s/);
		if (arrayContains(classNames, className)) {
			return el;
		}
	}
	return null;
}

/**
 * Checks if an array contains a certain element
 * @param array
 * @param string
 * @return boolean
 */
function arrayContains(haystack, needle) {
	for (var i = 0; i < haystack.length; i++) {
		if (haystack[i] == needle) {
			return true;
		}
	}
	return false;
}