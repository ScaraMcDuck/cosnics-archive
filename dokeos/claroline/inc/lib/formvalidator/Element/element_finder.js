// TODO: Provide an alternative if AJAX isn't supported.

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
	destination.options[0] = new Option("...", 0);
	origin.disabled = destination.disabled = true;
	origin.style.fontFamily = destination.style.fontFamily = 'monospace';
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
		this.destination.options[0] = new Option("ERROR", 0);
	}
	elementFinderObjects[this.destination] = null;
}

ElementFinderSearch.prototype.returnResults = function () {
	var xml = this.ajax.responseXML;
	if (!xml) return;
	var root = xml.firstChild;
	this.destination.options.length = 0;
	if (root.childNodes.length > 0) {
		this.origin.disabled = this.destination.disabled = false;
		fillElementFinderResults(root.firstChild, this.destination, 0, false);
	}
	else {
		this.destination.options[0] = new Option("No results", 0);
	}
}

function fillElementFinderResults (node, destination, indent, isLast) {
	var nbsp = String.fromCharCode(0xA0);
	var trunk = String.fromCharCode(0x2502);
	var leaf = String.fromCharCode(0x251C);
	var endLeaf = String.fromCharCode(0x2514);
	var lo = String.fromCharCode(0x25A1);
	var cat = String.fromCharCode(0x25A0);
	var prefix = '';
	for (var i = 1; i < indent; i++) {
		prefix += (isLast ? nbsp : trunk) + nbsp;
	}
	destination.options[destination.options.length] = new Option(prefix
		+ (indent > 0 ? leaf + nbsp : '')
		+ cat + nbsp
		+ node.getAttribute('title'), 0);
	if (indent > 0) {
		prefix += trunk + nbsp;
	}
	for (var i = 0; i < node.childNodes.length; i++) {
		var child = node.childNodes[i];
		var isLast = (i == node.childNodes.length - 1);
		switch (child.nodeName) {
			case 'learning_object':
				var title = child.getAttribute('title');
				var id = child.getAttribute('id');
				var type = child.getAttribute('type');
				var opt = new Option(
					prefix + (isLast ? endLeaf : leaf) + nbsp + lo + nbsp + title + ' [' + type + ']',
					id);
				opt.otherText = title;
				destination.options[destination.options.length] = opt;
				break;
			case 'category':
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
	}, 500);
}

function elementFinderGetAjaxObject () {
	return ajaxMethods[ajaxMethodIndex]();
}

function elementFinderMove (source, destination, hidden) {
	if (source.selectedIndex < 0 || source.options[source.selectedIndex].value <= 0) return;
	var otherText = source.options[source.selectedIndex].text;
	source.options[source.selectedIndex].text = source.options[source.selectedIndex].otherText;
	source.options[source.selectedIndex].otherText = otherText;
	if (destination) {
		destination.options[destination.options.length] = source.options[source.selectedIndex];
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