/*
 * YouTubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://www.joomlaboat.com
 * @GNU General Public License
 */

let videolist_textarea = '';

const channels_youtube = ['youtubeuseruploads', 'youtubestandard', 'youtubeplaylist', 'youtubeuserfavorites', 'youtubesearch', 'youtubeshow*', 'youtubeshow', 'youtubechannel', 'youtubehandle'];
const channels_other = ['vimeouservideos', 'vimeochannel', 'vimeoalbum', 'dailymotionplaylist'];
const channels_vimeo = ['vimeouservideos', 'vimeochannel', 'vimeoalbum'];
const single_videos = ['youtube', 'vimeo', 'dailymotion', 'ustream', 'ustreamlive', 'soundcloud', 'tiktok'];

const channels_youtube_title = ['Youtube User Uploads', 'Youtube Standard Feed', 'Youtube Playlist', 'Youtube User Favorites', 'Youtube Search', 'Youtube Show', 'Youtube Show', 'Youtube Channel', 'Youtube Handle'];
const channels_other_title = ['Vimeo User Uploads', 'Vimeo Channel', 'Vimeo Album', 'Dailymotion Playlist'];
const single_videos_title = ['Youtube', 'Vimeo', 'Own3DtvLive', 'Own3dtvVideo', 'Google', 'Yahoo', 'Break', 'CollegeHumor', 'Dailymotion', 'Present.me', 'UStream Recorded', 'UStream Live', 'SoundCloud', '.flv file', 'Tik Tok'];

let simple_mode = false;
let ygSiteBase = '';

function submitSimpleForm(force_to_save) {
    if (force_to_save) {
        hideModalAddVideoForm();
        //const obj_source = document.getElementById(videolist_textarea);
        Joomla.submitbutton('linksform.apply');
    } else {
        const obj = document.getElementById("jform_es_listname");

        if (obj && obj.value !== '') {
            hideModalAddVideoForm();
            Joomla.submitbutton('linksform.apply');
        }
    }
}

function YGGetTypeTitle(link_type) {
    let i;
    for (i = 0; i < channels_youtube.length; i++) {
        if (channels_youtube[i] === link_type) return channels_youtube_title[i];
    }

    for (i = 0; i < channels_other.length; i++) {
        if (channels_other[i] === link_type) return channels_other_title[i];
    }

    for (i = 0; i < single_videos.length; i++) {
        if (single_videos[i] === link_type) return single_videos_title[i];
    }

    return 'Unidentified';
}

function YGgetVideoSourceName(link) {

    if (link.indexOf("://youtube.com") !== -1 || link.indexOf('://www.youtube.com') !== -1) {
        if (link.indexOf('youtube.com/@') !== -1) {
            return 'youtubehandle';
        } else if (link.indexOf('/playlist') !== -1) return 'youtubeplaylist'; else if (link.indexOf('/favorites') !== -1) return 'youtubeuserfavorites'; else if (link.indexOf('/user') !== -1) return 'youtubeuseruploads'; else if (link.indexOf('/results') !== -1) return 'youtubesearch'; else if (link.indexOf('://www.youtube.com/show/') !== -1) return 'youtubeshow*'; else if (link.indexOf('://www.youtube.com/channel/') !== -1) return 'youtubechannel'; else return 'youtube';
    }

    if (link.indexOf('://youtu.be') !== -1 || link.indexOf('://www.youtu.be') !== -1) return 'youtube';

    if (link.indexOf('youtubestandard:') !== -1) return 'youtubestandard'

    if (link.indexOf('videolist:') !== -1) return 'videolist';

    if (link.indexOf('://vimeo.com/user') !== -1 || link.indexOf('://www.vimeo.com/user') !== -1) return 'vimeouservideos'; else if (link.indexOf('://vimeo.com/channels/') !== -1 || link.indexOf('://www.vimeo.com/channels/') !== -1) return 'vimeochannel'; else if (link.indexOf('://vimeo.com/album/') !== -1 || link.indexOf('://www.vimeo.com/album/') !== -1) return 'vimeoalbum'; else if (link.indexOf('://vimeo.com') !== -1 || link.indexOf('://www.vimeo.com') !== -1) return 'vimeo'; //return 'vimeo*friendlylink';

    if (link.indexOf('://own3d.tv/l/') !== -1 || link.indexOf('://www.own3d.tv/l/') !== -1) return 'own3dtvlive';

    if (link.indexOf('://own3d.tv/v/') !== -1 || link.indexOf('://www.own3d.tv/v/') !== -1) return 'own3dtvvideo';
    if (link.indexOf('video.google.com') !== -1) return 'google';

    if (link.indexOf('video.yahoo.com') !== -1) return 'yahoo';

    if (link.indexOf('://break.com') !== -1 || link.indexOf('://www.break.com') !== -1) return 'break';

    if (link.indexOf('://collegehumor.com') !== -1 || link.indexOf('://www.collegehumor.com') !== -1) return 'collegehumor';

    //https://www.dailymotion.com/playlist/x1crql_BigCatRescue_funny-action-big-cats/1#video=x7k9rx
    if (link.indexOf('://dailymotion.com/playlist/') !== -1 || link.indexOf('://www.dailymotion.com/playlist/') !== -1) return 'dailymotionplaylist';

    if (link.indexOf('://dailymotion.com') !== -1 || link.indexOf('://www.dailymotion.com') !== -1) return 'dailymotion';

    if (link.indexOf('://present.me') !== -1 || link.indexOf('://www.present.me') !== -1) return 'presentme';

    if (link.indexOf('://tiktok.com/') !== -1 || link.indexOf('://www.tiktok.com/') !== -1) return 'tiktok';

    if (link.indexOf('://ustream.tv/recorded/') !== -1 || link.indexOf('://www.ustream.tv/recorded/') !== -1) return 'ustream';

    if (link.indexOf('://ustream.tv/channel/') !== -1 || link.indexOf('://www.ustream.tv/channel/') !== -1) return 'ustreamlive';

    //https://api.soundcloud.com/tracks/49931.json  - accepts only resolved links
    if (link.indexOf('://api.soundcloud.com/tracks/') !== -1) return 'soundcloud';

    //https://soundcloud.com/newyfreshmusic/katy-perry-dark-horse-ft-juicy
    if (link.indexOf('://soundcloud.com') !== -1 || link.indexOf('://www.soundcloud.com') !== -1) return 'soundcloud*';

    if (link.toLowerCase().indexOf('.flv') !== -1) return '.flv';

    return '';
}

