/**
	 * cssTreeMenu
	 * Author: E. Vlieg - Flydesign.nl
	 */


	/**
	 * Create the + and - items in the menu and find the selected node
	 */
	onload = function(){
		makeMenu(document.getElementById('treeMenu'));
		// Find the selected node and open all the parent menus
		if(document.getElementById('treeMenuSelect')){
			openParentNode(document.getElementById('treeMenuSelect'));
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
		var oChilds = oTree.childNodes;
		var bLast = false;
		var aLastState = getCookie("treeMenuState").split("-");

		// Iterate through every child
		for(var i=oChilds.length-1; i >= 0; i--){
			// Create a new submenu when the li element contains a ul element
			if(oChilds[i].nodeName == "LI" && hasSubmenu(oChilds[i])){
				// If this is the last node, give it a different class
				var sClassName = (arrayContains(aLastState, aTreeMenu.length))? " itemOpen" : " itemClose";
				if(!bLast){
					oChilds[i].className += sClassName + "End";
					bLast = true;
				} else
					oChilds[i].className += sClassName;

				aTreeMenu[aTreeMenu.length] = oChilds[i];

				// If the boolean is set and the href of the firstChild A is '#'
				// the item opens and closes the menu
				if(makeMenuParentsOpenMenu && oChilds[i].firstChild.nodeName == "A"){
					if(oChilds[i].firstChild.href == location.href.replace("#","")+"#"){
						oChilds[i].firstChild.href="javascript:void(0);";
						oChilds[i].firstChild.onclick = function(event){
							if(!event){
								event = window.event;
								oObj = event.srcElement.parentNode;
							} else
								oObj = event.target.parentNode;
							event.cancelBubble = true;
							switchClassname(oObj);
						};
					}
				}

				// Register the event handler for this node
				oChilds[i].onclick = function(event){
					if(!event){
						event = window.event;
						oObj = event.srcElement;
					} else
						oObj = event.target;
					event.cancelBubble = true;
					switchClassname(oObj);
				};
			} else if(oChilds[i].nodeName == "LI") {
				oChilds[i].className = "item " + oChilds[i].className;
				// If this is the last node, give it an extra class
				if(!bLast){
					oChilds[i].className += " endItem";
					bLast = true;
				}
			}
		}
	}

	/**
	 * Switch the classname of an object
	 * @param object oObj
	 */
	function switchClassname(oObj){
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
		var oMenuChilds = oList.childNodes;
		var bHasList = false;

		// Iterate through all the child nodes and search for a ul tag
		for(var j = 0; j < oMenuChilds.length; j++){
			if(oMenuChilds[j].nodeName == "UL") {
				makeMenu(oMenuChilds[j]);
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