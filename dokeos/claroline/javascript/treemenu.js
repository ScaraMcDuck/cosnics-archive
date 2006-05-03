/**
 *
 * Tree Menu script
 *
 * Turns all UL elements that have "tree-menu" as their class name into nice
 * tree menus.
 *
 * Here's a trivial example:
 *
 * <ul class="tree-menu">
 *   <li>
 *     <a href="category1.html">Category 1</a>
 *     <ul>
 *       <li>
 *         <a href="category1_1.html">Category 1.1 (empty)</a>
 *         <ul><li></li></ul>
 *       </li>
 *       <li>
 *         <a href="document1.html">Document 1</a>
 *       </li>
 *     </ul>
 *   </li>
 *   <li>
 *     <a href="category2.html">Category 2 (empty)</a>
 *   </li>
 * </ul>
 *
 * @author Tim De Pauw <ct at xanoo dot com>
 *
 */

var tmClassName = "tree-menu";
var tmCollapseLevel = 1;

function tmInitAll ()
{
	var trees = tmGetElementsByClassName("ul", tmClassName);
	for (var i = 0; i < trees.length; i++)
	{
		tmInit(trees[i]);
	}
}

function tmInit (tree, collapseLevel)
{
	tree.style.visibility = "hidden";
	if (collapseLevel == null)
	{
		collapseLevel = tmCollapseLevel;
	}
	var activeNodes = new Array();
	tmWalkTree(tree, 0, collapseLevel, activeNodes);
	for (var i = 0; i < activeNodes.length; i++)
	{
		tmExpandNode(activeNodes[i], true);
	}
	tree.style.visibility = "visible";
}

function tmWalkTree (tree, level, collapseLevel, activeNodes)
{
	var children = tmFilterTextNodes(tree.childNodes);
	var hasChildren = false;
	for (var i = 0; i < children.length; i++)
	{
		var child = children[i];
		if (child.tagName.toLowerCase() == "li")
		{
			if (i == children.length - 1)
			{
				tmAddClassName(child, "last");
			}
			if (tmIsRootNode(child))
			{
				tmAddClassName(child, "root");
			}
			var validChild = tmParseNode(child, level + 1, collapseLevel, activeNodes);
			if (validChild)
			{
				hasChildren = true;
			}
			if (tmHasClassName(child, "current"))
			{
				activeNodes[activeNodes.length] = child;
			}
			else if (collapseLevel >= 0 && level >= collapseLevel)
			{
				tmCollapseNode(child);
			}
		}
	}
	return hasChildren;
}

function tmParseNode (node, level, collapseLevel, activeNodes)
{
	var children = tmFilterTextNodes(node.childNodes);
	// 0 = leaf, 1 = empty node, 2 = node with children
	var type = 0;
	var link;
	for (var i = 0; i < children.length; i++)
	{
		var child = children[i];
		switch (child.tagName.toLowerCase())
		{
			case "a":
				link = child;
				break;
			case "ul":
				var hasChildren = tmWalkTree(child, level, collapseLevel, activeNodes);
				if (hasChildren)
				{
					type = 2;
				}
				else
				{
					node.removeChild(child);
					type = 1;
				}
				if (tmIsLastNode(node))
				{
					tmAddClassName(child, "last");
				}
				break;
		}
	}
	switch (type)
	{
		case 0:
			tmAddClassName(node, "leaf");
			break;
		case 1:
			tmAddClassName(node, "empty");
			break;
	}
	if (link)
	{
		tmWrapInDiv(link, hasChildren);
		return true;
	}
	return false;
}

function tmExpandOrCollapse (node)
{
	if (tmIsCollapsed(node))
	{
		tmExpandNode(node);
	}
	else
	{
		tmCollapseNode(node);
	}
}

function tmExpandNode (node, climbUp)
{
	tmRemoveClassName(node, "collapsed");
	if (climbUp && !tmIsRootNode(node)
	&& node && node.parentNode && node.parentNode.parentNode)
	{
		tmExpandNode(node.parentNode.parentNode, true);
	}
}

function tmCollapseNode (node)
{
	tmAddClassName(node, "collapsed");
}

function tmIsCollapsed (node)
{
	return tmHasClassName(node, "collapsed");
}