function YGAddFormatedLink(isSingle, link, editIndex) {

    const obj_source = document.getElementById(videolist_textarea);
    const osv = obj_source.value;

    if (parseInt(editIndex) !== -1) {
        const lines = obj_source.value.split(/\r\n|\r|\n/g);
        let newList = '';

        for (let i = 0; i < lines.length; i++) {
            if (i === editIndex) {
                if (newList !== '') newList += "\r\n";

                newList += link;
            } else {
                if (newList !== '') newList += "\r\n";

                newList += lines[i];
            }
        }


        obj_source.value = newList;
        YGUpdatelinksTable();
        return true;
    } else {
        if (isSingle) {
            obj_source.value = obj_source.value + "\r\n" + link;
            YGUpdatelinksTable();
            return true;
        } else {

            if (osv.indexOf(link) === -1) {
                let v = obj_source.value;
                if (v !== '') v += "\r\n";

                obj_source.value = v + link;

                YGUpdatelinksTable();
                return true;
            } else alert("This link is already in the list.");
        }
    }
    return false;
}

function YGgetValueOfParameter(r, p) {

    const i = r.indexOf(p);
    if (i === -1) return false;

    const a = r.indexOf('"', i + p.length);
    if (a === -1) return false;

    return r.substring(i, a - i);
}

function YGLoadListOfSeasons(showId) {
    const xmlHttp = new XMLHttpRequest();
    const maxResults = 5;
    const Seasons = [];

    let url = 'components/com_youtubegallery/views/linksform/tmpl/requests.php?task=getyoutubeseasonsbyshowid&showid=' + showId + '&maxResults=' + maxResults;

    xmlHttp.open("GET", url, false);
    xmlHttp.send(null);
    const r = xmlHttp.responseText;

    let list = JSON && JSON.parse(r) || $.parseJSON(r);

    for (let i = 0; i < list.length; i++) Seasons[Seasons.length] = list[i];

    return Seasons;
}

function YGResolveYoutubeShowLink(link) {
    link = link.replace('https://', 'http://');

    let url = 'components/com_youtubegallery/views/linksform/tmpl/requests.php?task=getyoutubeshowowner&link=' + link;

    const xmlHttp = new XMLHttpRequest();

    xmlHttp.open("GET", url, false);
    xmlHttp.send(null);
    const r = xmlHttp.responseText;

    if (r.indexOf('{"') === -1) return false;

    const obj = JSON && JSON.parse(r) || $.parseJSON(r);

    //get list of shows
    const maxResults = 10;
    let showId = '';

    url = 'components/com_youtubegallery/views/linksform/tmpl/requests.php?task=getyoutubeshowsbyowner&owner=' + obj.username + '&maxResults=' + maxResults;

    xmlHttp.open("GET", url, false);
    xmlHttp.send(null);
    const r2 = xmlHttp.responseText;

    let list = JSON && JSON.parse(r2) || $.parseJSON(r2);

    for (let i = 0; i < list.length; i++) {
        const a = list[i];
        if (a.link[0] === link) {
            showId = a.id[0];
            break;
        }
    }

    const pair = showId.split(':');
    if (pair.length !== 4) {
        alert('Connection problem. Try again.');
        return false;
    }
    showId = pair[3];

    //Get List Of Seasons -----------------------------------------
    let Seasons = YGLoadListOfSeasons(showId);

    YGBuildShowSeasonsDialog(link, obj.username, showId, Seasons, -1);
    return true;

}

