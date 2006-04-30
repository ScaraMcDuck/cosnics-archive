// TODO: Provide an alternative if AJAX isn't supported.

var elementFinderLocale = new Array();
elementFinderLocale['Searching'] = 'Searching ...';
elementFinderLocale['NoResults'] = 'No results';
elementFinderLocale['Error'] = 'Error';
elementFinderLocale['SelectedColor'] = '#E6E6FF';

var elementFinderSearchDelay = 500;

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

var elementFinderSearches = new Array();
var elementFinderLastSearches = new Array();
var elementFinderTimeouts = new Array();
var elementFinderSelectedElements = new Array();

function ElementFinderSearch (url, origin, destination) {
	if (elementFinderSearches[destination]) {
		elementFinderSearches[destination].active = false;
	}
	elementFinderSearches[destination] = this;
	this.origin = origin;
	this.destination = destination;
	this.active = true;
	this.ajax = elementFinderGetAjaxObject();
	this.emptyDestination();
	elementFinderNotify('Searching', destination);
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
		elementFinderNotify('Error', this.destination);
	}
	elementFinderSearches[this.destination] = null;
}

ElementFinderSearch.prototype.returnResults = function () {
	var xml = this.ajax.responseXML;
	if (!xml) {
		elementFinderNotify('Error', this.destination);
		return;
	}
	var root = elementFinderLastChild(xml);
	if (!root) {
		elementFinderNotify('Error', this.destination);
		return;
	}
	var mainLeaf = elementFinderLastChild(root);
	if (mainLeaf) {
		this.emptyDestination();
		var ul = document.createElement('ul');
		this.destination.appendChild(ul);
		elementFinderBuildResults(mainLeaf, ul, this.destination.getAttribute('id'));
		ul.className = treeClassName;
		initTree(ul);
	}
	else {
		elementFinderNotify('NoResults', this.destination);
	}
}

ElementFinderSearch.prototype.emptyDestination = function () {
	elementFinderEmptyNode(this.destination);
}

function elementFinderEmptyNode (node) {
	var children = node.childNodes;
	for (var i = 0; i < children.length; i++) {
		node.removeChild(children[i]);
	}
}

