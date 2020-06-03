/*
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 */


	var  videolist_textarea= '';

			var channels_youtube=new Array('youtubeuseruploads','youtubestandard','youtubeplaylist','youtubeuserfavorites','youtubesearch','youtubeshow*','youtubeshow','youtubechannel');
			var channels_other=new Array('vimeouservideos','vimeochannel','vimeoalbum','dailymotionplaylist');
			var channels_vimeo=new Array('vimeouservideos','vimeochannel','vimeoalbum');
			var single_videos=new Array('youtube','vimeo','own3dtvlive','own3dtvvideo','google','yahoo','break','collegehumor','dailymotion','presentme','ustream','ustreamlive','soundcloud','.flv','tiktok');

			var channels_youtube_title=new Array('Youtube User Uploads','Youtube Standard Feed','Youtube Playlist','Youtube User Favorites','Youtube Search','Youtube Show','Youtube Show','Youtube Channel');
			var channels_other_title=new Array('Vimeo User Uploads','Vimeo Channel','Vimeo Album','Dailymotion Playlist');
			var single_videos_title=new Array('Youtube','Vimeo','Own3DtvLive','Own3dtvVideo','Google','Yahoo','Break','CollegeHumor','Dailymotion','Present.me','UStream Recorded','UStream Live','SoundCloud','.flv file','Tik Tok');
			
			var simple_mode=false;


			function submitSimpleForm(force_to_save)
			{
				if(force_to_save)
				{
					hideModalAddVideoForm();	
					Joomla.submitbutton('linksform.apply');
				}
				else
				{
					var obj=document.getElementById("jform_listname");
					if(obj && obj.value!='')
					{
						hideModalAddVideoForm();	
						Joomla.submitbutton('linksform.apply');
					}
				}
			}


			function YGGetTypeTitle(link_type)
			{
				for (var i = 0; i < channels_youtube.length; i++)
				{
				    if (channels_youtube[i] === link_type)
				        return channels_youtube_title[i];
				}

				for (var i = 0; i < channels_other.length; i++)
				{
				    if (channels_other[i] === link_type)
				        return channels_other_title[i];
				}

				for (var i = 0; i < single_videos.length; i++)
				{
				    if (single_videos[i] === link_type)
				        return single_videos_title[i];
				}

				return 'Unidentified';
			}

			function YGgetVideoSourceName(link)
			{


		if(link.indexOf("://youtube.com")!=-1 || link.indexOf('://www.youtube.com')!=-1)
		{
			if(link.indexOf('/playlist')!=-1)
				return 'youtubeplaylist';
			else if(link.indexOf('/favorites')!=-1)
				return 'youtubeuserfavorites';
			else if(link.indexOf('/user')!=-1)
				return 'youtubeuseruploads';
			else if(link.indexOf('/results')!=-1)
				return 'youtubesearch';
			else if(link.indexOf('://www.youtube.com/show/')!=-1)
				return 'youtubeshow*';
			else if(link.indexOf('://www.youtube.com/channel/')!=-1)
				return 'youtubechannel';
			else
				return 'youtube';
		}

		if(link.indexOf('://youtu.be')!=-1 || link.indexOf('://www.youtu.be')!=-1)
			return 'youtube';

		if(link.indexOf('youtubestandard:')!=-1)
			return 'youtubestandard';

		if(link.indexOf('videolist:')!=-1)
			return 'videolist';


		if(link.indexOf('://vimeo.com/user')!=-1 || link.indexOf('://www.vimeo.com/user')!=-1)
			return 'vimeouservideos';
		else if(link.indexOf('://vimeo.com/channels/')!=-1 || link.indexOf('://www.vimeo.com/channels/')!=-1)
			return 'vimeochannel';
		else if(link.indexOf('://vimeo.com/album/')!=-1 || link.indexOf('://www.vimeo.com/album/')!=-1)
			return 'vimeoalbum';
		else if(link.indexOf('://vimeo.com')!=-1 || link.indexOf('://www.vimeo.com')!=-1)
			return 'vimeo'; //return 'vimeo*friendlylink';

		if(link.indexOf('://own3d.tv/l/')!=-1 || link.indexOf('://www.own3d.tv/l/')!=-1)
			return 'own3dtvlive';

		if(link.indexOf('://own3d.tv/v/')!=-1 || link.indexOf('://www.own3d.tv/v/')!=-1)
			return 'own3dtvvideo';


		if(link.indexOf('video.google.com')!=-1)
			return 'google';

		if(link.indexOf('video.yahoo.com')!=-1)
			return 'yahoo';

		if(link.indexOf('://break.com')!=-1 || link.indexOf('://www.break.com')!=-1)
			return 'break';


		if(link.indexOf('://collegehumor.com')!=-1 || link.indexOf('://www.collegehumor.com')!=-1)
			return 'collegehumor';

		//http://www.dailymotion.com/playlist/x1crql_BigCatRescue_funny-action-big-cats/1#video=x7k9rx
		if(link.indexOf('://dailymotion.com/playlist/')!=-1 || link.indexOf('://www.dailymotion.com/playlist/')!=-1)
			return 'dailymotionplaylist';

		if(link.indexOf('://dailymotion.com')!=-1 || link.indexOf('://www.dailymotion.com')!=-1)
			return 'dailymotion';

		if(link.indexOf('://present.me')!=-1 || link.indexOf('://www.present.me')!=-1)
			return 'presentme';

		if(link.indexOf('://tiktok.com/')!=-1 || link.indexOf('://www.tiktok.com/')!=-1)
			return 'tiktok';

		if(link.indexOf('://ustream.tv/recorded/')!=-1 || link.indexOf('://www.ustream.tv/recorded/')!=-1)
			return 'ustream';

		if(link.indexOf('://ustream.tv/channel/')!=-1 || link.indexOf('://www.ustream.tv/channel/')!=-1)
			return 'ustreamlive';


		//http://api.soundcloud.com/tracks/49931.json  - accepts only resolved links
		if(link.indexOf('://api.soundcloud.com/tracks/')!=-1 )
			return 'soundcloud';

		//https://soundcloud.com/newyfreshmusic/katy-perry-dark-horse-ft-juicy
		if(link.indexOf('://soundcloud.com')!=-1 || link.indexOf('://www.soundcloud.com')!=-1)
			return 'soundcloud*';

		if(link.toLowerCase().indexOf('.flv')!=-1)
			return '.flv';

		return '';
	}


			function YGAddFormatedLink(isSingle,link,editIndex)
			{
				var obj_source=document.getElementById(videolist_textarea);
				var osv=obj_source.value;

				if(editIndex!=-1)
				{
					var lines = obj_source.value.split(/\r\n|\r|\n/g);
					var newList='';

					for(i=0;i<lines.length;i++)
					{
						if(i==editIndex)
						{
							if(newList!='')
								newList+="\r\n";

							newList+=link;
						}
						else
						{
							if(newList!='')
								newList+="\r\n";

							newList+=lines[i];
						}

					}

					obj_source.value=newList;
					YGUpdatelinksTable();
					return true;
				}
				else
				{


					if(isSingle)
					{
						obj_source.value=obj_source.value+"\r\n"+link;
						YGUpdatelinksTable();
						return true;
					}
					else
					{
						if(osv.indexOf(link)==-1)
						{
							var v=obj_source.value;
							if(v!='')
								v+="\r\n";

							obj_source.value=v+link;

							YGUpdatelinksTable();
							return true;
						}
						else
							alert("This link is already in the list.");
					}
				}
				return false;
			}

			function YGgetValueOfParameter(r,p)
			{

				var i=r.indexOf(p);
				if(i==-1)
					return false;

				var a=r.indexOf('"',i+p.length);
				if(a==-1)
					return false;

				return r.substring(i,a-i);

			}

			function YGLoadListOfSeasons(showId)
			{
				var xmlHttp = new XMLHttpRequest();

				var maxResults=5;

				var Seasons=[];
				var p='.';

					p+='.';
					url='components/com_youtubegallery/views/linksform/tmpl/requests.php?task=getyoutubeseasonsbyshowid&showid='+showId+'&maxResults='+maxResults;

					xmlHttp.open( "GET", url, false);
					xmlHttp.send(null);
					var r=xmlHttp.responseText;

					list = JSON && JSON.parse(r) || $.parseJSON(r);


					for(i=0;i<list.length;i++)
						Seasons[Seasons.length]=list[i];

				return Seasons;
			}

			function YGResolveYoutubeShowLink(link)
			{
				link=link.replace('https://', 'http://');

				var url='components/com_youtubegallery/views/linksform/tmpl/requests.php?task=getyoutubeshowowner&link='+link;
				//get user id

				var xmlHttp = new XMLHttpRequest();

				xmlHttp.open( "GET", url, false);
				xmlHttp.send(null);
				var r=xmlHttp.responseText;

				if(r.indexOf('{"')==-1)
					return false;

				var obj = JSON && JSON.parse(r) || $.parseJSON(r);


				//get list of shows
				var list;
				var maxResults=10;

				var showId='';
				var p='.';

					p+='.';
					url='components/com_youtubegallery/views/linksform/tmpl/requests.php?task=getyoutubeshowsbyowner&owner='+obj.username+'&maxResults='+maxResults;

					xmlHttp.open( "GET", url, false);
					xmlHttp.send(null);
					var r2=xmlHttp.responseText;

					list = JSON && JSON.parse(r2) || $.parseJSON(r2);

					for(i=0;i<list.length;i++)
					{
						var a=list[i];
						if(a.link[0]==link)
						{
							showId=a.id[0];
							break;
						}
					}

				var pair=showId.split(':');
				if(pair.length!=4)
				{
					alert('Connection problem. Try again.');
					return false;
				}
				showId=pair[3];


				//Get List Of Seasons -----------------------------------------
				Seasons=YGLoadListOfSeasons(showId);

				YGBuildShowSeasonsDialog(link, obj.username,showId,Seasons,-1);
				return true;

			}

			function YGResolveSoundCloudLink(link)
			{
				var client_id=YGGetSoundCloudClientID();
				if(client_id=='')
				{
					alert('SoundCloud Client ID not set. Go to "Youtube Gallery / Settings"');
					return false;
				}

				var theUrl='';
				YGAddShadowLabel("Resolving link...");
				theUrl='components/com_youtubegallery/views/linksform/tmpl/requests.php?task=resolvesoundcloudlink&url='+link+'&client_id='+client_id;

				var xmlHttp = new XMLHttpRequest();
				xmlHttp.open( "GET", theUrl, false);
				xmlHttp.send( null );
				var r=xmlHttp.responseText;

				if(r.indexOf('{"')!=-1)
				{

					var obj = JSON.parse(r);
					if(obj.kind=='track')
					{
						link='http://api.soundcloud.com/tracks/'+obj.id+'.json';
						link_type=YGgetVideoSourceName(link);
						if(link_type!='soundcloud')
						{
							alert("Something went wrong. Try again.");
							return false;
						}
					}
					else
					{
						alert("This type of SoundCloud ("+obj.kind+") link is not supported.");
						return false;
					}

					return link;
				}
				else
				{
					alert("This type of SoundCloud link is not supported. Reason: "+r);
					return false;
				}


			}

			function YGAddLink()
			{

				//var link='http://www.youtube.com/show/nammalthammil';//
				var link=prompt("Please enter a Link to your Video, Playlist or Channel","");
				if (link!=null)
				{
					var link_type=YGgetVideoSourceName(link);

					if(link_type=='')
					{
						alert("This type of links are not supported.");
						return false;
					}
					else
					{
						if(link_type.indexOf('*')!=-1)
						{
							//resolve link
							if(link_type=='soundcloud*')
							{
								link=YGResolveSoundCloudLink(link);
								if(!link)
									return false;

								link_type='soundcloud';
							}

							if(link_type=='youtubeshow*')
							{
								YGResolveYoutubeShowLink(link);
								return true;
							}

						}

						if(YGisSingleVideo(link_type))
						{
							var obj_source=document.getElementById(videolist_textarea);
							var osv=obj_source.value;
							var item=CSVtoArray(link);

							if(osv.indexOf(item[0])==-1)
								YGBuildSingleVideoDialog(link,link_type,-1);
							else
							{
								alert("This link is already in the list.");
								return false;
							}

						}
						else
							YGBuildListVideoDialog(link,link_type,-1);
					}


				}
				return true;
			}

			function YGAddSaveCloseButtons(link,editIndex,isSingleVideo,link_type)
			{
				var FormContent='';

				FormContent+='<div style="text-align:center;margin-top:20px;">';//width:180px;margin: 20px auto;position:relative;"><div class="-wrapper" style="position:absolute;left:0;top:0;" >';

				var startend=false;
				if(link_type=='youtube' || YGcontains(link_type,channels_youtube))
					startend=true;

				if(editIndex==-1)
				{
					if(isSingleVideo)
						FormContent+='<button onclick="YGFormatSingleLink(\''+link+'\',\''+editIndex+'\','+startend+')" class="btn btn-small btn-success" type="button">';
					else
						FormContent+='<button onclick="YGFormatListLink(\''+link+'\',\''+editIndex+'\',\''+link_type+'\')" class="btn btn-small btn-success" type="button">';

					FormContent+='<span class="icon-new icon-white"></span><span style="margin-left:10px;">Add</span></button>';
				}
				else
				{
					if(isSingleVideo)
						FormContent+='<button onclick="YGFormatSingleLink(\''+link+'\',\''+editIndex+'\','+startend+')" class="btn btn-small" type="button">';
					else
						FormContent+='<button onclick="YGFormatListLink(\''+link+'\',\''+editIndex+'\',\''+link_type+'\')" class="btn btn-small" type="button">';

					FormContent+='<span class="icon-save"></span><span style="">Save</span></button>';//margin-left:10px;
				}

				FormContent+='</div>';

				return FormContent;
			}

			function YGbuildForm(width,height,title,FormContent)
			{


				var obj=document.getElementById("layouteditor_modal_content_box");
				obj.innerHTML=FormContent;//+'<br/>';

				showModal();


			}


		function showModal()
		{
            // Get the modal
            var modal = document.getElementById('layouteditor_Modal');

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("layouteditor_close")[0];

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            };

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            };

            var box=document.getElementById("layouteditor_modalbox");

            modal.style.display = "block";

            var d = document;
            e = d.documentElement;

            var doc_w=e.clientWidth;
            var doc_h=e.clientHeight;

            var w=box.offsetWidth;
            var h=box.offsetHeight;

            var x= (doc_w/2)-w/2;
            if(x<10)
                x=10;

            if(x+w+10>doc_w)
                x=doc_w-w-10;

            var y=(doc_h/2)-h/2;

            if(y<50)
                y=50;


            if(y+h+50>doc_h)
                y=doc_h-h-50;


			if(y<0)
				y=0;

			box.style.position = "absolute";
            box.style.left=x+'px';
            box.style.top=y+'px';
		}


			function YGgetBasicValues(isSingle,link,SpecialParameters,startendsecond,usergroup)
			{
				var title=document.getElementById("ygcustomtitle").value;
				
				var description="";
				var ygcustomdescription=document.getElementById("ygcustomdescription");
				if(ygcustomdescription)
					description=ygcustomdescription.value;
				
				var image="";
				var ygcustomimage=document.getElementById("ygcustomimage");
				if(ygcustomimage)
					image=ygcustomimage.value;
				
				title=title.replace(/["']/g, "");
				description=description.replace(/["']/g, "");
				image=image.replace(/["']/g, "");

				var startsecond="";
				var endsecond="";
				
				if(startendsecond)
				{
					startsecond=document.getElementById("startsecond").value;
					
					var endsecond_obj=document.getElementById("endsecond");
					if(endsecond_obj)
						endsecond=endsecond_obj.value;
					
					startsecond=startsecond.replace(/["']/g, "");
					endsecond=endsecond.replace(/["']/g, "");
					startsecond=startsecond.replace(/[^\d.]/g, "");
					endsecond=endsecond.replace(/[^\d.]/g, "");
				}

				var new_link=link;
				if(title!='')
					new_link+=',"'+title+'"';
				else if(description!='' || image!='' || SpecialParameters!='' || startsecond!='' || endsecond!='' || (usergroup!='0' && usergroup!='1'))
					new_link+=',';

				if(description!='')
					new_link+=',"'+description+'"';
				else if(image!='' || SpecialParameters!='' || startsecond!='' || endsecond!='' || (usergroup!='0' && usergroup!='1'))
					new_link+=',';

				if(image!='')
					new_link+=',"'+image+'"';
				else if(SpecialParameters!='' || startsecond!='' || endsecond!='' || (usergroup!='0' && usergroup!='1'))
					new_link+=',';


				if(SpecialParameters!='')
					new_link+=',"'+SpecialParameters+'"';
				else if(startsecond!='' || endsecond!='' || (usergroup!='0' && usergroup!='1'))
					new_link+=',';

				if(startendsecond)
				{
					if(startsecond!='')
						new_link+=','+startsecond;
					else if(endsecond!='' || (usergroup!='0' && usergroup!='1'))
						new_link+=',';

					if(endsecond!='' || (usergroup!='0' && usergroup!='1'))
						new_link+=','+endsecond;
				}else if(usergroup!='0' && usergroup!='1')
						new_link+=',,';

				if(usergroup!='0' && usergroup!='1')
					new_link+=','+usergroup;



				return new_link;
			}

			function YGFormatSingleLink(link,editIndex,startendsecond)
			{
				var usergroup='';
				var ygwatchgroup=document.getElementById("ygwatchgroup");
				if(ygwatchgroup)
					usergroup=ygwatchgroup.value;
				
				var new_link=YGgetBasicValues(true,link,'',startendsecond,usergroup);
				YGAddFormatedLink(true,new_link,editIndex);
				//var obj=document.getElementById("YGDialog");
				//obj.innerHTML='';
				//obj.style.display="none";
				//YGShadeOn(false);

				var modal = document.getElementById('layouteditor_Modal');
				//modal.innerHTML='';
				modal.style.display = "none";
				
				if(simple_mode)
					submitSimpleForm(true);
			}


			function YGFormatListLink(link,editIndex,link_type)
			{
				var title=document.getElementById("ygcustomtitle").value;
				
				var description="";
				var ygcustomdescription=document.getElementById("ygcustomdescription");
				if(ygcustomdescription)
					description=ygcustomdescription.value;
				
				var image="";
				var ygcustomimage=document.getElementById("ygcustomimage");
				if(ygcustomimage)
					image=ygcustomimage.value;
				
				var SpecialParameters='';
				var startendsecond=false;
				
				var usergroup='';
				var ygwatchgroup=document.getElementById("ygwatchgroup");
				if(ygwatchgroup)
					usergroup=ygwatchgroup.value;
				
				
				var season="";
				var content="";

				if(YGcontains(link_type,channels_youtube))
				{
					//SpecialParameters
					startendsecond=true;

					var maxresults=document.getElementById("maxresults").value;

					if(link_type=='youtubeshow')
					{
						season=document.getElementById("season").value;
						content=document.getElementById("contenttype").value;
					}
					maxresults=maxresults.replace(/["']/g, "");
					maxresults=maxresults.replace(/[^\d.]/g, "");


					if(maxresults!='')
						SpecialParameters+='maxResults='+maxresults;

					if(link_type=='youtubeshow')
					{
						if(season!='')
						{
							if(SpecialParameters!='')
								SpecialParameters+=',';

							SpecialParameters+='season='+season;
						}

						if(content!='')
						{
							if(SpecialParameters!='')
								SpecialParameters+=',';

							SpecialParameters+='content='+content;
						}
					}

					if(link_type=='youtubeuseruploads')
					{


						var moredetails=document.getElementById("moredetails").value;
						moredetails=moredetails.replace(/["']/g, "");

						if(moredetails!='')
						{
							if(SpecialParameters!='')
							SpecialParameters+=',';

							SpecialParameters+='moredetails=true';
						}

					}

				}

				if(YGcontains(link_type,channels_vimeo))
				{

					//SpecialParameters

					var per_page=document.getElementById("per_page").value;
					var page=document.getElementById("page").value;


					per_page=per_page.replace(/["']/g, "");
					page=page.replace(/["']/g, "");

					per_page=per_page.replace(/[^\d.]/g, "");
					page=page.replace(/[^\d.]/g, "");

					if(per_page!='')
					{
						SpecialParameters+='per_page='+per_page;
					}

					if(page!='')
					{
						if(SpecialParameters!='')
							SpecialParameters+=',';

						SpecialParameters+='page='+page;
					}

				}



				var new_link=YGgetBasicValues(false,link,SpecialParameters,startendsecond,usergroup);
				YGAddFormatedLink(false,new_link,editIndex);

				var modal = document.getElementById('layouteditor_Modal');
				modal.style.display = "none";
			}


			function YGAddVelues(item,count)
			{
				var new_item=[];
				var l=item.length;
				for (var i = 0; i < count; i++)
				{

					if(i>l-1)
						new_item[i]='';
					else
						new_item[i]=item[i];
				}
				return new_item;
			}

			function YGBuildSingleVideoDialog(link,link_type,editIndex)
			{

				var linkSplit=CSVtoArray(link);
				var item=YGAddVelues(linkSplit,8);
				var formHeight=300;
				var FormContent='<table style="width:90%;margin-left:20px;margin-top:20px;"><tbody>';

				var link_type_title=YGGetTypeTitle(link_type);

				FormContent+='<tr><td>Link</td><td>:</td><td style="word-break:break-all;width:380px;">'+item[0]+'</div></td></tr>';
				FormContent+='<tr><td>Type</td><td>:</td><td><b>'+link_type_title+'</b></td></tr>';
				FormContent+='<tr><td>Custom Title</td><td>:</td><td><input type="text" id="ygcustomtitle" class="inputbox" style="width:100%;" value="'+item[1]+'" /></td></tr>';
				if(!simple_mode)
				{
					FormContent+='<tr><td>Custom Description</td><td>:</td><td><input type="text" id="ygcustomdescription" class="inputbox" style="width:100%;" value="'+item[2]+'" /></td></tr>';
					FormContent+='<tr><td>Custom Thumbnail</td><td>:</td><td><input type="text" id="ygcustomimage" class="inputbox" style="width:100%;" value="'+item[3]+'" /></td></tr>';
				}

				if(link_type=='youtube')
				{
					formHeight=340;
					FormContent+='<tr><td>Start Second</td><td>:</td><td><input type="text" id="startsecond" class="inputbox" style="width:100%;" value="'+item[5]+'" /></td></tr>';
					
					if(!simple_mode)
						FormContent+='<tr><td>End Second</td><td>:</td><td><input type="text" id="endsecond" class="inputbox" style="width:100%;" value="'+item[6]+'" /></td></tr>';
				}

				formHeight+=40;
				var d=YGGetUserGroups();
				if(!simple_mode)
				{
					FormContent+='<tr><td>Watch Group</td><td>:</td><td>'+YGMakeWatchGroupBox(d,item[7])+'</td></tr>';
				}

				FormContent+='</tbody></table>';

				FormContent+=YGAddSaveCloseButtons(item[0],editIndex,true,link_type);

				if(link_type=='soundcloud')
					YGbuildForm(500,formHeight,"Single Audio Details",FormContent);
				else
					YGbuildForm(500,formHeight,"Single Video Details",FormContent);

			}


			function YGBuildSelectBox(id,values,titles,value)
			{
				var FormContent='<select id="'+id+'" class="inputbox" style="width:100%;">';

				for (var i = 0; i < values.length; i++)
				{
						FormContent+='<option value="'+values[i]+'"';

						if(values[i]==value)
							FormContent+=' SELECTED';

						FormContent+='>'+titles[i]+'</option>';
				}

				FormContent+='</select>';

				return FormContent;

			}



			function YGBuildShowSeasonsDialog(link,userid,showid,seasons,editIndex)
			{
				var linkSplit=CSVtoArray(link);

				var item=YGAddVelues(linkSplit,8);

				var FormContent='<table style="width:90%;margin-left:20px;margin-top:20px;"><tbody>';
				var formHeight=560;
				var sp=item[4].split(",");

				if(userid=='' && sp!='')
				{
					var p=YGGetValue(sp,'season').split(':');
					if(p.length==4)
					{
						userid=p[0];
						showid=p[1];

						//Load list of seasons
						seasons=YGLoadListOfSeasons(showid);
					}
					else
					{
						alert('Link format is corrupted.');
						return false;
					}
				}

				FormContent+='<tr><td style="width:150px;">Link</td><td>:</td><td><div style="vertical-align:middle !important;word-break:break-all;width:330px;height:30px;overflow:hidden;border:1px red;">'+item[0]+'</div></td></tr>';
				FormContent+='<tr><td>Type</td><td>:</td><td><b>Youtube Show</b></td></tr>';

				var Values=[];
				var Titles=[];

				for(i=0;i<seasons.length;i++)
				{
					Values[i]=''+userid+':'+showid+':'+seasons[i].id+':'+seasons[i].title[0];
					Titles[i]='Season '+seasons[i].title[0];
				}
				FormContent+='<tr><td><b>Season</b></td><td>:</td><td>'+YGBuildSelectBox('season',Values,Titles,YGGetValue(sp,'season'))+'</td></tr>';

				Values=new Array('','clips');//episodes - by default
				Titles=new Array('Episodes','Clips');
				FormContent+='<tr><td>Content</td><td>:</td><td>'+YGBuildSelectBox('contenttype',Values,Titles,YGGetValue(sp,'content'))+'</td></tr>';

				FormContent+='<tr><td>Custom Title</td><td>:</td><td><input type="text" id="ygcustomtitle" class="inputbox" style="width:100%;" value="'+item[1]+'" /></td></tr>';
				
				if(!simple_mode)
				{
					FormContent+='<tr><td>Custom Description</td><td>:</td><td><input type="text" id="ygcustomdescription" class="inputbox" style="width:100%;" value="'+item[2]+'" /></td></tr>';
					FormContent+='<tr><td>Custom Thumbnail</td><td>:</td><td><input type="text" id="ygcustomimage" class="inputbox" style="width:100%;" value="'+item[3]+'" /></td></tr>';
				}




					FormContent+='<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';

					FormContent+='<tr><td>Count</td><td>:</td><td><input type="text" id="maxresults" class="inputbox" style="width:100%;" value="'+YGGetValue(sp,'maxResults')+'" /></td></tr>';

/*
    date – Resources are sorted in reverse chronological order based on the date they were created.
    rating – Resources are sorted from highest to lowest rating.
    relevance – Resources are sorted based on their relevance to the search query. This is the default value for this parameter.
    title – Resources are sorted alphabetically by title.
    videoCount – Channels are sorted in descending order of their number of uploaded videos.
    viewCount – Resources are sorted from highest to lowest number of views. For live broadcasts, videos are sorted by number of concurrent viewers while the broadcasts are ongoing.
*/

					var OrderByValues=new Array('','date','rating','relevance','title','viewCount');
					var OrderByTitles=new Array('-','Chronological Order','Highest to lowest rating','Relevance to the search query','Alphabetically by title','Highest to lowest number of views');
					FormContent+='<tr><td>Order By</td><td>:</td><td>'+YGBuildSelectBox('ygorderby',OrderByValues,OrderByTitles,YGGetValue(sp,'orderby'))+'</td></tr>';






					FormContent+='<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';


					FormContent+='<tr><td>Start Second</td><td>:</td><td><input type="text" id="startsecond" class="inputbox" style="width:100%;" value="'+item[5]+'" /></td></tr>';
					
					if(!simple_mode)
						FormContent+='<tr><td>End Second</td><td>:</td><td><input type="text" id="endsecond" class="inputbox" style="width:100%;" value="'+item[6]+'" /></td></tr>';

				formHeight+=40;
				var d=YGGetUserGroups();
				
				if(!simple_mode)
				{
					FormContent+='<tr><td>Watch Group</td><td>:</td><td>'+YGMakeWatchGroupBox(d,item[7])+'</td></tr>';
				}

				FormContent+='</tbody></table>';
				FormContent+=YGAddSaveCloseButtons(item[0],editIndex,false,'youtubeshow');


				YGbuildForm(500,formHeight,"Youtube Show Details",FormContent);

				return true;
			}

			function YGBuildListVideoDialog(link,link_type,editIndex)
			{
				var linkSplit=CSVtoArray(link);

				var item=YGAddVelues(linkSplit,8);

				var FormContent='<table style="width:90%;margin-left:20px;margin-top:20px;"><tbody>';
				var formHeight=300;
				var link_type_title=YGGetTypeTitle(link_type);

				FormContent+='<tr><td style="width:150px;">Link</td><td>:</td><td><div style="vertical-align:middle !important;word-break:break-all;width:330px;height:35px;overflow:hidden;border:1px red;">'+item[0]+'</div></td></tr>';
				FormContent+='<tr><td>Type</td><td>:</td><td><b>'+link_type_title+'</b></td></tr>';
				FormContent+='<tr><td>Custom Title</td><td>:</td><td><input type="text" id="ygcustomtitle" class="inputbox" style="width:100%;" value="'+item[1]+'" /></td></tr>';
				
				if(!simple_mode)
				{
					FormContent+='<tr><td>Custom Description</td><td>:</td><td><input type="text" id="ygcustomdescription" class="inputbox" style="width:100%;" value="'+item[2]+'" /></td></tr>';
					FormContent+='<tr><td>Custom Thumbnail</td><td>:</td><td><input type="text" id="ygcustomimage" class="inputbox" style="width:100%;" value="'+item[3]+'" /></td></tr>';
				}
				if(YGcontains(link_type,channels_youtube))
				{
					formHeight=530;
					var sp=item[4].split(",");

					FormContent+='<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';
					FormContent+='<tr><td colspan="3"><b>Special Parameters</b> <a href="https://joomlaboat.com/youtube-gallery/youtube-gallery-special-parameters" target="_blank">More about Special Parameters</a></td></tr>';
					FormContent+='<tr><td>Count</td><td>:</td><td><input type="text" id="maxresults" class="inputbox" style="width:100%;" value="'+YGGetValue(sp,'maxResults')+'" /></td></tr>';

					if(link_type=='youtubeuseruploads')
					{
						formHeight=530;
						var Values=new Array('','true');
						var Titles=new Array('No','Yes');
						FormContent+='<tr><td>More details</td><td>:</td><td>'+YGBuildSelectBox('moredetails',Values,Titles,YGGetValue(sp,'moredetails'))+'</td></tr>';

					}

					FormContent+='<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';


					FormContent+='<tr><td>Start Second</td><td>:</td><td><input type="text" id="startsecond" class="inputbox" style="width:100%;" value="'+item[5]+'" /></td></tr>';
					
					if(!simple_mode)
						FormContent+='<tr><td>End Second</td><td>:</td><td><input type="text" id="endsecond" class="inputbox" style="width:100%;" value="'+item[6]+'" /></td></tr>';
				}

				if(YGcontains(link_type,channels_vimeo))
				{
					formHeight=410;
					var sp=item[4].split(",");

					FormContent+='<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';
					FormContent+='<tr><td colspan="3"><b>Special Parameters</b> <a href="https://joomlaboat.com/youtube-gallery/youtube-gallery-special-parameters" target="_blank">More about Special Parameters</a></td></tr>';

					FormContent+='<tr><td>per_page</td><td>:</td><td><input type="text" id="per_page" class="inputbox" style="width:100%;" value="'+YGGetValue(sp,'per_page')+'" /></td></tr>';
					FormContent+='<tr><td>page</td><td>:</td><td><input type="text" id="page" class="inputbox" style="width:100%;" value="'+YGGetValue(sp,'page')+'" /></td></tr>';

					FormContent+='<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';
				}


				formHeight+=40;
				var d=YGGetUserGroups();
				
				if(!simple_mode)
				{
					FormContent+='<tr><td>Watch Group</td><td>:</td><td>'+YGMakeWatchGroupBox(d,item[7])+'</td></tr>';
				}
				FormContent+='</tbody></table>';
				FormContent+=YGAddSaveCloseButtons(item[0],editIndex,false,link_type);


				YGbuildForm(500,formHeight,"Video Link Details",FormContent);
			}

			function YGGetValue(a,p)
			{
				for (var i = 0; i < a.length; i++)
				{
					var pair=a[i].split('=');
					if(pair[0]==p)
					{
						if(pair.length>1)
							return pair[1];
						else
							return '';
					}
				}
				return '';
			}


			// Return array of string values, or NULL if CSV string not well formed.
function CSVtoArray(text) {
    var re_valid = /^\s*(?:'[^'\\]*(?:\\[\S\s][^'\\]*)*'|"[^"\\]*(?:\\[\S\s][^"\\]*)*"|[^,'"\s\\]*(?:\s+[^,'"\s\\]+)*)\s*(?:,\s*(?:'[^'\\]*(?:\\[\S\s][^'\\]*)*'|"[^"\\]*(?:\\[\S\s][^"\\]*)*"|[^,'"\s\\]*(?:\s+[^,'"\s\\]+)*)\s*)*$/;
    var re_value = /(?!\s*$)\s*(?:'([^'\\]*(?:\\[\S\s][^'\\]*)*)'|"([^"\\]*(?:\\[\S\s][^"\\]*)*)"|([^,'"\s\\]*(?:\s+[^,'"\s\\]+)*))\s*(?:,|$)/g;
    // Return NULL if input string is not well formed CSV string.
    if (!re_valid.test(text)) return null;
    var a = [];                     // Initialize array to receive values.
    text.replace(re_value, // "Walk" the string using replace with callback.
        function(m0, m1, m2, m3) {
            // Remove backslash from \' in single quoted values.
            if      (m1 !== undefined && m1!='') a.push(m1.replace(/\\'/g, "'"));
            // Remove backslash from \" in double quoted values.
            else if (m2 !== undefined && m2!='') a.push(m2.replace(/\\"/g, '"'));
            else if (m3 !== undefined) a.push(m3);
            return ''; // Return empty string.
        });
    // Handle special case of empty last value.
    if (/,\s*$/.test(text)) a.push('');

    return a;
};

			function YGcontains(obj,a)
			{
				for (var i = 0; i < a.length; i++)
				{
				    if (a[i] === obj)
				        return true;
				}
				return false;
			}

			function YGisSingleVideo(vsn)
			{
				//var channels_youtube=new Array('youtubeuseruploads','youtubestandard','youtubeplaylist','youtubeuserfavorites','youtubesearch');
				//var channels_other=new Array('vimeouservideos','vimeochannel','vimeoalbum','dailymotionplaylist');
				if(YGcontains(vsn,channels_youtube) || YGcontains(vsn,channels_other))
					return false;
				else
					return true;

			}

			function YGdeleteLink(index)
			{
				var result = confirm("Do you want to delete?");
				if (result==true)
				{
					var obj_source=document.getElementById(videolist_textarea);

					var lines = obj_source.value.split(/\r\n|\r|\n/g);
					var newList='';

					for(var i=0;i<lines.length;i++)
					{
						if(i!=index)
						{
							if(newList!='')
								newList+="\r\n";

							newList+=lines[i];
						}
					}

					obj_source.value=newList;
					YGUpdatelinksTable();

					if(simple_mode)
						submitSimpleForm(true);
				}
			}

			function YGeditLink(index)
			{
				var obj_source=document.getElementById(videolist_textarea);
				var lines = obj_source.value.split(/\r\n|\r|\n/g);

				var link=lines[index];//.replace(/["']/g, "");

				var item=CSVtoArray(link);

				var link_type=YGgetVideoSourceName(item[0]);

				if(link_type=='')
					alert("This type of links are not supported.");
				else
				{
					if(YGisSingleVideo(link_type))
						YGBuildSingleVideoDialog(link,link_type,index);
					else if(link_type=='youtubeshow*')
						YGBuildShowSeasonsDialog(link,'','','',index);
					else
						YGBuildListVideoDialog(link,link_type,index);
				}
			}

			function YGSetVLTA(vlta)
			{
				videolist_textarea=vlta;
			}

			function YGMakeWatchGroupBox(d,value)
			{
				var result='<select id="ygwatchgroup">';
				for (i = 0; i < d.length; i++)
				{
					var s=d[i].split(':');
					result+='<option value="'+s[0]+'"';
					if(value==s[0])
						result+=' selected="selected"';

					result+='>'+s[1]+'</option>';
				}
				result+='</select>';
				return result;
			}

			function YGGetUserGroups()
			{
				var ddlArray= [];
				var ddl=document.getElementById('jformwatchusergroup');
				if (!ddl) {
					ddl=document.getElementById('jform_watchusergroup');
				}
				if (ddl)
				{

					for (i = 0; i < ddl.options.length; i++)
					{
						ddlArray[i] = ddl.options[i].value+':'+ddl.options[i].text;

					}
				}
				return ddlArray;
			}

			function YGUpdatelinksTable()
			{
				var result='<table class="LinksTable" style=""><tbody><tr>';
				result+='<th>Link</th><th class="fullViewType">Type</th><th class="fullView">Custom Title</th><th class="fullView">Custom Description</th><th class="fullView">Custom Thumbnail</th><th class="fullView">Special Parameters</th>';
				result+='</tr>';

				var obj_source=document.getElementById(videolist_textarea);

				var lines = obj_source.value.split(/\r\n|\r|\n/g);

				var line_count=0;
				for(i=0;i<lines.length;i++)
				{
					if(lines[i]!='')
					{
						line_count++;
						result+='<tr>';

						item=CSVtoArray(lines[i]);
						var link_type=YGgetVideoSourceName(item[0]);
						result+='<td style="max-width:400px;word-break:break-all;"><b>'+item[0]+'</b>';
						if(link_type=='youtubeshow*')
						{
							var sp=item[4].split(",");
							var season=YGGetValue(sp,'season');
							var s=season.split(':');
							if(s.length==4)
								result+='<br>Season '+s[3];
						}


						result+='</td>';


						var link_type_title=YGGetTypeTitle(link_type);

						result+='<td class="fullViewType">'+link_type_title+'</td>';

						if(item.length>1)result+='<td class="fullView">'+item[1]+'</td>'; else result+='<td></td>';
						if(item.length>2)result+='<td class="fullView">'+item[2]+'</td>'; else result+='<td></td>';
						if(item.length>3)result+='<td class="fullView">'+item[3]+'</td>'; else result+='<td></td>';
						if(item.length>4)
						{
							var v=item[4];

							if(item.length>5 && item[5]!='')
							{
								if (v!='')v+='<br/>';
								v+='start second: '+item[5];
							}

							if(item.length>6 && item[6]!='')
							{
								if (v!='')v+='<br/>';
								v+='end second: '+item[6];
							}

							if(item.length>7 && item[7]!='')
							{
								if (v!='')v+='<br/>';
								v+='user group: '+item[7];
							}

							result+='<td class="fullView">'+v+'</td>';
						}
						else
							result+='<td class="fullView"></td>';

						result+='<td class="ygToolButton"><div class="btn-wrapper"  id="toolbar-edit"><button onclick="YGeditLink('+i+')" type="button" class="btn btn-small"><span class="icon-edit"></span>Edit</button>';
						result+='<div class="btn-wrapper" id="toolbar-delete"><button onclick="YGdeleteLink('+i+');" type="button" class="btn btn-small"><span class="icon-delete"></span>Delete</button></div></td>';

						result+='</tr>';
					}
				}

				result+='</tbody></table>';

				if(line_count==0)
				{
					var o1=document.getElementById("ygvideolinkstable");
					if(o1)o1.innerHTML="";
					
					var o2=document.getElementById("ygvideolinkstablemessage");
					if(o2)o2.style.display="none";
				}
				else
				{
					document.getElementById("ygvideolinkstable").innerHTML=result;
					document.getElementById("ygvideolinkstablemessage").style.display="block";
					
				}
				
			}
			
			
			function hideModalAddVideoForm()
			{
				var hideModalAddVideoFormMessage=document.getElementById("hideModalAddVideoFormMessage");
				hideModalAddVideoFormMessage.innerHTML="Saving video list...";
				
				hideModalAddVideoFormMessage.style.display="block";
				
				document.getElementById("hideModalAddVideoForm").style.display="none";
			}