function YGResolveSoundCloudLink(link) {
    const client_id = YGGetSoundCloudClientID();
    if (client_id === '') {
        alert('SoundCloud Client ID not set. Go to "Youtube Gallery / Settings"');
        return false;
    }

    YGAddShadowLabel("Resolving link...");
    let theUrl = 'components/com_youtubegallery/views/linksform/tmpl/requests.php?task=resolvesoundcloudlink&url=' + link + '&client_id=' + client_id;

    const xmlHttp = new XMLHttpRequest();
    xmlHttp.open("GET", theUrl, false);
    xmlHttp.send(null);
    const r = xmlHttp.responseText;

    let link_type;
    if (r.indexOf('{"') !== -1) {

        const obj = JSON.parse(r);
        if (obj.kind === 'track') {
            link = 'https://api.soundcloud.com/tracks/' + obj.id + '.json';
            link_type = YGgetVideoSourceName(link);
            if (link_type !== 'soundcloud') {
                alert("Something went wrong. Try again.");
                return false;
            }
        } else {
            alert("This type of SoundCloud (" + obj.kind + ") link is not supported.");
            return false;
        }

        return link;
    } else {
        alert("This type of SoundCloud link is not supported. Reason: " + r);
        return false;
    }
}

function YGAddLink() {

    //var link='http://www.youtube.com/show/nammalthammil';//
    let link = prompt("Please enter a Link to your Video, Playlist or Channel", "");
    if (link != null) {
        let link_type = YGgetVideoSourceName(link);

        if (link_type === '') {
            alert("This type of links are not supported.");
            return false;
        } else {
            if (link_type.indexOf('*') !== -1) {
                //resolve link
                if (link_type === 'soundcloud*') {
                    link = YGResolveSoundCloudLink(link);
                    if (!link) return false;

                    link_type = 'soundcloud';
                }

                if (link_type === 'youtubeshow*') {
                    YGResolveYoutubeShowLink(link);
                    return true;
                }
            }

            if (YGisSingleVideo(link_type)) {
                const obj_source = document.getElementById(videolist_textarea);
                const osv = obj_source.value;
                const item = CSVtoArray(link);

                if (osv.indexOf(item[0]) === -1) YGBuildSingleVideoDialog(link, link_type, -1); else {
                    alert("This link is already in the list.");
                    return false;
                }
            } else YGBuildListVideoDialog(link, link_type, -1);
        }
    }
    return true;
}

function YGAddSaveCloseButtons(link, editIndex, isSingleVideo, link_type) {
    let FormContent = '';

    FormContent += '<div style="text-align:center;margin-top:20px;">';//width:180px;margin: 20px auto;position:relative;"><div class="-wrapper" style="position:absolute;left:0;top:0;" >';

    let startEnd = false;
    if (link_type === 'youtube' || YGcontains(link_type, channels_youtube)) startEnd = true;

    if (editIndex === -1) {
        if (isSingleVideo) FormContent += '<button onclick="YGFormatSingleLink(\'' + link + '\',\'' + editIndex + '\',' + startEnd + ')" class="btn btn-small btn-success" type="button">'; else FormContent += '<button onclick="YGFormatListLink(\'' + link + '\',\'' + editIndex + '\',\'' + link_type + '\')" class="btn btn-small btn-success" type="button">';

        FormContent += '<span class="icon-new icon-white"></span><span style="margin-left:10px;">Add</span></button>';
    } else {
        if (isSingleVideo) FormContent += '<button onclick="YGFormatSingleLink(\'' + link + '\',\'' + editIndex + '\',' + startEnd + ')" class="btn btn-small" type="button">'; else FormContent += '<button onclick="YGFormatListLink(\'' + link + '\',\'' + editIndex + '\',\'' + link_type + '\')" class="btn btn-small" type="button">';

        FormContent += '<span class="icon-save"></span><span style="">Save</span></button>';//margin-left:10px;
    }

    FormContent += '</div>';
    return FormContent;
}

function YGbuildForm(width, height, title, FormContent) {

    const obj = document.getElementById("layouteditor_modal_content_box");
    obj.innerHTML = FormContent;//+'<br/>';
    showModal();
}


function showModal() {
    // Get the modal
    const modal = document.getElementById('layouteditor_Modal');

    // Get the <span> element that closes the modal
    const span = document.getElementsByClassName("layouteditor_close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function () {
        modal.style.display = "none";
    };

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    const box = document.getElementById("layouteditor_modalbox");

    modal.style.display = "block";

    let e = document.documentElement;

    const doc_w = e.clientWidth;
    const doc_h = e.clientHeight;

    const w = box.offsetWidth;
    const h = box.offsetHeight;

    let x = (doc_w / 2) - w / 2;
    if (x < 10) x = 10;

    if (x + w + 10 > doc_w) x = doc_w - w - 10;

    let y = (doc_h / 2) - h / 2;

    if (y < 50) y = 50;

    if (y + h + 50 > doc_h) y = doc_h - h - 50;

    if (y < 0) y = 0;

    box.style.position = "absolute";
    box.style.left = x + 'px';
    box.style.top = y + 'px';
}


