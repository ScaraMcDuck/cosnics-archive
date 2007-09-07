/*
 *
 * Option Orderer QuickForm element JavaScript part
 *
 * @author Tim De Pauw <tim,pwnt,be>
 *
 */

function OptionOrderer(ol) {
	this.ol = ol;
	var metadata = OptionOrdererUtilities.getElementMetadata(this.ol);
	this.input = OptionOrdererUtilities.getElementsByClassName("input", metadata["name"]);
	this.loadData();
	this.updateValue();
	this.transform();
}

OptionOrderer.prototype.updateValue = function()
{
	var value;
	if (this.listItems.length > 0)
	{
		value = this.data[this.listItems[0]];
		for (var i = 1; i < this.listItems.length; i++)
		{
			value += "|" + this.data[this.listItems[i]];
		}
	}
	this.input.value = value;
};

OptionOrderer.prototype.moveUp = function(listItem)
{
	var i = this.determineIndex(listItem);
	if (i > 0)
	{
		this.swap(i - 1, i);
		this.updateValue();
	}
};

OptionOrderer.prototype.moveDown = function(listItem)
{
	var i = this.determineIndex(listItem);
	if (i < this.listItems.length - 1)
	{
		this.swap(i, i + 1);
		this.updateValue();
	}
};

OptionOrderer.prototype.swap = function(i1, i2)
{
	var temp = this.listItems[i1];
	this.listItems[i1] = this.listItems[i2];
	this.listItems[i2] = temp;
	// TODO: update view using DOM manipulation
};

OptionOrderer.prototype.determineIndex = function(listItem)
{
	for (var i = 0; i < this.listItems.length; i++)
	{
		if (this.listItems[i] == listItem)
		{
			return i;
		}
	}
	return -1;
};

OptionOrderer.prototype.loadData = function()
{
	this.data = new Array();
	this.listItems = new Array();
	for (var i = 0; i < this.ol.childNodes.length; i++)
	{
		if (this.ol.childNodes[i].nodeName.toLowerCase() == "li")
		{
			this.listItems.push(this.ol.childNodes[i]);
			var metadata = OptionOrdererUtilities.getElementMetadata(this.ol.childNodes[i]);
			this.data[this.ol.childNodes[i]] = metadata["value"];
		}
	}
};

OptionOrderer.prototype.transform = function()
{
	var inst = this;
	for (var i = 0; i < this.listItems.length; i++)
	{
		var up = document.createElement("a");
		up.className = "up-link";
		up.setAttribute("href", "javascript:void(0);");
		up.onclick = function()
		{
			inst.moveUp(inst.listItems[i]);
		};
		up.appendChild(document.createTextNode(String.fromCharCode(8593)));
		var down = document.createElement("a");
		down.className = "down-link";
		down.setAttribute("href", "javascript:void(0);");
		down.onclick = function()
		{
			inst.moveDown(inst.listItems[i]);
		};
		down.appendChild(document.createTextNode(String.fromCharCode(8595)));
		var container = document.createElement("span");
		container.className = "ordering-links";
		container.appendChild(up);
		container.appendChild(down);
		this.listItems[i].appendChild(container);
	}
};

OptionOrdererUtilities = new Object();

OptionOrdererUtilities.initializeOptionOrderers = function()
{
	var ols = OptionOrdererUtilities.getElementsByClassName("ol", "option-orderer");
	for (var i = 0; i < ols.length; i++)
	{
		new OptionOrderer(ols[i]);
	}
};

OptionOrdererUtilities.getElementMetadata = function(element)
{
	var prefix = "oord-";
	var classes = OptionOrdererUtilities.getClassNames(element);
	var metadata = new Array();
	for (var i = 0; i < classes.length; i++)
	{
		if (classes[i].indexOf(prefix) == 0)
		{
			var parts = classes[i].substring(prefix.length).split("_");
			metadata[parts[0]] = parts[1];
		}
	}
	return metadata;
};

OptionOrdererUtilities.getClassNames = function(element)
{
	return (element && element.className ? element.className.split(/ +/) : new Array());
};

OptionOrdererUtilities.getElementsByClassName = function(tagName, className)
{
	var el = document.getElementsByTagName(tagName);
	var res = new Array();
	for (var i = 0; i < el.length; i++)
	{
		var elmt = el[i];
		if (OptionOrdererUtilities.hasClassName(elmt, className))
		{
			res[res.length] = elmt;
		}
	}
	return res;
};

OptionOrdererUtilities.hasClassName = function(element, className)
{
	return OptionOrdererUtilities.arrayContains(OptionOrdererUtilities.getClassNames(element), className);
};

OptionOrdererUtilities.arrayContains = function(haystack, needle)
{
	for (var i = 0; i < haystack.length; i++)
	{
		if (haystack[i] == needle)
		{
			return true;
		}
	}
	return false;
};

OptionOrdererUtilities.addOnloadFunction = function(f)
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
};

OptionOrdererUtilities.addOnloadFunction(OptionOrdererUtilities.initializeOptionOrderers);