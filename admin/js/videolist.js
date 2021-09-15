
	function changeThumb(id,links_str,index)
	{
		var links=links_str.split(";");
		var objImages=document.getElementById('thumbnails'+id);
		var content='';
		var image='';
		
		for(var i=0;i<links.length;i++)
		{
			var parts=links[i].split(',');
			
					
			if(i==index)
			{
				content+=i+'  ';
				var img=parts[0];
				
				var obj2=document.getElementById('thumbnail'+id);
				obj2.innerHTML='<a href="'+img+'" target="_blank"><img src="'+img+'" style="width:200px;" /></a>';
			}
			else
			{
				//show another thumbnail image on link click
				var link='changeThumb('+id+',\''+links_str+'\','+i+')';
				var alt='Thumbnail';
				
				if(parts.length==3)
					alt+=' '+parts[1]+'x'+parts[2];
				
				content+='<a href="javascript:'+link+';" alt="'+alt+'" title="'+alt+'" />'+i+'  </a>';
			}
		}
		objImages.innerHTML=content;
	}
	
	function getAnswerValue(p,s)
	{
		var ps="*"+p+"_start*=";
		var pe="*"+p+"_end*";

		var 	i1=s.indexOf(ps);
		if(i1==-1)
			return "";

		var 	i2=s.indexOf(pe,i1+ps.length);
		if(i2==-1)
			return "";

		return s.substring(i1+ps.length,i2);

	}

	function YGgetURlContent(theUrl,videoid,itemid)
	{
		var xmlHttp = null;

		xmlHttp = new XMLHttpRequest();
		xmlHttp.onreadystatechange = function()
		{
			if (xmlHttp.readyState == 4)
			{
				YGpostURlContent(videoid,xmlHttp.responseText,itemid);
			}
		};

		if(isHTTPS)
			theUrl=theUrl.replace("http://","https://");
		
		xmlHttp.open( "GET", theUrl, true );
		xmlHttp.send( null );
	}

	function YGpostURlContent(videoid,ygvdata,itemid)
	{
		var url = "index.php";
		$.post( url, { option: "com_youtubegallery", view: "updatedata", tmpl: "component", videoid: videoid, ygvdata: ygvdata })
		.done(function( data ) {
		  UpdateFormData(data,itemid)
		});



	}

	function UpdateVideoData(link,videoid,itemid)
	{
		var progressImage='<img src="../components/com_youtubegallery/images/progress_circle.gif" style="border:none !important;" />';
		document.getElementById("video_"+itemid+"_status").innerHTML=progressImage;
		YGgetURlContent(link,videoid,itemid);

	}

	function UpdateFormData(answer,itemid)
	{
		var video_title=getAnswerValue("title",answer);
		var video_description=getAnswerValue("description",answer);
		var video_lastupdate=getAnswerValue("lastupdate",answer);

		document.getElementById("video_"+itemid+"_title").innerHTML=video_title;
		document.getElementById("video_"+itemid+"_description").innerHTML=video_description;
		document.getElementById("video_"+itemid+"_lastupdate").innerHTML=video_lastupdate;

		if(video_lastupdate!="")
			document.getElementById("video_"+itemid+"_status").innerHTML='<span style="color:green;">Ok</span>';
		else
			document.getElementById("video_"+itemid+"_status").innerHTML='<span style="color:red;font-weight:bold;">No data</span>';
	}