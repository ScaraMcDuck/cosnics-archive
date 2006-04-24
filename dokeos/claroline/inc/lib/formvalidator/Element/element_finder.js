// TODO: Provide an alternative if AJAX isn't supported.

elementFinderLocale = new Array();
elementFinderLocale['Searching'] = 'Searching ...';
elementFinderLocale['NoResults'] = 'No results';
elementFinderLocale['Error'] = 'Error';

var elementFinderSearchDelay = 500;

var elementFinderStyle = new Array();
elementFinderStyle['Space'] = String.fromCharCode(0xA0);
elementFinderStyle['Trunk'] = String.fromCharCode(0x2502);
elementFinderStyle['Branch'] = String.fromCharCode(0x251C);
elementFinderStyle['BranchEnd'] = String.fromCharCode(0x2514);
elementFinderStyle['Leaf'] = String.fromCharCode(0x25A1);
elementFinderStyle['Node'] = String.fromCharCode(0x25A0);


var elementFinderAjaxMethods = new Array(
	function() { return new ActiveXObject("Msxml2.XMLHTTP") },
	function() { return new ActiveXObject("Microsoft.XMLHTTP") },
	function() { return new XMLHttpRequest() }
);

var elementFinderAjaxMethodIndex = -1;

for (var i = 0; i < elementFinderAjaxMethods.length; i++) {
	try {
		elementFinderAjaxMethods[i]();
		elementFinderAjaxMethodIndex = i;
		break;
	} catch (e) { }
}

var elementFinderObjects = new Array();
var elementFinderTimeout;

function ElementFinderSearch (url, origin, destination) {
	if (elementFinderObjects[destination]) {
		elementFinderObjects[destination].active = false;
	}
	this.origin = origin;
	this.destination = destination;
	this.active = true;
	elementFinderObjects[destination] = this;
	this.ajax = elementFinderGetAjaxObject();
	destination.options.length = 0;
	destination.options[0] = new Option(elementFinderLocale['Searching'], 0);
	origin.disabled = destination.disabled = true;
	var searchObject = this;
	this.ajax.onreadystatechange = function() {
		searchObject.readyStateChanged();
	}
	this.ajax.open("GET", url, true);
	this.ajax.send("");
}

ElementFinderSearch.prototype.readyStateChanged = function () {
	if (!this.active || this.ajax.readyState != 4) return;
	if (this.ajax.status == 200) {
		this.returnResults();
	}
	else {
		this.destination.options.length = 0;
		this.destination.options[0] = new Option(elementFinderLocale['Error'], 0);
	}
	elementFinderObjects[this.destination] = null;
}

ElementFinderSearch.prototype.returnResults = function () {
	this.destination.options.length = 0;
	var xml = this.ajax.responseXML;
	if (!xml) {
		this.destination.options[0] = new Option(elementFinderLocale['Error'], 0);
		return;
	}
	var root = elementFinderLastChild(xml);
	if (!root) {
		this.destination.options[0] = new Option(elementFinderLocale['Error'], 0);
		return;
	}
	var mainLeaf = elementFinderLastChild(root);
	if (mainLeaf) {
		this.origin.disabled = this.destination.disabled = false;
		elementFinderAddResults(mainLeaf, this.destination);
	}
	else {
		this.destination.options[0] = new Option(elementFinderLocale['NoResults'], 0);
	}
}

function elementFinderFilterTextNodes (nodes) {
	var result = new Array();
	for (var i = 0; i < nodes.length; i++) {
		var node = nodes[i];
		if (node.nodeType != 3) {
			result[result.length] = node;
		}
	}
	return result;
}

function elementFinderLastChild (node) {
	if (!node || !node.childNodes || node.childNodes.length == 0) {
		return null;
	}
	var a = elementFinderFilterTextNodes(node.childNodes);
	return (a.length == 0 ? null : a[a.length - 1]);
}

