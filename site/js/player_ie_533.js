"use strict";

function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
        throw new TypeError("Cannot call a class as a function");
    }
}

function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
        var descriptor = props[i];
        descriptor.enumerable = descriptor.enumerable || false;
        descriptor.configurable = true;
        if ("value" in descriptor) descriptor.writable = true;
        Object.defineProperty(target, descriptor.key, descriptor);
    }
}

function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
}

String.prototype.replaceAll = function (find, replace) {
    var str = this; //return str.replace(new RegExp(find, 'g'), replace);

    return str.replace(new RegExp(find.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g'), replace);
};

var YoutubeGalleryPlayerObject = /*#__PURE__*/function () {
    function YoutubeGalleryPlayerObject(width_, height_, playerapiid_, initial_volume_, mute_on_play_, auto_play_, allowplaylist_) {
        _classCallCheck(this, YoutubeGalleryPlayerObject);

        this.WebsiteRoot = "";

        this.iframeAPIloaded = false;
        this.iframeAPIloadedCheckCount = 0;
        this.videorecords = [];
        this.videolistid = null;
        this.playerapiid = playerapiid_;
        this.videoStopped = false;
        this.PlayList = [];
        this.CurrentVideoID = "";
        this.IframeApiReady = false;
        this.youtubeplayer_options = null; //options_;

        this.ApiStart = null; //this.options.start;

        this.ApiEnd = null; //this.options.end;

        this.width = width_;
        this.height = height_;
        this.api_player = null;
        this.APIPlayerBodyPartLoaded = false;
        this.initial_volume = initial_volume_; //-1 is default

        this.mute_on_play = mute_on_play_;
        this.auto_play = auto_play_;
        this.allowplaylist = allowplaylist_;
        this.VideoSources = [];
        this.Player = [];
        this.openinnewwindow = 0;
    }

    _createClass(YoutubeGalleryPlayerObject, [{
        key: "youtube_SetPlayer_",
        value: function youtube_SetPlayer_(videoid) {
            //---------------------------------
            this.youtubeplayer_options.start = this.ApiStart;
            this.youtubeplayer_options.end = this.ApiEnd;
            this.youtubeplayer_options.mute = 1; //for autoplay: https://stackoverflow.com/questions/54944500/problems-with-youtube-iframe-api-to-start-playing-video-on-chrome

            this.videoStopped = false;
            this.CurrentVideoID = videoid;
            var playerid = this.playerapiid + "api";
            document.getElementById(playerid).innerHTML = '';
            var initial_volume = this.initial_volume;
            var mute_on_play = this.mute_on_play;
            var auto_play = this.auto_play;
            var allowplaylist = this.allowplaylist;
            var PlayList = this.PlayList;
            var classname = "youtubeplayer" + this.videolistid;
            var func = classname + '.FindNextVideo();';
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
                origin: 'http://demo.oxforddavid.com/?aa=1s',
                events: {
                    "onReady": function onReady(event) {
                        if (initial_volume != -1) event.target.setVolume(initial_volume);
                        if (mute_on_play) event.target.mute();
                        if (auto_play) event.target.playVideo();

                        if (!mute_on_play) {
                            setTimeout(function () {
                                event.target.unMute();
                                if (auto_play) event.target.playVideo(); //try to play video after unmuting
                            }, 500);
                        }
                    },
                    "onStateChange": function onStateChange(event) {
                        if (PlayList.length != 0 && allowplaylist) {
                            if (event.data == YT.PlayerState.ENDED) {
                                setTimeout(eval(func), 500);
                            }
                        }
                    }
                }
            }); //
        }
    }, {
        key: "vimeo_SetPlayer_",
        value: function vimeo_SetPlayer_(videoid) {
            this.vimeoplayer_options.start = this.ApiStart;
            this.vimeoplayer_options.end = this.ApiEnd;
            this.videoStopped = false;
            this.CurrentVideoID = videoid;
            var playerid = this.playerapiid + "api";
            document.getElementById(playerid).innerHTML = '';
            var classname = "youtubeplayer" + this.videolistid;
            var func = classname + '.FindNextVideo();';
            var player_options = {
                id: videoid,
                width: this.width,
                height: this.height,
                autoplay: !!+this.auto_play,
                background: !!+this.vimeoplayer_options.background,
                loop: !!+this.vimeoplayer_options.loop,
                muted: !!+this.mute_on_play
            };
            this.api_player = new Vimeo.Player(playerid, player_options);
            if (this.initial_volume != -1) this.api_player.setVolume(this.initial_volume / 100); //Vimeo player volume is from 0 to 1

            this.api_player.on('play', function () {//alert('Played the first video');
            });
            this.api_player.on('ended', function () {
                setTimeout(eval(func), 500);
            });
        }
    }, {
        key: "FindNextVideo",
        value: function FindNextVideo() {
            if (this.PlayList.length == 1 && this.PlayList[0] == '') {
                //alert("Video not found");
                return;
            }

            if (!this.iframeAPIloaded) {
                //alert("iframeAPIloaded not loaded");
                if (this.iframeAPIloadedCheckCount < 5) setTimeout(this.FindNextVideo(), 500);
                this.iframeAPIloadedCheckCount++;
                return false;
            }

            if (this.PlayList.length == 0) {
                setTimeout(this.FindNextVideo(), 500);
                return false;
            }

            //this.updateVideoRecords();
            var d = 0;
            var v = this.CurrentVideoID;
            var l = this.PlayList.length;
            var g = null;

            for (var i = 0; i < l; i++) {
                g = this.PlayList[i].split("*");

                if (g[0] == v) //0 - id
                {
                    //if current video is the last in the list play the first video
                    d = i + 1;
                    if (d == l) d = 0;
                    break;
                }
            }

            g = this.PlayList[d].split("*");
            var videoid = g[0];
            var objectid = g[1];
            var videosource = g[2];
            this.HotVideoSwitch(this.videolistid, videoid, videosource, objectid);
        }
    }, {
        key: "FindCurrentVideo",
        value: function FindCurrentVideo() {
            if (!this.iframeAPIloaded) {
                if (this.iframeAPIloadedCheckCount < 5) setTimeout(this.FindCurrentVideo(), 500);
                this.iframeAPIloadedCheckCount++;
                return false;
            }

            if (this.PlayList.length == 0) {
                setTimeout(this.FindCurrentVideo(), 500);
                return false;
            }

            //this.updateVideoRecords();
            var l = this.PlayList.length;

            for (var i = 0; i < l; i++) {
                var g = this.PlayList[i].split("*");

                if (g[0] == this.CurrentVideoID) {
                    var videoid = g[0];
                    var objectid = g[1];
                    var videosource = g[2];
                    this.HotVideoSwitch(this.videolistid, videoid, videosource, objectid);
                    break;
                }
            }
        }
    }, {
        key: "loadVideoRecords",
        value: function loadVideoRecords() {
            var xmlHttp = new XMLHttpRequest();

            let url = this.WebsiteRoot + '/index.php?option=com_youtubegallery&view=youtubegallery&yg_api=1&listid=' + this.videolistid + '&themeid=' + this.themeid + '&ygstart=' + ygstart;

            xmlHttp.open("GET", url, false);
            xmlHttp.send(null);
            var r = xmlHttp.responseText;

            this.videorecords = JSON && JSON.parse(r) || $.parseJSON(r);
        }
    }, {
        key: "findVideoRecordByID",
        value: function findVideoRecordByID(videoid) {
            for (var i = 0; i < this.videorecords.length; i++) {
                var rec = this.videorecords[i];
                if (rec.es_videoid == videoid) return rec;
            }

            return null;
        }
    }, {
        key: "element_addClass",
        value: function element_addClass(objname, classname) {
            var obj = document.getElementById(objname);

            if (obj) {
                var classes = obj.className.split(" ");
                var found = false;

                for (var i = 0; i < classes.length; i++) {
                    if (classes[i] == classname) found = true;
                }

                if (!found) {
                    classes.push(classname);
                    obj.className = classes.join(" ");
                }
            }
        }
    }, {
        key: "element_removeClass",
        value: function element_removeClass(objname, classname) {
            var obj = document.getElementById(objname);

            if (obj) {
                var classes = obj.className.split(" ");
                var new_classes = [];

                for (var i = 0; i < classes.length; i++) {
                    if (classes[i] != classname) new_classes.push(classes[i]);
                }

                obj.className = new_classes.join(" ");
            }
        }
    }, {
        key: "thumb_addClass",
        value: function thumb_addClass(id, classname) {
            var objname = "youtubegallery_thumbnail_box_" + this.videolistid + "_" + id;
            this.element_addClass(objname, classname);
        }
    }, {
        key: "thumb_removeClass",
        value: function thumb_removeClass(id, classname) {
            var objname = "youtubegallery_thumbnail_box_" + this.videolistid + "_" + id;
            this.element_removeClass(objname, classname);
        }
    }, {
        key: "highlightCurrentThumb",
        value: function highlightCurrentThumb(id) {
            for (var i = 0; i < this.videorecords.length; i++) {
                var rec = this.videorecords[i];
                this.thumb_removeClass(rec.id, "ygThumb-active");
                this.thumb_addClass(rec.id, "ygThumb-inactive");
            }

            this.thumb_removeClass(id, "ygThumb-inactive");
            this.thumb_addClass(id, "ygThumb-active");
        }
    }, {
        key: "HotVideoSwitch",
        value: function HotVideoSwitch(videolistid, videoid, videosource, id) {
            this.updateVideoRecords();
            var i = this.VideoSources.indexOf(videosource);
            var playercode = "";
            if (i != -1) playercode = this.Player[i];
            playercode = playercode.replace("****youtubegallery-video-id****", videoid);
            var rec = this.findVideoRecordByID(videoid);

            if (rec == null) return;

            if (rec.es_customimageurl !== null && rec.es_customimageurl != "" && rec.es_customimageurl.indexOf("#") == -1) {
                var customimage = rec.es_customimageurl;
                var n = customimage.indexOf("_small");

                if (n == -1) {
                    playercode = playercode.replace("****youtubegallery-video-customimage****", customimage);

                    for (i = 0; i < 2; i++) {
                        playercode = playercode.replace("***code_begin***", "");
                        playercode = playercode.replace("***code_end***", "");
                    }
                } else playercode = YoutubeGalleryCleanCode(playercode);
            } else playercode = YoutubeGalleryCleanCode(playercode);

            playercode = playercode.replace("****youtubegallery-video-link****", rec.es_link);
            playercode = playercode.replace("****youtubegallery-video-startsecond****", rec.es_startsecond);
            playercode = playercode.replace("****youtubegallery-video-endsecond****", rec.es_endsecond);
            playercode = playercode.replace("autoplay=0", "autoplay=1");
            playercode = playercode.replace("****scriptbegin****", "<script ");
            playercode = playercode.replace("****scriptend****", "</script>");
            var ygsc = document.getElementById("YoutubeGallerySecondaryContainer" + this.videolistid + "");
            ygsc.innerHTML = playercode;
            ygsc.style.display = "block";

            if (playercode.indexOf('data-marker="DYNAMIC PLAYER"') != -1) {
                this.ApiStart = rec.es_startsecond;
                this.ApiEnd = rec.es_endsecond;

                if (videosource == "youtube") {
                    this.youtube_SetPlayer_(videoid);
                } else if (videosource == "vimeo") {
                    this.vimeo_SetPlayer_(videoid);
                } else {
                    eval("this.youtubegallery_updateplayer_" + videosource + "(videoid,true)");
                }
            }

            var title_obj_name = "YoutubeGalleryVideoTitle" + this.videolistid + "";
            var tObj = document.getElementById(title_obj_name);
            var description_obj_name = "YoutubeGalleryVideoDescription" + this.videolistid + "";
            var dObj = document.getElementById(description_obj_name);
            var t = this;

            if (tObj) {
                this.element_removeClass(title_obj_name, "ygTitle-visible");
                this.element_addClass(title_obj_name, "ygTitle-hidden");
                var title = rec.es_title;
                tObj.innerHTML = title.replaceAll('_quote_', '&quot;');
                setTimeout(function () {
                    t.element_removeClass(title_obj_name, "ygTitle-hidden");
                    t.element_addClass(title_obj_name, "ygTitle-visible");
                }, 200);
            }

            if (dObj) {
                this.element_removeClass(description_obj_name, "ygDescription-visible");
                this.element_addClass(description_obj_name, "ygDescription-hidden");
                var desc = rec.es_description;
                desc = desc.replaceAll('_thelinebreak_', '<br />');
                desc = desc.replaceAll('_quote_', '&quot;');
                desc = desc.replaceAll('_email_', '@');
                dObj.innerHTML = desc;
                setTimeout(function () {
                    t.element_removeClass(description_obj_name, "ygDescription-hidden");
                    t.element_addClass(description_obj_name, "ygDescription-visible");
                }, 200);
            }

            if (this.openinnewwindow == 5) {
                //Jump to the player anchor:"youtubegallery"
                window.location.hash = "youtubegallery";
            }

            this.highlightCurrentThumb(id);
        }
    }]);

    return YoutubeGalleryPlayerObject;
}();

function YoutubeGalleryCleanCode(playercode) {
    do {
        var b = playercode.indexOf("***code_begin***");
        var e = playercode.indexOf("***code_end***");
        if (b != -1 && e != -1) playercode = playercode.substr(0, b) + playercode.substr(e + 14);
        if (b == -1 || e == -1) break;
    } while (1 == 1);

    return playercode;
}