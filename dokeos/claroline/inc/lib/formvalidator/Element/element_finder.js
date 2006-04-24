// TODO: Provide an alternative if AJAX isn't supported.

elementFinderSetup = new Array();
elementFinderSetup['timeout'] = 500;
elementFinderSetup['searching'] = 'Searching ...';
elementFinderSetup['noresults'] = 'No results';
elementFinderSetup['error'] = 'Error';

var ajaxMethods = new Array(
	function() { return new ActiveXObject("Msxml2.XMLHTTP") },
	function() { return new ActiveXObject("Microsoft.XMLHTTP") },
	function() { return new XMLHttpRequest() }
);

var ajaxMethodIndex = -1;

for (var i = 0; i < ajaxMethods.length; i++) {
	try {
		ajaxMethods[i]();
		ajaxMethodIndex = i;
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
	destination.options[0] = new Option(elementFinderSetup['searching'], 0);
	origin.disabled = destination.disabled = true;
	destination.style.fontFamily = 'monospace';
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
		this.destination.options[0] = new Option(elementFinderSetup['error'], 0);
	}
	elementFinderObjects[this.destination] = null;
}

ElementFinderSearch.prototype.returnResults = function () {
	this.destination.options.length = 0;
	var xml = this.ajax.responseXML;
	if (!xml) {
		this.destination.options[0] = new Option(elementFinderSetup['error'], 0);
		return;
	}
	var root = elementFinderExtractChild(xml);
	if (!root) {
		this.destination.options[0] = new Option(elementFinderSetup['error'], 0);
		return;
	}
	var mainLeaf = elementFinderExtractChild(root);
	if (mainLeaf) {
		this.origin.disabled = this.destination.disabled = false;
		fillElementFinderResults(mainLeaf, this.destination, 0, false);
	}
	else {
		this.destination.options[0] = new Option(elementFinderSetup['noresults'], 0);
	}
}

function elementFinderExtractChild (node) {
	for (var i = 0; i < node.childNodes.length; i++) {
		if (node.childNodes[i].nodeType == 1) {
			return node.childNodes[i];
		}
	}
	return null;
}

function fillElementFinderResults (node, destination, indent, isLast) {
	var nbsp = String.fromCharCode(0xA0);
	var trunk = String.fromCharCode(0x2502);
	var leaf = String.fromCharCode(0x251C);
	var endLeaf = String.fromCharCode(0x2514);
	var leafIcon = String.fromCharCode(0x25A1);
	var nodeIcon = String.fromCharCode(0x25A0);
	var prefix = '';
	for (var i = 1; i < indent; i++) {
		prefix += (isLast ? nbsp : trunk) + nbsp;
	}
	destination.options[destination.options.length] = new Option(prefix
		+ (indent > 0 ? leaf + nbsp : '')
		+ nodeIcon + nbsp
		+ node.getAttribute('title'), 0);
	if (indent > 0) {
		prefix += trunk + nbsp;
	}
	for (var i = 0; i < node.childNodes.length; i++) {
		var child = node.childNodes[i];
		var isLast = (i == node.childNodes.length - 1);
		switch (child.nodeName) {
			case 'leaf':
				var title = child.getAttribute('title');
				var id = child.getAttribute('id');
				var opt = new Option(
					prefix + (isLast ? endLeaf : leaf) + nbsp + leafIcon + nbsp + title,
					id);
				opt.otherText = title;
				destination.options[destination.options.length] = opt;
				break;
			case 'node':
				fillElementFinderResults(child, destination, indent + 1, isLast);
				break;
		}
	}

}

function elementFinderFind (searchURL, origin, destination) {
	if (elementFinderTimeout) {
		clearTimeout(elementFinderTimeout);
		elementFinderTimeout = null;
	}
	elementFinderTimeout = setTimeout(function () {
		new ElementFinderSearch(searchURL, origin, destination);
	}, elementFinderSetup['timeout']);
}

function elementFinderGetAjaxObject () {
	return ajaxMethods[ajaxMethodIndex]();
}

function elementFinderMove (source, destination, hidden) {
	if (source.selectedIndex < 0 || source.options[source.selectedIndex].value <= 0) return;
	if (destination) {
		var src = source.options[source.selectedIndex];
		var opt = new Option(src.otherText, src.value);
		opt.otherText = src.text;
		destination.options[destination.options.length] = opt;
	}
	source.options[source.selectedIndex] = null;
}

function elementFinderExcludeString (elmt) {
	var string = '';
	var brackets = escape('[]');
	for (var i = 0; i < elmt.options.length; i++) {
		string += '&exclude' + brackets + '=' + elmt.options[i].value;
	}
	return string;
}

function cloneActiveElements (source, destination) {
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