/**
 * Youtube Gallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

/**
 * YG behavior for editor modal gallery selector
 */

(function ($, doc)
{
	'use strict';

	window.YG = {

		/**
		 * Initialization
		 *
		 * @return  void
		 */
		initialize: function ()
		{
			var o = this.getUriObject(window.self.location.href),
				q = this.getQueryObject(o.query);

			this.frameurl = location.href;
		
			this.editor = q.e_name;

		},

	
		onok: function ()
		{
			var videolist=document.getElementById("vidoelistselector").value;
			var theme=document.getElementById("themeselector").value;
			
			var tag = '{youtubegalleryid='+videolist+','+theme+'}';
			
			/** Use the API, if editor supports it **/
			if (window.Joomla && Joomla.editors.instances.hasOwnProperty(this.editor)) {
				Joomla.editors.instances[editor].replaceSelection(tag);
			} else {
				window.parent.jInsertEditorText(tag, this.editor);
			}

			return true;
		},

		updatePreview: function ()
		{
			var videolist=document.getElementById("vidoelistselector").value;
			var theme=document.getElementById("themeselector").value;
			
			var html_string= '<span style="color:#aaaaaa;">Loading...</span>';
			
			document.getElementById("YGVideoLinks").src= "data:text/html;charset=utf-8," + escape(html_string);
			document.getElementById("YGPreview").src= "data:text/html;charset=utf-8," + escape(html_string);
			
			if(videolist==0)
				document.getElementById('yginsertbutton').disabled=true;
			else
				document.getElementById('yginsertbutton').disabled=false;
				
			var videolist_url=ygSiteBase+'index.php?option=com_youtubegallery&view=linksform&layout=edit&tmpl=component&id='+videolist;
			setTimeout(function(){
				document.getElementById("YGVideoLinks").src=videolist_url;
			}, 200);

			var preview_url=ygSiteBase+'index.php?option=com_youtubegallery&view=listandthemeselection&tmpl=component&task=preview&videolist='+videolist+'&theme='+theme;
			setTimeout(function(){
				document.getElementById("YGPreview").src=preview_url;
			}, 200);
			
		},
		
		showMessage: function (text)
		{
			var $message = $('#message');

			$message.find('>:first-child').remove();
			$message.append(text);
			$('#messages').css('display', 'block');
		},

		refreshFrame: function ()
		{
			var videolist=document.getElementById("vidoelistselector").value;
			var themeid=document.getElementById("themeselector").value;
			
			
			document.getElementById('YGPreviewMessageBox').innerHTML="Loading videos...";
			document.getElementById('YGVideoLinksDiv').style.display="none";
			document.getElementById('YGPreviewDiv').style.display="none";
						
			var url=this.frameurl;
			if(url.indexOf("?")==-1)
				url+='?';
			else
				url+='&';
			
			if(videolist=="")
				url+='showlatestvideolist=1';
			else
				url+='videolistid='+videolist;
			
			url+='&themeid='+themeid;
			
			location.href=url;
		},

		getQueryObject: function (q)
		{
			var rs = {};

			$.each((q || '').split(/[&;]/), function (key, val)
			{
				var keys = val.split('=');

				rs[ decodeURIComponent(keys[0]) ] = keys.length == 2 ? decodeURIComponent(keys[1]) : null;
			});

			return rs;
		},

		/**
		 * Break a url into its component parts
		 *
		 * @param   string  u  URL
		 *
		 * @return  object
		 */
		getUriObject: function (u)
		{
			var bitsAssociate = {},
				bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);

			$.each(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'], function (key, index)
			{
				bitsAssociate[index] = (!!bits && !!bits[key]) ? bits[key] : '';
			});

			return bitsAssociate;
		}
	};

	$(function ()
	{
		window.YG.initialize();
	});

}(jQuery, document));
