String.prototype.replaceAll = function (find, replace) {
    const str = this;
    //return str.replace(new RegExp(find, 'g'), replace);
    return str.replace(new RegExp(find.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g'), replace);
};

const YoutubeGalleryPlayerObject = class {
    constructor(width_, height_, playerapiid_, initial_volume_, mute_on_play_, auto_play_, allowplaylist_) {
        this.WebsiteRoot = "";

        this.iframeAPIloaded = false;
        this.iframeAPIloadedCheckCount = 0;
        this.videorecords = [];
        this.videolistid = null;
        this.themeid = null;
        this.playerapiid = playerapiid_;

        this.PlayList = [];
        this.CurrentVideoID = "";
        this.IframeApiReady = false;
        this.youtubeplayer_options = null;//options_;
        this.ApiStart = null;//this.options.start;
        this.ApiEnd = null;//this.options.end;
        this.width = width_;
        this.height = height_;
        this.api_player = null;
        this.APIPlayerBodyPartLoaded = false;
        this.initial_volume = initial_volume_;//-1 is default
        this.mute_on_play = mute_on_play_;
        this.auto_play = auto_play_;
        this.allowplaylist = allowplaylist_;
        this.VideoSources = [];
        this.Player = [];
        this.openinnewwindow = 0;
    }

    youtube_SetPlayer_(videoid) {
        this.youtubeplayer_options.start = this.ApiStart;
        this.youtubeplayer_options.end = this.ApiEnd;
        this.youtubeplayer_options.mute = 1;//for autoplay: https://stackoverflow.com/questions/54944500/problems-with-youtube-iframe-api-to-start-playing-video-on-chrome

        this.CurrentVideoID = videoid;
        const playerid = this.playerapiid + "api";
        document.getElementById(playerid).innerHTML = '';

        const initial_volume = this.initial_volume;
        const mute_on_play = this.mute_on_play;
        const auto_play = this.auto_play;
        const allowplaylist = this.allowplaylist;
        const PlayList = this.PlayList;
        const classname = "youtubeplayer" + this.videolistid;
        const func = classname + '.FindNextVideo();';

        this.api_player = new YT.Player(playerid, {
            width: this.width,
            id: playerid,
            height: this.height,
            host: 'https://www.youtube.com',
            mute: 1,
            allow: "autoplay",
            autoplay: 0,
            playerVars: this.youtubeplayer_options,
            videoId: videoid,
            origin: 'https://joomlaboat.com/',

            events: {
                "onReady": function (event) {
                    if (initial_volume !== -1)
                        event.target.setVolume(initial_volume);

                    if (mute_on_play)
                        event.target.mute();

                    if (auto_play)
                        event.target.playVideo();

                    if (!mute_on_play) {
                        setTimeout(function () {
                            event.target.unMute();

                            if (auto_play)
                                event.target.playVideo(); //try to play video after unmuting

                        }, 500);
                    }
                },
                "onStateChange": function (event) {
                    if (PlayList.length !== 0 && allowplaylist) {
                        if (event.data === YT.PlayerState.ENDED) {
                            setTimeout(eval(func), 500);
                        }
                    }
                }
            }
        });
        //
    }

    vimeo_SetPlayer_(videoid) {
        this.vimeoplayer_options.start = this.ApiStart;
        this.vimeoplayer_options.end = this.ApiEnd;

        this.CurrentVideoID = videoid;
        const playerid = this.playerapiid + "api";
        document.getElementById(playerid).innerHTML = '';

        const classname = "youtubeplayer" + this.videolistid;
        const func = classname + '.FindNextVideo();';
        const player_options = {
            id: videoid,
            width: this.width,
            height: this.height,
            autoplay: !!+this.auto_play,
            background: !!+this.vimeoplayer_options.background,
            loop: !!+this.vimeoplayer_options.loop,
            muted: !!+this.mute_on_play
        };

        this.api_player = new Vimeo.Player(playerid, player_options);

        if (this.initial_volume !== -1)
            this.api_player.setVolume(this.initial_volume / 100);//Vimeo player volume is from 0 to 1

        this.api_player.on('play', function () {
            //alert('Played the first video');
        });

        this.api_player.on('ended', function () {

            alert("eval147");
            setTimeout(eval(func), 500);
        });
    }


    FindNextVideo() {
        if (this.PlayList.length === 1 && this.PlayList[0] === '') {
            //Video not found
            return;
        }

        if (!this.iframeAPIloaded) {
            //iframe API not loaded
            if (this.iframeAPIloadedCheckCount < 5)
                setTimeout(this.FindNextVideo(), 500);

            this.iframeAPIloadedCheckCount++;
            return false;
        }

        if (this.PlayList.length === 0) {
            //alert("FindNextVideo:Video records not loaded yet");
            setTimeout(this.FindNextVideo(), 500);
            return false;
        }

        let d = 0;
        const v = this.CurrentVideoID;
        const l = this.PlayList.length;

        let g = null;
        for (let i = 0; i < l; i++) {
            g = this.PlayList[i].split("*");
            if (g[0] === v)//0 - id
            {
                //if current video is the last in the list play the first video
                d = i + 1;
                if (d === l)
                    d = 0;

                break;
            }
        }
        g = this.PlayList[d].split("*");

        const videoid = g[0];
        const objectid = g[1];
        const videosource = g[2];

        this.HotVideoSwitch(this.videolistid, videoid, videosource, objectid);
    }

    FindCurrentVideo() {
        if (!this.iframeAPIloaded) {
            if (this.iframeAPIloadedCheckCount < 5)
                setTimeout(this.FindCurrentVideo(), 500);

            this.iframeAPIloadedCheckCount++;
            return false;
        }

        if (this.PlayList.length === 0) {
            setTimeout(this.FindCurrentVideo(), 500);
            return false;
        }

        const l = this.PlayList.length;
        for (let i = 0; i < l; i++) {
            const g = this.PlayList[i].split("*");
            if (g[0] === this.CurrentVideoID) {
                const videoid = g[0];
                const objectid = g[1];
                const videosource = g[2];
                this.HotVideoSwitch(this.videolistid, videoid, videosource, objectid);
                break;
            }
        }
    }

    loadVideoRecords(ygstart) {
        const xmlHttp = new XMLHttpRequest();

        let url = this.WebsiteRoot + '/index.php?option=com_youtubegallery&view=youtubegallery&yg_api=1&listid=' + this.videolistid + '&themeid=' + this.themeid + '&ygstart=' + ygstart;

        xmlHttp.open("GET", url, false);
        xmlHttp.send(null);
        const r = xmlHttp.responseText;

        this.videorecords = JSON && JSON.parse(r) || $.parseJSON(r);
    }

    findVideoRecordByID(videoid) {
        for (let i = 0; i < this.videorecords.length; i++) {
            const rec = this.videorecords[i];

            if (rec.es_videoid === videoid)
                return rec;
        }
        return null;
    }

    element_addClass(objname, classname) {
        const obj = document.getElementById(objname);
        if (obj) {
            const classes = obj.className.split(" ");
            let found = false;
            for (let i = 0; i < classes.length; i++) {
                if (classes[i] === classname)
                    found = true;
            }

            if (!found) {
                classes.push(classname);
                obj.className = classes.join(" ");
            }
        }
    }

    element_removeClass(objname, classname) {
        const obj = document.getElementById(objname);
        if (obj) {
            const classes = obj.className.split(" ");
            const new_classes = [];

            for (let i = 0; i < classes.length; i++) {
                if (classes[i] !== classname)
                    new_classes.push(classes[i]);
            }

            obj.className = new_classes.join(" ");
        }
    }

    thumb_addClass(id, classname) {
        const objname = "youtubegallery_thumbnail_box_" + this.videolistid + "_" + id;
        this.element_addClass(objname, classname)
    }

    thumb_removeClass(id, classname) {
        const objname = "youtubegallery_thumbnail_box_" + this.videolistid + "_" + id;
        this.element_removeClass(objname, classname)
    }

    highlightCurrentThumb(id) {
        for (let i = 0; i < this.videorecords.length; i++) {
            const rec = this.videorecords[i];
            this.thumb_removeClass(rec.id, "ygThumb-active");
            this.thumb_addClass(rec.id, "ygThumb-inactive");
        }

        this.thumb_removeClass(id, "ygThumb-inactive");
        this.thumb_addClass(id, "ygThumb-active");
    }

    HotVideoSwitch(videolistid, videoid, videosource, id) {

        let i = this.VideoSources.indexOf(videosource);
        let playercode = "";
        if (i !== -1)
            playercode = this.Player[i];

        playercode = playercode.replace("****youtubegallery-video-id****", videoid);
        const rec = this.findVideoRecordByID(videoid);

        if (rec == null)
            return;

        if (rec.es_customimageurl !== null && rec.es_customimageurl !== "" && rec.es_customimageurl.indexOf("#") === -1) {
            const customimage = rec.es_customimageurl;
            const n = customimage.indexOf("_small");
            if (n === -1) {
                playercode = playercode.replace("****youtubegallery-video-customimage****", customimage);
                for (i = 0; i < 2; i++) {
                    playercode = playercode.replace("***code_begin***", "");
                    playercode = playercode.replace("***code_end***", "");
                }
            } else
                playercode = YoutubeGalleryCleanCode(playercode);
        } else
            playercode = YoutubeGalleryCleanCode(playercode);

        playercode = playercode.replace("****youtubegallery-video-link****", rec.es_link);
        playercode = playercode.replace("****youtubegallery-video-startsecond****", rec.es_startsecond);
        playercode = playercode.replace("****youtubegallery-video-endsecond****", rec.es_endsecond);
        playercode = playercode.replace("autoplay=0", "autoplay=1");
        playercode = playercode.replace("****scriptbegin****", "<script ");
        playercode = playercode.replace("****scriptend****", "</script>");

        const ygsc = document.getElementById("YoutubeGallerySecondaryContainer" + this.videolistid + "");
        ygsc.innerHTML = playercode;
        ygsc.style.display = "block";

        if (playercode.indexOf('data-marker="DYNAMIC PLAYER"') !== -1) {
            this.ApiStart = rec.es_startsecond;
            this.ApiEnd = rec.es_endsecond;

            if (videosource === "youtube") {
                this.youtube_SetPlayer_(videoid);
            } else if (videosource === "vimeo") {
                this.vimeo_SetPlayer_(videoid);

            } else {
                alert("eval394");
                eval("this.youtubegallery_updateplayer_" + videosource + "(videoid,true)");
            }
        }

        const title_obj_name = "YoutubeGalleryVideoTitle" + this.videolistid + "";
        const tObj = document.getElementById(title_obj_name);
        const description_obj_name = "YoutubeGalleryVideoDescription" + this.videolistid + "";
        const dObj = document.getElementById(description_obj_name);
        const t = this;

        if (tObj) {
            this.element_removeClass(title_obj_name, "ygTitle-visible");
            this.element_addClass(title_obj_name, "ygTitle-hidden");

            const title = rec.es_title;
            tObj.innerHTML = title.replaceAll('_quote_', '&quot;');

            setTimeout(function () {
                t.element_removeClass(title_obj_name, "ygTitle-hidden");
                t.element_addClass(title_obj_name, "ygTitle-visible");
            }, 200);
        }

        if (dObj) {
            this.element_removeClass(description_obj_name, "ygDescription-visible");
            this.element_addClass(description_obj_name, "ygDescription-hidden");
            let desc = rec.es_description;

            desc = desc.replaceAll('_thelinebreak_', '<br />');
            desc = desc.replaceAll('_quote_', '&quot;');
            desc = desc.replaceAll('_email_', '@');

            dObj.innerHTML = desc;

            setTimeout(function () {
                t.element_removeClass(description_obj_name, "ygDescription-hidden");
                t.element_addClass(description_obj_name, "ygDescription-visible");
            }, 200);
        }

        if (this.openinnewwindow === 5) {
            //Jump to the player anchor:"youtubegallery"
            window.location.hash = "youtubegallery";
        }

        this.highlightCurrentThumb(id);
    }
};

function YoutubeGalleryCleanCode(playercode) {
    do {
        const b = playercode.indexOf("***code_begin***");
        const e = playercode.indexOf("***code_end***");
        if (b !== -1 && e !== -1)
            playercode = playercode.substr(0, b) + playercode.substr(e + 14);

        if (b === -1 || e === -1)
            break;

    } while (1);
    return playercode;
}
