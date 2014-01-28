/*
 * bootstrap-fm.js - model
 *
 * */

/*
 * Helper functions
 *
 * */
function bytesToSize(bytes)
{
	var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return '0 B';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
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
	$('#error-message').parent().hide();
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
	function FileListEntry(name, owner, group, perms, size, timestamp)
	{
		var self = this;
		self.name	= ko.observable(name);
		self.owner	= ko.observable(owner);
		self.group	= ko.observable(group);
		self.perms	= ko.observable(perms);
		self.directory	= ko.observable(self.perms().substring(0,1) == 'd' ? true : false);
		self.size	= ko.observable((self.directory() ? '' : bytesToSize(size)));
		self.timestamp	= ko.observable(timestampToDate(timestamp));
	}

	function FileListViewModel()
	{
		var self = this;

		self.entries	 = ko.observableArray();
		self.breadcrumbs = ko.observableArray();

		self.query	 = ko.observable('');
		// Origin: breadcrumb
		self.visit = function(data)
		{
			self.query(self.breadcrumbs.slice(1, self.breadcrumbs().indexOf(data) + 1).join('/'));
			self.refresh();
		}

		// Origin: FileListEntry
		self.subdir = function(data)
		{
			var path = (self.breadcrumbs().length > 1 ? self.breadcrumbs().slice(1, self.breadcrumbs().length).join('/') + '/' : '');

			if(!data.directory())
				window.location.href = 'php/download.php?file=' + path + data.name();
			
			self.query(path + data.name());
			self.refresh();
		}

		self.refresh = function()
		{
			console.log('php/list.php?start=2&count=20&dir=' + self.query());
			$.getJSON('php/list.php?start=2&count=20&dir=' + encodeURIComponent(self.query()), function(data){
				console.log(data);
				if(data.error)
				{
					switch(data.error)
					{
						case 404:	$('#error-message').html('That directory was not found.').parent().show();
								return;
						case 403:	$('#error-message').html('You do not have permission to list that directory').parent().show();
								return;
						default:	$('#error-message').html('Unknown error').parent().show();
								return;
					}
				}
				self.breadcrumbs(data.path.split('/'));
				console.log(self.breadcrumbs());
				console.log(self.breadcrumbs().length - 1);
				self.entries().length = 0;
				for(i = 0 ; i < data.entries.length ; ++i)
				{
					var entry = data.entries[i];
					self.entries.push(new FileListEntry(entry['name'], entry['owner'], entry['group'], entry['permissions'], entry['size'], entry['timestamp']));
				}
			});
		}
	}

	var fModel = new FileListViewModel();
	fModel.refresh();
	ko.applyBindings(fModel);
});
