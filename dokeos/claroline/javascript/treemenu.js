/**
 * Turns all UL elements that have "treeMenu" as their class name into nice
 * tree menus.
 * @author Tim De Pauw
 */

function initTrees ()
{
	var trees = getElementsByClassName("UL", "treeMenu");
	for (var i = 0; i < trees.length; i++)
	{
		initTree(trees[i]);
	}
}

function initTree (tree)
{
	walkTree(tree);
}

function walkTree (tree)
{
	var children = filterTextNodes(tree.childNodes);
	for (var i = 0; i < children.length; i++)
	{
		var child = children[i];
		if (child.tagName == "LI")
		{
			if (i == children.length - 1)
			{
				addClassName(child, "last");
			}
			if (isRootFolder(child))
			{
				addClassName(child, "root");
			}
			parseNode(child);
		}
	}
}

function parseNode (node)
{
	var children = filterTextNodes(node.childNodes);
	var hasChildren = false;
	var link;
	for (var i = 0; i < children.length; i++)
	{
		var child = children[i];
		if (child.tagName == "UL")
		{
			walkTree(child);
			hasChildren = true;
			if (isLastNode(node))
			{
				addClassName(child, "last");
			}
		}
		else if (child.tagName == "A")
		{
			link = child;
		}
	}
	if (!hasChildren)
	{
		addClassName(node, "leaf");
	}
	wrapInDiv(link, hasChildren);
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

function expandNode (node)
{
	removeClassName(node, "collapsed");
}

function collapseNode (node)
{
	addClassName(node, "collapsed");
}

function isCollapsed (node)
{
	return hasClassName(node, "collapsed");
}

function isRootFolder (node)
{
	return hasClassName(node.parentNode, "treeMenu");
}

function isLastNode (node)
{
	return hasClassName(node, "last");
}

function wrapInDiv (link, collapsible)
{
	var div = document.createElement("div");
	var copy = link.cloneNode(true);
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
	if (needsCssFix(className))
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
	if (needsCssFix(className))
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
	removeClassName(element, "lastleaf");
	removeClassName(element, "lastcollapsed");
	var names = getClassNames(element);
	if (!arrayContains(names, "last"))
	{
		return;
	}
	if (arrayContains(names, "leaf"))
	{
		addClassName(element, "lastleaf");
	}
	if (arrayContains(names, "collapsed"))
	{
		addClassName(element, "lastcollapsed");
	}
}

function needsCssFix (className)
{
	return (className == "last" || className == "leaf" || className == "collapsed");
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

window.onload = initTrees;