function elementFinderNotify (msgID, destination) {
	elementFinderEmptyNode(destination);
	destination.appendChild(document.createTextNode(elementFinderLocale[msgID]));
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

function elementFinderBuildResults (node, ul, destinationID) {
	var li = document.createElement('li');
	ul.appendChild(li);
	var a = document.createElement('a');
	a.appendChild(document.createTextNode(node.getAttribute('title')));
	a.setAttribute('href', 'javascript:void(0);');
	var className = node.getAttribute('class');
	if (className) {
		a.setAttribute('class', className);
	}
	li.appendChild(a);
	var ulSub = document.createElement('ul');
	li.appendChild(ulSub);
	var childNodes = elementFinderFilterTextNodes(node.childNodes);
	for (var i = 0; i < childNodes.length; i++) {
		var child = childNodes[i];
		switch (child.nodeName) {
			case 'leaf':
				var title = child.getAttribute('title');
				var description = child.getAttribute('description');
				var id = child.getAttribute('id');
				var className = child.getAttribute('class');
				var li = document.createElement('li');
				var a = document.createElement('a');
				var aID = destinationID + '_' + id;
				a.setAttribute('id', aID);
				a.setAttribute('href', 'javascript:elementFinderToggleLinkSelectionState(document.getElementById("' + aID + '"), document.getElementById("' + destinationID + '"));');
				a.setAttribute('element', id);
				if (className) {
					a.setAttribute('class', className);
				}
				if (description) {
					a.setAttribute('title', description);
				}
				a.appendChild(document.createTextNode(title));
				li.appendChild(a);
				ulSub.appendChild(li);
				break;
			case 'node':
				elementFinderBuildResults(child, ulSub, destinationID);
				break;
		}
	}
}

function elementFinderToggleLinkSelectionState (link, destination) {
	elementFinderSetLinkSelected(link, (link.style.backgroundColor ? false : true), destination);
}

function elementFinderSetLinkSelected (link, selected, destination) {
	if (selected) {
		link.style.backgroundColor = elementFinderLocale['SelectedColor'];
		if (destination) {
			elementFinderSelectedElements[destination] = elementFinderAddToArray(elementFinderSelectedElements[destination], link);
		}
	}
	else {
		link.style.backgroundColor = null;
		if (destination) {
			elementFinderSelectedElements[destination] = elementFinderRemoveFromArray(elementFinderSelectedElements[destination], link);
		}
	}
	if (destination) {
		var button = document.getElementById(destination.getAttribute('id')+'_button');
		button.disabled = (elementFinderSelectedElements[destination].length <= 0);
	}
}

function elementFinderActivate (inactive, active) {
	var toActivate = elementFinderSelectedElements[inactive];
	if (!toActivate || !toActivate.length) return;
	var hiddenElmt = document.getElementById(active.getAttribute('id')+'_hidden');
	var cached = elementFinderUnserialize(hiddenElmt.getAttribute('value'));
	for (var j = 0; j < toActivate.length; j++) {
		var link = toActivate[j];
		var id = link.getAttribute('element');
		var description = link.getAttribute('title');
		var label = link.firstChild.nodeValue;
		elementFinderActivateElement(id, label, active, description);
		elementFinderSetLinkSelected(link, false);
		elementFinderSetLinkEnabled(link, false);
		cached = elementFinderAddToArray(cached, id + ":" + label);
	}
	hiddenElmt.setAttribute('value', elementFinderSerialize(cached));
	toActivate.length = 0;
	document.getElementById(inactive.getAttribute('id')+'_button').disabled = true;
}

function elementFinderDeactivate (active, inactive) {
	var toDeactivate = elementFinderSelectedElements[active];
	if (!toDeactivate || !toDeactivate.length) return;
	var hiddenElmt = document.getElementById(active.getAttribute('id')+'_hidden');
	var cached = elementFinderUnserialize(hiddenElmt.getAttribute('value'));
	for (var j = 0; j < toDeactivate.length; j++) {
		var link = toDeactivate[j];
		var id = link.getAttribute('element');
		var label = link.firstChild.nodeValue;
		var otherLink = document.getElementById(link.getAttribute('id').replace('_active_', '_inactive_'));
		elementFinderDeactivateElement(id, active);
		if (otherLink) {
			elementFinderSetLinkEnabled(otherLink, true);
		}
		cached = elementFinderRemoveFromArray(cached, id + ":" + label);
	}
	hiddenElmt.setAttribute('value', elementFinderSerialize(cached));
	toDeactivate.length = 0;
	document.getElementById(active.getAttribute('id')+'_button').disabled = true;
}

function elementFinderActivateElement (element, label, activeList, description) {
	var ul = activeList.firstChild;
	if (!ul) {
		ul = document.createElement('ul');
		ul.style.listStyle = 'none';
		ul.style.margin = 0;
		ul.style.padding = 0;
		activeList.appendChild(ul);
	}
	var li = document.createElement('li');
	li.style.margin = 0;
	li.style.padding = 0;
	li.style.display = 'block';
	var containerID = activeList.getAttribute('id');
	var aID = containerID + '_' + element;
	var a = document.createElement('a');
	a.style.display = 'block';
	a.setAttribute('id', aID);
	a.setAttribute('href', 'javascript:elementFinderToggleLinkSelectionState(document.getElementById("' + aID + '"),document.getElementById("' + containerID + '"));');
	if (description) {
		a.setAttribute('title', description);
	}
	a.setAttribute('element', element);
	a.appendChild(document.createTextNode(label));
	li.appendChild(a);
	ul.appendChild(li);
}

function elementFinderDeactivateElement (element, activeList) {
	var ul = activeList.firstChild;
	for (var i = 0; i < ul.childNodes.length; i++) {
		var el = ul.childNodes[i];
		if (el.firstChild.getAttribute('element') == element) {
			ul.removeChild(el);
			break;
		}
	}
}

function elementFinderSetLinkEnabled (link, enabled) {
	var href = link.getAttribute('href');
	var realHref = link.getAttribute('realHref');
	if (enabled) {
		if (realHref) {
			link.setAttribute('href', realHref);
			link.setAttribute('realHref', null);
			link.style.fontWeight = null;
		}
	}
	else if (href) {
		link.setAttribute('realHref', href);
		link.setAttribute('href', 'javascript:void(0);');
		link.style.fontWeight = 'normal !important';
	}
}

function elementFinderAddToArray (array, element) {
	var newArray = (array ? elementFinderCloneArray(array) : new Array());
	if (elementFinderArrayContains(array, element)) return newArray;
	newArray[newArray.length] = element;
	return newArray;
}

function elementFinderRemoveFromArray (array, element) {
	var newArray = new Array();
	if (!array) return newArray;
	for (var i = 0; i < array.length; i++) {
		if (array[i] != element) {
			newArray[newArray.length] = array[i];
		}
	}
	return newArray;
}

function elementFinderCloneArray (array) {
	var newArray = new Array();
	if (!array) return newArray;
	for (var i = 0; i < array.length; i++) {
		newArray[i] = array[i];
	}
	return newArray;
}

function elementFinderArrayContains (array, element) {
	if (!array) return false;
	for (var i = 0; i < array.length; i++) {
		if (array[i] == element) {
			return true;
		}
	}
	return false;
}

function elementFinderFind (query, searchURL, origin, destination) {
	var destID = destination.getAttribute('id');
	query = elementFinderStripWhitespace(query);
	if (query.length > 0 && query == elementFinderLastSearches[destID]) {
		return;
	}
	if (elementFinderTimeouts[destID]) {
		clearTimeout(elementFinderTimeouts[destID]);
		elementFinderTimeouts[destID] = null;
	}
	if (query.length == 0) {
		elementFinderEmptyNode(destination);
		return;
	}
	elementFinderLastSearches[destID] = query;
	elementFinderTimeouts[destID] = setTimeout(function () {
		new ElementFinderSearch(searchURL + '?query=' + escape(query) + elementFinderExcludeString(origin), origin, destination);
	}, elementFinderSearchDelay);
}

function elementFinderStripWhitespace (str) {
	if (str.length == 0) return str;
	var start;
	for (start = 0; start < str.length; start++) {
		var char = str.charAt(start);
		if (char != " " && char != "\t") {
			break;
		}
	}
	if (start == str.length) return "";
	var end;
	for (end = str.length - 1; end >= 0; end--) {
		var char = str.charAt(end);
		if (char != " " && char != "\t") {
			break;
		}
	}
	return str.substring(start, end + 1);
}

function elementFinderExcludeString (destination) {
	var elmt = document.getElementById(destination.getAttribute('id')+'_hidden');
	var chunks = elementFinderUnserialize(elmt.getAttribute('value'));
	var str = '';
	for (var i = 0; i < chunks.length; i++) {
		var separatorIndex = chunks[i].indexOf(':');
		str += "&exclude[]=" + chunks[i].substring(0, separatorIndex);
	}
	return str;
}

function elementFinderRestoreFromCache (hiddenElmt, active) {
	var cached = elementFinderUnserialize(hiddenElmt.getAttribute('value'));
	for (var j = 0; j < cached.length; j++) {
		var separatorIndex = cached[j].indexOf(':');
		elementFinderActivateElement(cached[j].substring(0, separatorIndex), cached[j].substring(separatorIndex + 1), active);
	}
}

function elementFinderSerialize (elements) {
	if (!elements.length) {
		return "";
	}
	var string = elements[0];
	for (var i = 1; i < elements.length; i++) {
		string += "\t" + elements[i];
	}
	return string;
}

function elementFinderUnserialize (elements) {
	return elements.split("\t");
}

function elementFinderGetAjaxObject () {
	return elementFinderAjaxMethods[elementFinderAjaxMethodIndex]();
}