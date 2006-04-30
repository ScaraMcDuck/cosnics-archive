// TODO: Provide an alternative if AJAX isn't supported.

var elfLocale = new Array();
elfLocale['Searching'] = 'Searching ...';
elfLocale['NoResults'] = 'No results';
elfLocale['Error'] = 'Error';
elfLocale['SelectedColor'] = '#E6E6FF';
elfLocale['DisabledColor'] = '#F6F6F6';

var elfSearchDelay = 500;

var elfAjaxMethods = new Array(
	function() { return new ActiveXObject("Msxml2.XMLHTTP") },
	function() { return new ActiveXObject("Microsoft.XMLHTTP") },
	function() { return new XMLHttpRequest() }
);

var elfAjaxMethodIndex = -1;

for (var i = 0; i < elfAjaxMethods.length; i++) {
	try {
		elfAjaxMethods[i]();
		elfAjaxMethodIndex = i;
		break;
	} catch (e) { }
}

var elfSearches = new Array();
var elfLastSearches = new Array();
var elfTimeouts = new Array();
var elfSelectedElements = new Array();

function ElementFinderSearch (url, origin, destination) {
	if (elfSearches[destination.getAttribute('id')]) {
		elfSearches[destination.getAttribute('id')].active = false;
	}
	elfSearches[destination.getAttribute('id')] = this;
	this.origin = origin;
	this.destination = destination;
	this.active = true;
	this.ajax = elfGetAjaxObject();
	this.emptyDestination();
	elfNotify('Searching', destination);
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
		elfNotify('Error', this.destination);
	}
	elfSearches[this.destination.getAttribute('id')] = null;
}

ElementFinderSearch.prototype.returnResults = function () {
	var xml = this.ajax.responseXML;
	if (!xml) {
		elfNotify('Error', this.destination);
		return;
	}
	var root = elfLastChild(xml);
	if (!root) {
		elfNotify('Error', this.destination);
		return;
	}
	var mainLeaf = elfLastChild(root);
	if (mainLeaf) {
		this.emptyDestination();
		var ul = document.createElement('ul');
		this.destination.appendChild(ul);
		elfBuildResults(mainLeaf, ul, this.destination.getAttribute('id'));
		ul.className = treeClassName;
		initTree(ul);
	}
	else {
		elfNotify('NoResults', this.destination);
	}
}

ElementFinderSearch.prototype.emptyDestination = function () {
	elfEmptyNode(this.destination);
}

function elfEmptyNode (node) {
	var children = node.childNodes;
	for (var i = 0; i < children.length; i++) {
		node.removeChild(children[i]);
	}
}

function elfNotify (msgID, destination) {
	elfEmptyNode(destination);
	destination.appendChild(document.createTextNode(elfLocale[msgID]));
}

function elfFilterTextNodes (nodes) {
	var result = new Array();
	for (var i = 0; i < nodes.length; i++) {
		var node = nodes[i];
		if (node.nodeType != 3) {
			result[result.length] = node;
		}
	}
	return result;
}

function elfLastChild (node) {
	if (!node || !node.childNodes || node.childNodes.length == 0) {
		return null;
	}
	var a = elfFilterTextNodes(node.childNodes);
	return (a.length == 0 ? null : a[a.length - 1]);
}

function elfBuildResults (node, ul, destinationID) {
	var li = document.createElement('li');
	ul.appendChild(li);
	var a = document.createElement('a');
	a.appendChild(document.createTextNode(node.getAttribute('title')));
	a.setAttribute('href', 'javascript:void(0);');
	var className = node.getAttribute('class');
	if (className) {
		a.className = className;
	}
	li.appendChild(a);
	var ulSub = document.createElement('ul');
	li.appendChild(ulSub);
	var childNodes = elfFilterTextNodes(node.childNodes);
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
				a.setAttribute('href', 'javascript:elfToggleLinkSelectionState(document.getElementById("' + aID + '"), document.getElementById("' + destinationID + '"));');
				a.setAttribute('element', id);
				if (className) {
					a.className = className;
				}
				if (description) {
					a.setAttribute('title', description);
				}
				a.appendChild(document.createTextNode(title));
				li.appendChild(a);
				ulSub.appendChild(li);
				break;
			case 'node':
				elfBuildResults(child, ulSub, destinationID);
				break;
		}
	}
}

function elfToggleLinkSelectionState (link, destination) {
	elfSetLinkSelected(link, (link.getAttribute('selected') ? false : true), destination);
}

function elfSetLinkSelected (link, selected, destination) {
	if (selected) {
		link.setAttribute('selected', 1);
		link.style.backgroundColor = elfLocale['SelectedColor'];
		if (destination) {
			elfSelectedElements[destination.getAttribute('id')] = elfAddToArray(elfSelectedElements[destination.getAttribute('id')], link);
		}
	}
	else {
		link.removeAttribute('selected');
		link.style.backgroundColor = 'transparent';
		if (destination) {
			elfSelectedElements[destination.getAttribute('id')] = elfRemoveFromArray(elfSelectedElements[destination.getAttribute('id')], link);
		}
	}
	if (destination) {
		var button = document.getElementById(destination.getAttribute('id')+'_button');
		button.disabled = (elfSelectedElements[destination.getAttribute('id')].length <= 0);
	}
}