function YGgetBasicValues(isSingle, link, SpecialParameters, startendsecond, usergroup) {
    let title = document.getElementById("ygcustomtitle").value;

    let description = "";
    const ygcustomdescription = document.getElementById("ygcustomdescription");
    if (ygcustomdescription) description = ygcustomdescription.value;

    let image = "";
    const ygcustomimage = document.getElementById("ygcustomimage");
    if (ygcustomimage) image = ygcustomimage.value;

    title = title.replace(/["']/g, "");
    description = description.replace(/["']/g, "");
    image = image.replace(/["']/g, "");

    let startsecond = "";
    let endsecond = "";

    if (startendsecond) {
        startsecond = document.getElementById("startsecond").value;

        const endsecond_obj = document.getElementById("endsecond");
        if (endsecond_obj) endsecond = endsecond_obj.value;

        startsecond = startsecond.replace(/["']/g, "");
        endsecond = endsecond.replace(/["']/g, "");
        startsecond = startsecond.replace(/[^\d.]/g, "");
        endsecond = endsecond.replace(/[^\d.]/g, "");
    }

    let new_link = link;
    if (title !== '') new_link += ',"' + title + '"'; else if (description !== '' || image !== '' || SpecialParameters !== '' || startsecond !== '' || endsecond !== '' || (usergroup !== '0' && usergroup !== '1')) new_link += ',';

    if (description !== '') new_link += ',"' + description + '"'; else if (image !== '' || SpecialParameters !== '' || startsecond !== '' || endsecond !== '' || (usergroup !== '0' && usergroup !== '1')) new_link += ',';

    if (image !== '') new_link += ',"' + image + '"'; else if (SpecialParameters !== '' || startsecond !== '' || endsecond !== '' || (usergroup !== '0' && usergroup !== '1')) new_link += ',';


    if (SpecialParameters !== '') new_link += ',"' + SpecialParameters + '"'; else if (startsecond !== '' || endsecond !== '' || (usergroup !== '0' && usergroup !== '1')) new_link += ',';

    if (startendsecond) {
        if (startsecond !== '') new_link += ',' + startsecond; else if (endsecond !== '' || (usergroup !== '0' && usergroup !== '1')) new_link += ',';

        if (endsecond !== '' || (usergroup !== '0' && usergroup !== '1')) new_link += ',' + endsecond;
    } else if (usergroup !== '0' && usergroup !== '1') new_link += ',,';

    if (usergroup !== '0' && usergroup !== '1') new_link += ',' + usergroup;


    return new_link;
}

function YGFormatSingleLink(link, editIndex, startendsecond) {

    let usergroup = '';
    const ygwatchgroup = document.getElementById("ygwatchgroup");
    if (ygwatchgroup) usergroup = ygwatchgroup.value;

    const new_link = YGgetBasicValues(true, link, '', startendsecond, usergroup);
    YGAddFormatedLink(true, new_link, parseInt(editIndex));
    //var obj=document.getElementById("YGDialog");
    //obj.innerHTML='';
    //obj.style.display="none";
    //YGShadeOn(false);

    const modal = document.getElementById('layouteditor_Modal');
    //modal.innerHTML='';
    modal.style.display = "none";

    if (simple_mode) submitSimpleForm(true);
}


function YGFormatListLink(link, editIndex, link_type) {
    //const title = document.getElementById("ygcustomtitle").value;

    //let description = "";
    //const YGCustomDescription = document.getElementById("ygcustomdescription");
    //if (YGCustomDescription) description = YGCustomDescription.value;

    //let image = "";
    //const YGCustomImage = document.getElementById("ygcustomimage");
    //if (YGCustomImage) image = YGCustomImage.value;

    let SpecialParameters = '';
    let StartEndSecond = false;

    let usergroup = '';
    const ygwatchgroup = document.getElementById("ygwatchgroup");

    if (ygwatchgroup) usergroup = ygwatchgroup.value;

    let season = "";
    let content = "";

    if (YGcontains(link_type, channels_youtube)) {
        //SpecialParameters
        StartEndSecond = true;

        let maxResults = document.getElementById("maxresults").value;

        if (link_type === 'youtubeshow') {
            season = document.getElementById("season").value;
            content = document.getElementById("contenttype").value;
        }
        maxResults = maxResults.replace(/["']/g, "");
        maxResults = maxResults.replace(/[^\d.]/g, "");


        if (maxResults !== '') SpecialParameters += 'maxResults=' + maxResults;

        if (link_type === 'youtubeshow') {
            if (season !== '') {
                if (SpecialParameters !== '') SpecialParameters += ',';

                SpecialParameters += 'season=' + season;
            }

            if (content !== '') {
                if (SpecialParameters !== '') SpecialParameters += ',';

                SpecialParameters += 'content=' + content;
            }
        }

        // || link_type === 'youtubehandle'
        if (link_type === 'youtubeuseruploads') {

            let moreDetails = document.getElementById("moredetails").value;
            moreDetails = moreDetails.replace(/["']/g, "");

            if (moreDetails !== '') {
                if (SpecialParameters !== '') SpecialParameters += ',';

                SpecialParameters += 'moredetails=true';
            }
        }
    }

    if (YGcontains(link_type, channels_vimeo)) {

        //SpecialParameters
        let per_page = document.getElementById("per_page").value;
        let page = document.getElementById("page").value;

        per_page = per_page.replace(/["']/g, "");
        page = page.replace(/["']/g, "");

        per_page = per_page.replace(/[^\d.]/g, "");
        page = page.replace(/[^\d.]/g, "");

        if (per_page !== '') {
            SpecialParameters += 'per_page=' + per_page;
        }

        if (page !== '') {
            if (SpecialParameters !== '') SpecialParameters += ',';

            SpecialParameters += 'page=' + page;
        }
    }

    const new_link = YGgetBasicValues(false, link, SpecialParameters, StartEndSecond, usergroup);

    YGAddFormatedLink(false, new_link, editIndex);

    const modal = document.getElementById('layouteditor_Modal');
    modal.style.display = "none";
}


function YGAddVelues(item, count) {
    const new_item = [];
    const l = item.length;
    for (let i = 0; i < count; i++)
        if (i > l - 1) new_item[i] = ''; else new_item[i] = item[i];

    return new_item;
}

function YGBuildSingleVideoDialog(link, link_type, editIndex) {

    const linkSplit = CSVtoArray(link);
    const item = YGAddVelues(linkSplit, 8);
    let formHeight = 300;
    let FormContent = '<table style="width:90%;margin-left:20px;margin-top:20px;"><tbody>';
    const link_type_title = YGGetTypeTitle(link_type);

    FormContent += '<tr><td>Link</td><td>:</td><td style="word-break:break-all;width:380px;">' + item[0] + '</div></td></tr>';
    FormContent += '<tr><td>Type</td><td>:</td><td><b>' + link_type_title + '</b></td></tr>';
    FormContent += '<tr><td>Custom Title</td><td>:</td><td><input type="text" id="ygcustomtitle" class="inputbox" style="width:100%;" value="' + item[1] + '" /></td></tr>';
    if (!simple_mode) {
        FormContent += '<tr><td>Custom Description</td><td>:</td><td><input type="text" id="ygcustomdescription" class="inputbox" style="width:100%;" value="' + item[2] + '" /></td></tr>';
        FormContent += '<tr><td>Custom Thumbnail</td><td>:</td><td><input type="text" id="ygcustomimage" class="inputbox" style="width:100%;" value="' + item[3] + '" /></td></tr>';
    }

    if (link_type === 'youtube') {
        formHeight = 340;
        FormContent += '<tr><td>Start Second</td><td>:</td><td><input type="text" id="startsecond" class="inputbox" style="width:100%;" value="' + item[5] + '" /></td></tr>';

        if (!simple_mode) FormContent += '<tr><td>End Second</td><td>:</td><td><input type="text" id="endsecond" class="inputbox" style="width:100%;" value="' + item[6] + '" /></td></tr>';
    }

    formHeight += 40;
    const d = YGGetUserGroups();
    if (!simple_mode) {
        FormContent += '<tr><td>Watch Group</td><td>:</td><td>' + YGMakeWatchGroupBox(d, item[7]) + '</td></tr>';
    }

    FormContent += '</tbody></table>';

    FormContent += YGAddSaveCloseButtons(item[0], editIndex, true, link_type);

    if (link_type === 'soundcloud') YGbuildForm(500, formHeight, "Single Audio Details", FormContent); else YGbuildForm(500, formHeight, "Single Video Details", FormContent);

}


function YGBuildSelectBox(id, values, titles, value) {
    let FormContent = '<select id="' + id + '" class="inputbox" style="width:100%;">';

    for (let i = 0; i < values.length; i++) {
        FormContent += '<option value="' + values[i] + '"';

        if (values[i] == value) FormContent += ' SELECTED';

        FormContent += '>' + titles[i] + '</option>';
    }

    FormContent += '</select>';
    return FormContent;
}


function YGBuildShowSeasonsDialog(link, userid, showid, seasons, editIndex) {
    const linkSplit = CSVtoArray(link);
    const item = YGAddVelues(linkSplit, 8);
    let FormContent = '<table style="width:90%;margin-left:20px;margin-top:20px;"><tbody>';
    let formHeight = 560;
    const sp = item[4].split(",");

    if (userid === '' && sp !== '') {
        const p = YGGetValue(sp, 'season').split(':');
        if (p.length === 4) {
            userid = p[0];
            showid = p[1];

            //Load list of seasons
            seasons = YGLoadListOfSeasons(showid);
        } else {
            alert('Link format is corrupted.');
            return false;
        }
    }

    FormContent += '<tr><td style="width:150px;">Link</td><td>:</td><td><div style="vertical-align:middle !important;word-break:break-all;width:330px;height:30px;overflow:hidden;border:1px red;">' + item[0] + '</div></td></tr>';
    FormContent += '<tr><td>Type</td><td>:</td><td><b>Youtube Show</b></td></tr>';

    let Values = [];
    let Titles = [];

    for (let i = 0; i < seasons.length; i++) {
        Values[i] = '' + userid + ':' + showid + ':' + seasons[i].id + ':' + seasons[i].title[0];
        Titles[i] = 'Season ' + seasons[i].title[0];
    }
    FormContent += '<tr><td><b>Season</b></td><td>:</td><td>' + YGBuildSelectBox('season', Values, Titles, YGGetValue(sp, 'season')) + '</td></tr>';

    Values = ['', 'clips'];//episodes - by default
    Titles = ['Episodes', 'Clips'];
    FormContent += '<tr><td>Content</td><td>:</td><td>' + YGBuildSelectBox('contenttype', Values, Titles, YGGetValue(sp, 'content')) + '</td></tr>';

    FormContent += '<tr><td>Custom Title</td><td>:</td><td><input type="text" id="ygcustomtitle" class="inputbox" style="width:100%;" value="' + item[1] + '" /></td></tr>';

    if (!simple_mode) {
        FormContent += '<tr><td>Custom Description</td><td>:</td><td><input type="text" id="ygcustomdescription" class="inputbox" style="width:100%;" value="' + item[2] + '" /></td></tr>';
        FormContent += '<tr><td>Custom Thumbnail</td><td>:</td><td><input type="text" id="ygcustomimage" class="inputbox" style="width:100%;" value="' + item[3] + '" /></td></tr>';
    }


    FormContent += '<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';
    FormContent += '<tr><td>Count</td><td>:</td><td><input type="text" id="maxresults" class="inputbox" style="width:100%;" value="' + YGGetValue(sp, 'maxResults') + '" /></td></tr>';

    /*
        date – Resources are sorted in reverse chronological order based on the date they were created.
        rating – Resources are sorted from highest to lowest rating.
        relevance – Resources are sorted based on their relevance to the search query. This is the default value for this parameter.
        title – Resources are sorted alphabetically by title.
        videoCount – Channels are sorted in descending order of their number of uploaded videos.
        viewCount – Resources are sorted from highest to lowest number of views. For live broadcasts, videos are sorted by number of concurrent viewers while the broadcasts are ongoing.
    */

    const OrderByValues = ['', 'date', 'rating', 'relevance', 'title', 'viewCount'];
    const OrderByTitles = ['-', 'Chronological Order', 'Highest to lowest rating', 'Relevance to the search query', 'Alphabetically by title', 'Highest to lowest number of views'];
    FormContent += '<tr><td>Order By</td><td>:</td><td>' + YGBuildSelectBox('ygorderby', OrderByValues, OrderByTitles, YGGetValue(sp, 'orderby')) + '</td></tr>';


    FormContent += '<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';

    FormContent += '<tr><td>Start Second</td><td>:</td><td><input type="text" id="startsecond" class="inputbox" style="width:100%;" value="' + item[5] + '" /></td></tr>';

    if (!simple_mode) FormContent += '<tr><td>End Second</td><td>:</td><td><input type="text" id="endsecond" class="inputbox" style="width:100%;" value="' + item[6] + '" /></td></tr>';

    formHeight += 40;
    const d = YGGetUserGroups();

    if (!simple_mode)
        FormContent += '<tr><td>Watch Group</td><td>:</td><td>' + YGMakeWatchGroupBox(d, item[7]) + '</td></tr>';

    FormContent += '</tbody></table>';
    FormContent += YGAddSaveCloseButtons(item[0], editIndex, false, 'youtubeshow');
    YGbuildForm(500, formHeight, "Youtube Show Details", FormContent);
    return true;
}

function YGBuildListVideoDialog(link, link_type, editIndex) {
    let sp;
    const linkSplit = CSVtoArray(link);
    const item = YGAddVelues(linkSplit, 8);
    let FormContent = '<table style="width:90%;margin-left:20px;margin-top:20px;"><tbody>';
    let formHeight = 300;
    const link_type_title = YGGetTypeTitle(link_type);

    FormContent += '<tr><td style="width:150px;">Link</td><td>:</td><td><div style="vertical-align:middle !important;word-break:break-all;width:330px;height:35px;overflow:hidden;border:1px red;">' + item[0] + '</div></td></tr>';
    FormContent += '<tr><td>Type</td><td>:</td><td><b>' + link_type_title + '</b></td></tr>';
    FormContent += '<tr><td>Custom Title</td><td>:</td><td><input type="text" id="ygcustomtitle" class="inputbox" style="width:100%;" value="' + item[1] + '" /></td></tr>';

    if (!simple_mode) {
        FormContent += '<tr><td>Custom Description</td><td>:</td><td><input type="text" id="ygcustomdescription" class="inputbox" style="width:100%;" value="' + item[2] + '" /></td></tr>';
        FormContent += '<tr><td>Custom Thumbnail</td><td>:</td><td><input type="text" id="ygcustomimage" class="inputbox" style="width:100%;" value="' + item[3] + '" /></td></tr>';
    }
    if (YGcontains(link_type, channels_youtube)) {
        formHeight = 530;
        sp = item[4].split(",");

        FormContent += '<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';
        FormContent += '<tr><td colspan="3"><b>Special Parameters</b> <a href="https://joomlaboat.com/youtube-gallery/youtube-gallery-special-parameters" target="_blank">More about Special Parameters</a></td></tr>';
        FormContent += '<tr><td>Count</td><td>:</td><td><input type="text" id="maxresults" class="inputbox" style="width:100%;" value="' + YGGetValue(sp, 'maxResults') + '" /></td></tr>';

        if (link_type === 'youtubeuseruploads') {
            formHeight = 530;
            const Values = ['', 'true'];
            const Titles = ['No', 'Yes'];
            FormContent += '<tr><td>More details</td><td>:</td><td>' + YGBuildSelectBox('moredetails', Values, Titles, YGGetValue(sp, 'moredetails')) + '</td></tr>';
        }

        FormContent += '<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';
        FormContent += '<tr><td>Start Second</td><td>:</td><td><input type="text" id="startsecond" class="inputbox" style="width:100%;" value="' + item[5] + '" /></td></tr>';

        if (!simple_mode) FormContent += '<tr><td>End Second</td><td>:</td><td><input type="text" id="endsecond" class="inputbox" style="width:100%;" value="' + item[6] + '" /></td></tr>';
    }

    if (YGcontains(link_type, channels_vimeo)) {
        formHeight = 410;
        sp = item[4].split(",");

        FormContent += '<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';
        FormContent += '<tr><td colspan="3"><b>Special Parameters</b> <a href="https://joomlaboat.com/youtube-gallery/youtube-gallery-special-parameters" target="_blank">More about Special Parameters</a></td></tr>';

        FormContent += '<tr><td>per_page</td><td>:</td><td><input type="text" id="per_page" class="inputbox" style="width:100%;" value="' + YGGetValue(sp, 'per_page') + '" /></td></tr>';
        FormContent += '<tr><td>page</td><td>:</td><td><input type="text" id="page" class="inputbox" style="width:100%;" value="' + YGGetValue(sp, 'page') + '" /></td></tr>';

        FormContent += '<tr><td colspan="3"><hr style="border:1px grey dotted;" /></td></tr>';
    }


    formHeight += 40;
    const d = YGGetUserGroups();

    if (!simple_mode) {
        FormContent += '<tr><td>Watch Group</td><td>:</td><td>' + YGMakeWatchGroupBox(d, item[7]) + '</td></tr>';
    }
    FormContent += '</tbody></table>';
    FormContent += YGAddSaveCloseButtons(item[0], editIndex, false, link_type);


    YGbuildForm(500, formHeight, "Video Link Details", FormContent);
}

function YGGetValue(a, p) {
    for (let i = 0; i < a.length; i++) {
        const pair = a[i].split('=');
        if (pair[0] == p) {
            if (pair.length > 1) return pair[1]; else return '';
        }
    }
    return '';
}


// Return array of string values, or NULL if CSV string not well formed.
function CSVtoArray(text) {
    const re_valid = /^\s*(?:'[^'\\]*(?:\\[\S\s][^'\\]*)*'|"[^"\\]*(?:\\[\S\s][^"\\]*)*"|[^,'"\s\\]*(?:\s+[^,'"\s\\]+)*)\s*(?:,\s*(?:'[^'\\]*(?:\\[\S\s][^'\\]*)*'|"[^"\\]*(?:\\[\S\s][^"\\]*)*"|[^,'"\s\\]*(?:\s+[^,'"\s\\]+)*)\s*)*$/;
    const re_value = /(?!\s*$)\s*(?:'([^'\\]*(?:\\[\S\s][^'\\]*)*)'|"([^"\\]*(?:\\[\S\s][^"\\]*)*)"|([^,'"\s\\]*(?:\s+[^,'"\s\\]+)*))\s*(?:,|$)/g;
    // Return NULL if input string is not well formed CSV string.
    if (!re_valid.test(text)) return null;
    const a = [];                     // Initialize array to receive values.
    text.replace(re_value, // "Walk" the string using replace with callback.
        function (m0, m1, m2, m3) {
            // Remove backslash from \' in single quoted values.
            if (m1 !== undefined && m1 !== '') a.push(m1.replace(/\\'/g, "'"));
            // Remove backslash from \" in double quoted values.
            else if (m2 !== undefined && m2 !== '') a.push(m2.replace(/\\"/g, '"')); else if (m3 !== undefined) a.push(m3);
            return ''; // Return empty string.
        });
    // Handle special case of empty last value.
    if (/,\s*$/.test(text)) a.push('');

    return a;
}

function YGcontains(obj, a) {
    for (let i = 0; i < a.length; i++) {
        if (a[i] === obj) return true;
    }
    return false;
}

function YGisSingleVideo(vsn) {
    //var channels_youtube=new Array('youtubeuseruploads','youtubestandard','youtubeplaylist','youtubeuserfavorites','youtubesearch');
    //var channels_other=new Array('vimeouservideos','vimeochannel','vimeoalbum','dailymotionplaylist');
    return !(YGcontains(vsn, channels_youtube) || YGcontains(vsn, channels_other));
}

function YGdeleteLink(index) {
    const result = confirm("Do you want to delete?");
    if (result === true) {
        const obj_source = document.getElementById(videolist_textarea);

        const lines = obj_source.value.split(/\r\n|\r|\n/g);
        let newList = '';

        for (let i = 0; i < lines.length; i++) {
            if (i !== index) {
                if (newList !== '') newList += "\r\n";

                newList += lines[i];
            }
        }

        obj_source.value = newList;
        YGUpdatelinksTable();

        if (simple_mode) submitSimpleForm(true);
    }
}

function YGeditLink(index) {
    const obj_source = document.getElementById(videolist_textarea);
    const lines = obj_source.value.split(/\r\n|\r|\n/g);
    const link = lines[index];//.replace(/["']/g, "");
    const item = CSVtoArray(link);
    const link_type = YGgetVideoSourceName(item[0]);

    if (link_type === '') alert("This type of links are not supported."); else {
        if (YGisSingleVideo(link_type)) YGBuildSingleVideoDialog(link, link_type, index); else if (link_type === 'youtubeshow*') YGBuildShowSeasonsDialog(link, '', '', '', index); else YGBuildListVideoDialog(link, link_type, index);
    }
}

function YGSetVLTA(vlta) {
    videolist_textarea = vlta;
}

function YGMakeWatchGroupBox(d, value) {
    let result = '<select id="ygwatchgroup">';
    for (let i = 0; i < d.length; i++) {
        const s = d[i].split(':');
        result += '<option value="' + s[0] + '"';
        if (value === s[0]) result += ' selected="selected"';

        result += '>' + s[1] + '</option>';
    }
    result += '</select>';
    return result;
}

function YGGetUserGroups() {
    const ddlArray = [];
    let ddl = document.getElementById('jformwatchusergroup');
    if (!ddl) {
        ddl = document.getElementById('jform_watchusergroup');
    }
    if (ddl) {
        for (let i = 0; i < ddl.options.length; i++)
            ddlArray[i] = ddl.options[i].value + ':' + ddl.options[i].text;
    }
    return ddlArray;
}

function YGUpdatelinksTable() {
    let result = '<table class="LinksTable" style=""><tbody><tr>';
    result += '<th>Link</th><th class="fullViewType">Type</th><th class="fullView">Custom Title</th><th class="fullView">Custom Description</th><th class="fullView">Custom Thumbnail</th><th class="fullView">Special Parameters</th>';
    result += '</tr>';

    const obj_source = document.getElementById(videolist_textarea);
    const lines = obj_source.value.split(/\r\n|\r|\n/g);

    let line_count = 0;

    let item;
    for (let i = 0; i < lines.length; i++) {
        if (lines[i] !== '') {
            line_count++;
            result += '<tr>';

            item = CSVtoArray(lines[i]);
            const link_type = YGgetVideoSourceName(item[0]);
            result += '<td style="max-width:400px;word-break:break-all;"><b>' + item[0] + '</b>';
            if (link_type === 'youtubeshow*') {
                const sp = item[4].split(",");
                const season = YGGetValue(sp, 'season');
                const s = season.split(':');
                if (s.length === 4) result += '<br>Season ' + s[3];
            }

            result += '</td>';

            const link_type_title = YGGetTypeTitle(link_type);

            result += '<td class="fullViewType">' + link_type_title + '</td>';

            if (item.length > 1) result += '<td class="fullView">' + item[1] + '</td>'; else result += '<td></td>';
            if (item.length > 2) result += '<td class="fullView">' + item[2] + '</td>'; else result += '<td></td>';
            if (item.length > 3) result += '<td class="fullView">' + item[3] + '</td>'; else result += '<td></td>';
            if (item.length > 4) {
                let v = item[4];

                if (item.length > 5 && item[5] !== '') {
                    if (v !== '') v += '<br/>';
                    v += 'start second: ' + item[5];
                }

                if (item.length > 6 && item[6] !== '') {
                    if (v !== '') v += '<br/>';
                    v += 'end second: ' + item[6];
                }

                if (item.length > 7 && item[7] !== '') {
                    if (v !== '') v += '<br/>';
                    v += 'user group: ' + item[7];
                }

                result += '<td class="fullView">' + v + '</td>';
            } else result += '<td class="fullView"></td>';

            result += '<td class="ygToolButton"><div class="btn-wrapper"  id="toolbar-edit"><button onclick="YGeditLink(' + i + ')" type="button" class="btn btn-small"><span class="icon-edit"></span>Edit</button>';
            result += '<div class="btn-wrapper" id="toolbar-delete"><button onclick="YGdeleteLink(' + i + ');" type="button" class="btn btn-small"><span class="icon-delete"></span>Delete</button></div></td>';

            result += '</tr>';
        }
    }

    result += '</tbody></table>';

    if (line_count === 0) {
        const o1 = document.getElementById("ygvideolinkstable");
        if (o1) o1.innerHTML = "";

        const o2 = document.getElementById("ygvideolinkstablemessage");
        if (o2) o2.style.display = "none";
    } else {
        document.getElementById("ygvideolinkstable").innerHTML = result;
        document.getElementById("ygvideolinkstablemessage").style.display = "block";
    }
}

function hideModalAddVideoForm() {
    const hideModalAddVideoFormMessage = document.getElementById("hideModalAddVideoFormMessage");
    hideModalAddVideoFormMessage.innerHTML = "Saving video list...";
    hideModalAddVideoFormMessage.style.display = "block";
    document.getElementById("hideModalAddVideoForm").style.display = "none";
}