function tmIsRootNode (node)
{
	return (node && node.parentNode ? tmHasClassName(node.parentNode, tmClassName) : false);
}

function tmIsLastNode (node)
{
	return tmHasClassName(node, "last");
}

function tmWrapInDiv (link, collapsible)
{
	var div = document.createElement("div");
	var linkID = link.getAttribute('id');
	var oldOnclick = link.onclick;
	link.removeAttribute('id');
	var copy = link.cloneNode(true);
	copy.setAttribute('id', linkID);
	copy.onclick = function (e) {
		if (!e) e = window.event;
		e.cancelBubble = true;
		this.blur();
		return (oldOnclick ? oldOnclick(e) : true);
	};
	div.appendChild(copy);
	var parent = link.parentNode;
	parent.replaceChild(div, link);
	if (tmHasClassName(parent, "last"))
	{
		div.className = "last";
	}
	if (collapsible)
	{
		div.onclick = function (e) {
			tmExpandOrCollapse(parent);
		};
	}
}

function tmFilterTextNodes (nodes) {
	var result = new Array();
	for (var i = 0; i < nodes.length; i++) {
		var node = nodes[i];
		if (node.nodeType != 3) {
			result[result.length] = node;
		}
	}
	return result;
}


function tmGetElementsByClassName (tagName, className)
{
	var el = document.getElementsByTagName(tagName);
	var res = new Array();
	for (var i = 0; i < el.length; i++)
	{
		var elmt = el[i];
		if (tmHasClassName(elmt, className))
		{
			res[res.length] = elmt;
		}
	}
	return res;
}

function tmAddClassName (element, className)
{
	if (!tmHasClassName(element, className))
	{
		var names = tmGetClassNames(element);
		names[names.length] = className;
		tmSetClassNames(element, names);
	}
	if (tmRequiresCssFix(className))
	{
		tmIECssFix(element);
	}
}

function tmRemoveClassName (element, className)
{
	var names = tmGetClassNames(element);
	var newNames = new Array();
	for (var i = 0; i < names.length; i++)
	{
		if (names[i] != className)
		{
			newNames[newNames.length] = names[i];
		}
	}
	tmSetClassNames(element, newNames);
	if (tmRequiresCssFix(className))
	{
		tmIECssFix(element);
	}
}

function tmHasClassName (element, className)
{
	return tmArrayContains(tmGetClassNames(element), className);
}

function tmGetClassNames (element)
{
	return (element && element.className ? element.className.split(/ +/) : new Array());
}

function tmSetClassNames (element, classNames)
{
	if (!element) return;
	var n = "";
	for (var i = 0; i < classNames.length; i++)
	{
		n += classNames[i] + " ";
	}
	element.className = n;
}

function tmIECssFix (element)
{
	tmRemoveClassName(element, "last-empty");
	tmRemoveClassName(element, "last-leaf");
	tmRemoveClassName(element, "last-collapsed");
	var names = tmGetClassNames(element);
	if (tmArrayContains(names, "last"))
	{
		if (tmArrayContains(names, "empty"))
		{
			tmAddClassName(element, "last-empty");
		}
		else if (tmArrayContains(names, "leaf"))
		{
			tmAddClassName(element, "last-leaf");
		}
		else if (tmArrayContains(names, "collapsed"))
		{
			tmAddClassName(element, "last-collapsed");
		}
	}
}

function tmRequiresCssFix (className)
{
	return (document.all && (className == "last" || className == "empty" || className == "leaf" || className == "collapsed"));
}

function tmArrayContains (haystack, needle)
{
	for (var i = 0; i < haystack.length; i++)
	{
		if (haystack[i] == needle)
		{
			return true;
		}
	}
	return false;
}

function tmAddOnloadFunction (f)
{
	if (window.onload)
	{
		var oldOnload = window.onload;
		window.onload = function (e) {
			oldOnload(e);
			f();
		};
	}
	else
	{
		window.onload = f;
	}
}

/*
 * Primitive check for tree menu initialization. Prevents tree menus from being
 * initialized several times in case the script gets included several times.
 * Note that this still redefines all the functions above every time, which is
 * sort of bad.
 * TODO: Use some sort of singleton pattern to make this obsolete.
 */
if (!('tmInitialized' in window))
{
	tmAddOnloadFunction(tmInitAll);
	tmInitialized = true;
}