function elfActivate (inactive, active) {
	var toActivate = elfSelectedElements[inactive.getAttribute('id')];
	if (!toActivate || !toActivate.length) return;
	var hiddenElmt = document.getElementById(active.getAttribute('id')+'_hidden');
	var cached = elfUnserialize(hiddenElmt.getAttribute('value'));
	for (var j = 0; j < toActivate.length; j++) {
		var link = toActivate[j];
		var id = link.getAttribute('element');
		var description = link.getAttribute('title');
		var label = link.firstChild.nodeValue;
		elfActivateElement(id, label, active, description);
		elfSetLinkSelected(link, false);
		elfSetLinkEnabled(link, false);
		cached = elfAddToArray(cached, id + ":" + label);
	}
	hiddenElmt.setAttribute('value', elfSerialize(cached));
	toActivate.length = 0;
	document.getElementById(inactive.getAttribute('id')+'_button').disabled = true;
}

function elfDeactivate (active, inactive) {
	var toDeactivate = elfSelectedElements[active.getAttribute('id')];
	if (!toDeactivate || !toDeactivate.length) return;
	var hiddenElmt = document.getElementById(active.getAttribute('id')+'_hidden');
	var cached = elfUnserialize(hiddenElmt.getAttribute('value'));
	for (var j = 0; j < toDeactivate.length; j++) {
		var link = toDeactivate[j];
		var id = link.getAttribute('element');
		var label = link.firstChild.nodeValue;
		var otherLink = document.getElementById(link.getAttribute('id').replace('_active_', '_inactive_'));
		elfDeactivateElement(id, active);
		if (otherLink) {
			elfSetLinkEnabled(otherLink, true);
		}
		cached = elfRemoveFromArray(cached, id + ":" + label);
	}
	hiddenElmt.setAttribute('value', elfSerialize(cached));
	toDeactivate.length = 0;
	document.getElementById(active.getAttribute('id')+'_button').disabled = true;
}

function elfActivateElement (element, label, activeList, description) {
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
	a.setAttribute('href', 'javascript:elfToggleLinkSelectionState(document.getElementById("' + aID + '"),document.getElementById("' + containerID + '"));');
	if (description) {
		a.setAttribute('title', description);
	}
	a.setAttribute('element', element);
	a.appendChild(document.createTextNode(label));
	li.appendChild(a);
	ul.appendChild(li);
}

function elfDeactivateElement (element, activeList) {
	var ul = activeList.firstChild;
	for (var i = 0; i < ul.childNodes.length; i++) {
		var el = ul.childNodes[i];
		if (el.firstChild.getAttribute('element') == element) {
			ul.removeChild(el);
			break;
		}
	}
}

function elfSetLinkEnabled (link, enabled) {
	var href = link.getAttribute('href');
	var realHref = link.getAttribute('realHref');
	if (enabled) {
		if (realHref) {
			link.setAttribute('href', realHref);
			link.removeAttribute('realHref');
			link.style.backgroundColor = '';
		}
	}
	else if (href) {
		link.setAttribute('realHref', href);
		link.setAttribute('href', 'javascript:void(0);');
		link.style.backgroundColor = elfLocale['DisabledColor'];
	}
}

function elfAddToArray (array, element) {
	var newArray = (array ? elfCloneArray(array) : new Array());
	if (elfArrayContains(array, element)) return newArray;
	newArray[newArray.length] = element;
	return newArray;
}

function elfRemoveFromArray (array, element) {
	var newArray = new Array();
	if (!array) return newArray;
	for (var i = 0; i < array.length; i++) {
		if (array[i] != element) {
			newArray[newArray.length] = array[i];
		}
	}
	return newArray;
}

function elfCloneArray (array) {
	var newArray = new Array();
	if (!array) return newArray;
	for (var i = 0; i < array.length; i++) {
		newArray[i] = array[i];
	}
	return newArray;
}

function elfArrayContains (array, element) {
	if (!array) return false;
	for (var i = 0; i < array.length; i++) {
		if (array[i] == element) {
			return true;
		}
	}
	return false;
}

function elfFind (query, searchURL, origin, destination) {
	var destID = destination.getAttribute('id');
	query = elfStripWhitespace(query);
	if (query.length > 0 && query == elfLastSearches[destID]) {
		return;
	}
	if (elfTimeouts[destID]) {
		clearTimeout(elfTimeouts[destID]);
		elfTimeouts[destID] = null;
	}
	if (query.length == 0) {
		elfEmptyNode(destination);
		return;
	}
	elfLastSearches[destID] = query;
	elfTimeouts[destID] = setTimeout(function () {
		new ElementFinderSearch(searchURL + '?query=' + escape(query) + elfExcludeString(origin), origin, destination);
	}, elfSearchDelay);
}

function elfStripWhitespace (str) {
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

function elfExcludeString (destination) {
	var elmt = document.getElementById(destination.getAttribute('id')+'_hidden');
	var chunks = elfUnserialize(elmt.getAttribute('value'));
	var str = '';
	for (var i = 0; i < chunks.length; i++) {
		var separatorIndex = chunks[i].indexOf(':');
		str += "&exclude[]=" + chunks[i].substring(0, separatorIndex);
	}
	return str;
}

function elfRestoreFromCache (hiddenElmt, active) {
	var cached = elfUnserialize(hiddenElmt.getAttribute('value'));
	for (var j = 0; j < cached.length; j++) {
		var separatorIndex = cached[j].indexOf(':');
		elfActivateElement(cached[j].substring(0, separatorIndex), cached[j].substring(separatorIndex + 1), active);
	}
}

function elfSerialize (elements) {
	if (!elements.length) {
		return "";
	}
	var string = elements[0];
	for (var i = 1; i < elements.length; i++) {
		string += "\t" + elements[i];
	}
	return string;
}

function elfUnserialize (elements) {
	if (!elements.length)
	{
		return new Array();
	}
	return elements.split("\t");
}

function elfGetAjaxObject () {
	return elfAjaxMethods[elfAjaxMethodIndex]();
}