/**
 *
 * Tree Menu script
 *
 * Turns all UL elements that have "tree-menu" as their class name into nice
 * tree menus.
 *
 * Here's a trivial example:
 *
 * <ul class="treeMenu">
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

var treeClassName = "tree-menu";
var treeCollapseLevel = 1;

function initTrees ()
{
	var trees = getElementsByClassName("ul", treeClassName);
	for (var i = 0; i < trees.length; i++)
	{
		initTree(trees[i]);
	}
}

function initTree (tree)
{
	tree.style.visibility = "hidden";
	var activeNodes = new Array();
	walkTree(tree, 0, activeNodes);
	for (var i = 0; i < activeNodes.length; i++)
	{
		expandNode(activeNodes[i], true);
	}
	tree.style.visibility = "visible";
}

function walkTree (tree, level, activeNodes)
{
	var children = filterTextNodes(tree.childNodes);
	var hasChildren = false;
	for (var i = 0; i < children.length; i++)
	{
		var child = children[i];
		if (child.tagName.toLowerCase() == "li")
		{
			if (i == children.length - 1)
			{
				addClassName(child, "last");
			}
			if (isRootNode(child))
			{
				addClassName(child, "root");
			}
			var validChild = parseNode(child, level + 1, activeNodes);
			if (validChild)
			{
				hasChildren = true;
			}
			if (hasClassName(child, "active"))
			{
				activeNodes[activeNodes.length] = child;
			}
			else if (level >= treeCollapseLevel)
			{
				collapseNode(child);
			}
		}
	}
	return hasChildren;
}

function parseNode (node, level, activeNodes)
{
	var children = filterTextNodes(node.childNodes);
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
				var hasChildren = walkTree(child, level, activeNodes);
				if (hasChildren)
				{
					type = 2;
				}
				else
				{
					node.removeChild(child);
					type = 1;
				}
				if (isLastNode(node))
				{
					addClassName(child, "last");
				}
				break;
		}
	}
	switch (type)
	{
		case 0:
			addClassName(node, "leaf");
			break;
		case 1:
			addClassName(node, "empty");
			break;
	}
	if (link)
	{
		wrapInDiv(link, hasChildren);
		return true;
	}
	return false;
}

function expandOrCollapse (node)
{
	if (isCollapsed(node))
	{
		expandNode(node);
	}
	else
	{
		collapseNode(node);
	}
}

function expandNode (node, climbUp)
{
	removeClassName(node, "collapsed");
	if (climbUp && !isRootNode(node))
	{
		expandNode(node.parentNode.parentNode, true);
	}
}

function collapseNode (node)
{
	addClassName(node, "collapsed");
}

function isCollapsed (node)
{
	return hasClassName(node, "collapsed");
}

function isRootNode (node)
{
	return hasClassName(node.parentNode, treeClassName);
}

function isLastNode (node)
{
	return hasClassName(node, "last");
}

function wrapInDiv (link, collapsible)
{
	var div = document.createElement("div");
	var copy = link.cloneNode(true);
	copy.onclick = function (e) {
		if (!e)
		{
			e = window.event;
		}
		e.cancelBubble = true;
		this.blur();
	};
	div.appendChild(copy);
	var parent = link.parentNode;
	parent.replaceChild(div, link);
	if (hasClassName(parent, "last"))
	{
		div.className = "last";
	}
	if (collapsible)
	{
		div.onclick = function () {
			expandOrCollapse(parent);
		};
	}
}

function filterTextNodes (nodes)
{
	var result = new Array();
	for (var i = 0; i < nodes.length; i++)
	{
		if (nodes[i].tagName)
		{
			result[result.length] = nodes[i];
		}
	}
	return result;
}

function getElementsByClassName (tagName, className)
{
	var el = document.getElementsByTagName(tagName);
	var res = new Array();
	for (var i = 0; i < el.length; i++)
	{
		var elmt = el[i];
		if (hasClassName(elmt, className))
		{
			res[res.length] = elmt;
		}
	}
	return res;
}

function addClassName (element, className)
{
	if (!hasClassName(element, className))
	{
		var names = getClassNames(element);
		names[names.length] = className;
		setClassNames(element, names);
	}
	if (requiresCssFix(className))
	{
		ieCssFix(element);
	}
}

function removeClassName (element, className)
{
	var names = getClassNames(element);
	var newNames = new Array();
	for (var i = 0; i < names.length; i++)
	{
		if (names[i] != className)
		{
			newNames[newNames.length] = names[i];
		}
	}
	setClassNames(element, newNames);
	if (requiresCssFix(className))
	{
		ieCssFix(element);
	}
}

function hasClassName (element, className)
{
	return arrayContains(getClassNames(element), className);
}

function getClassNames (element)
{
	return element.className.split(/ +/);
}

function setClassNames (element, classNames)
{
	var n = "";
	for (var i = 0; i < classNames.length; i++)
	{
		n += classNames[i] + " ";
	}
	element.className = n;
}

function ieCssFix (element)
{
	removeClassName(element, "last-empty");
	removeClassName(element, "last-leaf");
	removeClassName(element, "last-collapsed");
	var names = getClassNames(element);
	if (arrayContains(names, "last"))
	{
		if (arrayContains(names, "empty"))
		{
			addClassName(element, "last-empty");
		}
		else if (arrayContains(names, "leaf"))
		{
			addClassName(element, "last-leaf");
		}
		else if (arrayContains(names, "collapsed"))
		{
			addClassName(element, "last-collapsed");
		}
	}
}

function requiresCssFix (className)
{
	return (document.all && (className == "last" || className == "empty" || className == "leaf" || className == "collapsed"));
}

function arrayContains (haystack, needle)
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

function addOnloadFunction (f)
{
	if (window.onload != null)
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

addOnloadFunction(initTrees);