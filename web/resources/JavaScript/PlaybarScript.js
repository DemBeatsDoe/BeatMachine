var playBar = (function (jQueryRef) {
    var $ = jQueryRef;

    //Script for controlling soundcloud widget API
    var scWidget;
    var player;
    var barCanvas;
    var ctx;
    var currentTrackIndex = 0;
    var playlist = [];
    var isSongPlaying = false;

    //Function to replace the currently playing song in the playlist and start playing it
    function playNewSong(name, songArtist, id) {
        playSongAt(addSongToPlaylist(name, songArtist, id));
    }

    function loadPlaylist(songsArray, autoplay=true) {
        emptyPlaylist();
        for (i = 0; i < songsArray.length; i++) {
            addSongToPlaylist(songsArray[i].songName, songsArray[i].songArtist, songsArray[i].trackID, songsArray[i].songArt);
        }
        playSongAt(0, autoplay);
    }

    //Function to add a song to the playlist
    function addSongToPlaylist(songName, songArtist, trackID, songArt='https://upload.wikimedia.org/wikipedia/en/1/16/All_star.jpg') {
        //Check if soundcloud or youtube link
        var properties = trackID.split(":");
        if(properties[0] == "YT"){
            playlist.push({name: songName, artist: songArtist, url: properties[1], art: songArt, YT:true});
        }
        else {
            playlist.push({name: songName, artist: songArtist, url: "http://api.soundcloud.com/tracks/" + trackID, art: songArt, YT:false});
        }

        return playlist.length-1;
    }

    //Function to play the current song
    function playSong() {

        //Check if youtube
        if(playlist[currentTrackIndex].YT){
            player.playVideo();
        }
        //Otherwise assume soundcloud
        else{
            scWidget.play();
        }

        $("#playPauseIcon").removeClass().addClass('glyphicon glyphicon-pause');
    }

    //Function to pause the current song
    function pauseSong() {
        player.pauseVideo();
        scWidget.pause();
        $("#playPauseIcon").removeClass().addClass('glyphicon glyphicon-play');
    }

    function togglePlay() {
        if(isSongPlaying){
            pauseSong();
            isSongPlaying = false;
        }
        else{
            playSong();
            isSongPlaying = true;
        }
    }

    //Empties the playlist
    function emptyPlaylist() {
        playlist = [];
        currentTrackIndex = 0;
    }

    //Function to play next song
    function playNextSong() {
        var newTrackIndex = (currentTrackIndex+1)%playlist.length;//Play the next song
        playSongAt(newTrackIndex);
    }

    //Function to play the previous song
    function playPrevSong() {
        currentTrackIndex = (((currentTrackIndex-1)%playlist.length)+playlist.length)%playlist.length;
        playSongAt(currentTrackIndex);
    }

    //Function to play a specific song in the playlist
    function playSongAt(index, autoplay=true) {
        //redrawBar(0);
        $('.playingIcon').hide();
        $('#playingIcon' + index).show();
        pauseSong();

        //Set the song name, artist, art
        $('#songTitleLabel').stop(false, true).animate({'opacity': 0}, 0, function () {
            $(this).text(playlist[index].name);
        }).animate({'opacity': 1}, 500);
        $('#songArtistLabel').stop(false, true).animate({'opacity': 0}, 0, function () {
            $(this).text(playlist[index].artist);
        }).animate({'opacity': 1}, 500);

        $('#songAlbumArt').stop(false, true).animate({'opacity': 0}, 0, function () {
            $(this).attr('src',playlist[index].art).show();
        }).animate({'opacity': 1}, 500);
        //$("#songTitleLabel").text(playlist[index].name);

        //Set the song art
        //$("#playbarIcon0").attr("src", playlist[index].art);
        $("#playbarIcon0").fadeTo(0,0.30, function() {
            $("#playbarIcon0").attr("src", playlist[index].art);
        }).fadeTo(500,1);

        $("#playbarIconF1").attr("src", playlist[negMod(index+1,playlist.length)].art);
        $("#playbarIconB1").attr("src", playlist[negMod(index-1,playlist.length)].art);
        $("#playbarIconF2").attr("src", playlist[negMod(index+2,playlist.length)].art);
        $("#playbarIconB2").attr("src", playlist[negMod(index-2,playlist.length)].art);

        //Load the song
        //Check if a youtube song
        if(playlist[index].YT){
            player.loadVideoById(playlist[index].url, 0, "small");
        }
        //Otherwise assume soundcloud
        else
        {
            scWidget.load(playlist[index].url, {auto_play: autoplay});
        }
        currentTrackIndex = index;

        if(autoplay){
            isSongPlaying = true;
            $("#playPauseIcon").removeClass().addClass('glyphicon glyphicon-pause');
        }

        playSong();

        //Fire event
        var event = new Event("newSongLoaded");
        document.dispatchEvent(event);
    }

    function getCurrentTrackIndex() {
        return currentTrackIndex;
    }

    //Function to redraw the progress bar at a particular location
    function redrawBar(percentageComplete) {
        var edgeOffset = 5;

        //Clear the canvas
        ctx.clearRect(0, 0, barCanvas.width, barCanvas.height);

        //Draw base line
        ctx.globalAlpha = 0.4;
        ctx.strokeStyle = "#fefefe";
        ctx.lineCap = "round";
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(edgeOffset, barCanvas.height/2);
        ctx.lineTo(barCanvas.width-edgeOffset, barCanvas.height/2);
        ctx.stroke();

        //Draw progress line
        ctx.save();
        ctx.globalAlpha = 1;
        ctx.lineWidth = 4;
        ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
        ctx.shadowBlur = 4;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 1;
        ctx.beginPath();
        ctx.moveTo(edgeOffset, barCanvas.height/2);
        ctx.lineTo(edgeOffset+(barCanvas.width-edgeOffset*2)*percentageComplete, barCanvas.height/2);
        ctx.stroke();

        //Draw circle
        ctx.globalAlpha = 1;
        ctx.fillStyle = "#fefefe";
        ctx.shadowColor = 'rgba(0, 0, 0, 0.7)';
        ctx.shadowBlur = 5;
        ctx.shadowOffsetY = 2;
        ctx.beginPath();
        ctx.arc(edgeOffset+barCanvas.width*percentageComplete,barCanvas.height/2,5,0,2*Math.PI);
        ctx.fill();
        ctx.restore();

        //ctx.fillRect(0,0, 10,10);
    }

    $(window).keypress(function (e) {
        if (e.keyCode === 0 || e.keyCode === 32) {
            e.preventDefault();
            togglePlay();
            return false;
        }
    })

    //Bind widget and set event listeners
    $(function() {
        //Set canvas
        barCanvas = document.getElementById("progressBar");
        ctx = barCanvas.getContext("2d");
        //Resize canvas
        barCanvas.style.width ='100%';
        barCanvas.width  = barCanvas.offsetWidth;
        ctx.canvas.width = barCanvas.width;
        redrawBar(0);

        //---------------------------------------------Youtube---------------------------------------:

        // 1. This code loads the IFrame Player API code asynchronously.
        var tag = document.createElement('script');

        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        //-----------------------------------------Soundcloud-------------------------------------:
        //Set widget
        scWidget = SC.Widget("so");

        //Listen for song finish
        scWidget.bind(SC.Widget.Events.FINISH, function() {
            playNextSong();
        });

        //Listen for progress of current song
        scWidget.bind(SC.Widget.Events.PLAY_PROGRESS, function (soundInfo ) {
            redrawBar((soundInfo.relativePosition));
        });
    });

    function initYoutubePlayer(){
        //---------------------------------------------Youtube---------------------------------------:
        player = new YT.Player('player', {
            height: '10',
            width: '10',
            videoId: '',
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });

        // 4. The API will call this function when the video player is ready.
        function onPlayerReady(event) {
            //event.target.playVideo();
        }

        //Listen for song finish
        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.ENDED) {
                playNextSong();
            }
        }

        //Listen for progress of current song
        setInterval(function(){
            if(playlist[currentTrackIndex].YT){
                var ratio = Math.min(1, player.getCurrentTime()/player.getDuration());
                redrawBar(ratio);
            }
        }, 50);
    }

    function negMod(num, den) {
        return (((num)%den)+den)%den;
    }

    return {
        initYoutubePlayer:initYoutubePlayer,
        loadPlaylist: loadPlaylist,
        emptyPlaylist: emptyPlaylist,
        addSongToPlaylist: addSongToPlaylist,
        playSongAt: playSongAt,
        togglePlay: togglePlay,
        playNextSong: playNextSong,
        playPrevSong: playPrevSong,
        getCurrentTrackIndex: getCurrentTrackIndex
    }

})($);

function onYouTubeIframeAPIReady() {
    playBar.initYoutubePlayer();
}