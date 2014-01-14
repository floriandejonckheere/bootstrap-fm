/*
 * bootstrap-fm.js - model
 *
 * */

//http://stackoverflow.com/questions/15900485/correctly-way-to-convert-size-in-bytes-to-kb-mb-gb-in-javascript
function bytesToSize(bytes)
{
	var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return '0 B';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
};

function timestampToDate(time)
{
	var date = new Date(time * 1000);
	var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
	return date.getDate() + ' ' +
		months[date.getMonth()] + ' ' +
		date.getFullYear() + ' ' +
		(date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':' +
		(date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes());
}

$(document).ready(function(){
	/*
	 * FileEntry - file entry
	 * title	file-/directoryname (string)
	 * owner	(string)
	 * group	(string)
	 * perms	10-character string (string)
	 * size		in bytes (integer)
	 * timestamp	Unix timestamp (integer)
	 *
	 * */
	function FileEntry(title, owner, group, perms, size, timestamp)
	{
		var self = this;
		self.title	= ko.observable(title);
		self.owner	= ko.observable(owner);
		self.group	= ko.observable(group);
		self.perms	= ko.observable(perms);
		self.directory	= ko.observable(self.perms().substring(0,1) == 'd' ? true : false);
		self.size	= ko.observable((self.directory() ? '-' : bytesToSize(size)));
		self.timestamp	= ko.observable(timestampToDate(timestamp));
	}

	function FileListViewModel()
	{
		var self = this;
		self.entries = ko.observableArray([
						new FileEntry('File', 'florian', 'users', '-rw-r--r--', 1000000, 1234567890),
						new FileEntry('Folder', 'florian', 'users', 'drw-r--r--', 0, 1234567890)
						]);
	}

	ko.applyBindings(new FileListViewModel());
});