function elementFinderAddResults (node, destination, isLast) {
	var nbsp = elementFinderStyle['Space'];
	var trunk = elementFinderStyle['Trunk'];
	var branch = elementFinderStyle['Branch'];
	var endBranch = elementFinderStyle['BranchEnd'];
	var leafIcon = elementFinderStyle['Leaf'];
	var nodeIcon = elementFinderStyle['Node'];
	if (!isLast) {
		isLast = new Array(true);
	}
	var prefix = '';
	for (var i = 1; i < isLast.length - 1; i++) {
		prefix += (isLast[i] ? nbsp : trunk) + nbsp;
	}
	var br = (isLast.length > 1
		? (isLast[isLast.length - 1] ? endBranch : branch) + nbsp
		: '');
	var parentOpt = new Option(prefix
		+ br + nodeIcon + nbsp
		+ node.getAttribute('title'), 0);
	parentOpt.isNode = true;
	parentOpt.isLast = isLast[isLast.length - 1];
	destination.options[destination.options.length] = parentOpt;
	prefix += (isLast.length > 1
		? (isLast[isLast.length - 1] ? nbsp : trunk) + nbsp
		: '');
	var childNodes = elementFinderFilterTextNodes(node.childNodes);
	for (var i = 0; i < childNodes.length; i++) {
		var child = childNodes[i];
		var currentIsLast = (i == childNodes.length - 1);
		switch (child.nodeName) {
			case 'leaf':
				var title = child.getAttribute('title');
				var id = child.getAttribute('id');
				var opt = new Option(
					prefix + (currentIsLast ? endBranch : branch) + nbsp + leafIcon + nbsp + title,
					id);
				opt.otherText = title;
				opt.isLast = currentIsLast;
				destination.options[destination.options.length] = opt;
				break;
			case 'node':
				var newIsLast = elementFinderPush(isLast, currentIsLast);
				elementFinderAddResults(child, destination, newIsLast);
				break;
		}
	}
}

function elementFinderPush (array, element) {
	var a = new Array();
	for (var i = 0; i < array.length; i++) {
		a[i] = array[i];
	}
	a[array.length] = element;
	return a;
}

function elementFinderFind (searchURL, origin, destination) {
	if (elementFinderTimeout) {
		clearTimeout(elementFinderTimeout);
		elementFinderTimeout = null;
	}
	elementFinderTimeout = setTimeout(function () {
		new ElementFinderSearch(searchURL, origin, destination);
	}, elementFinderSearchDelay);
}

function elementFinderGetAjaxObject () {
	return elementFinderAjaxMethods[elementFinderAjaxMethodIndex]();
}

function elementFinderMove (source, destination, hidden) {
	if (source.selectedIndex < 0 || source.options[source.selectedIndex].value <= 0) return;
	if (destination) {
		var src = source.options[source.selectedIndex];
		var opt = new Option(src.otherText, src.value);
		opt.otherText = src.text;
		destination.options[destination.options.length] = opt;
		if (src.isLast) {
			var prev = source.options[source.selectedIndex - 1];
			if (!prev.isNode) {
				prev.isLast = true;
				prev.text = prev.text.replace(
					elementFinderStyle['Branch'],
					elementFinderStyle['BranchEnd']);
			}
		}
	}
	var oldIndex = source.selectedIndex;
	source.options[oldIndex] = null;
	source.selectedIndex
		= (oldIndex >= source.options.length || source.options[oldIndex].isNode
			? oldIndex - 1
			: oldIndex);
}

function elementFinderExcludeString (elmt) {
	var string = '';
	var brackets = escape('[]');
	for (var i = 0; i < elmt.options.length; i++) {
		string += '&exclude' + brackets + '=' + elmt.options[i].value;
	}
	return string;
}

function elementFinderClone (source, destination) {
	var string = '';
	if (source.options.length > 0) {
		string = source.options[0].value
			+ '\t' + source.options[0].text;
		for (var i = 1; i < source.options.length; i++) {
			string += '\t' + source.options[i].value
				+ '\t' + source.options[i].text;
		}
	}
	destination.value = string;
}