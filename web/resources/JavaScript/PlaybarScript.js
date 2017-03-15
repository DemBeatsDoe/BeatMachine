var playBar = (function (jQueryRef) {
    var $ = jQueryRef;

    //Script for controlling soundcloud widget API
    var scWidget;
    var barCanvas;
    var ctx;
    var currentTrackIndex = 0;
    var playlist = [];
    var isSongPlaying = false;

    //Function to replace the currently playing song in the playlist and start playing it
    function playNewSong(name, id) {
        playSongAt(addSongToPlaylist(name, id));
    }

    function loadPlaylist(songsArray, autoplay=true) {
        emptyPlaylist();
        for (i = 0; i < songsArray.length; i++) {
            addSongToPlaylist(songsArray[i].songName, songsArray[i].trackID, songsArray[i].songArt);
        }
        playSongAt(0, autoplay);
    }

    //Function to add a song to the playlist
    function addSongToPlaylist(songName, trackID, songArt="https://upload.wikimedia.org/wikipedia/en/1/16/All_star.jpg") {
        playlist.push({name: songName, url:"http://api.soundcloud.com/tracks/"+trackID, art: songArt});
        return playlist.length-1;
    }

    //Function to play the current song
    function playSong() {
        scWidget.play();
        $("#playPauseIcon").removeClass().addClass('glyphicon glyphicon-pause');
    }

    //Function to pause the current song
    function pauseSong() {
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
        currentTrackIndex = (currentTrackIndex+1)%playlist.length;
        playSongAt(currentTrackIndex);
    }

    //Function to play the previous song
    function playPrevSong() {
        currentTrackIndex = (((currentTrackIndex-1)%playlist.length)+playlist.length)%playlist.length;
        playSongAt(currentTrackIndex);
    }

    //Function to play a specific song in the playlist
    function playSongAt(index, autoplay=true) {
        //redrawBar(0);

        //Set the song name
        $('#songTitleLabel').stop(false, true).animate({'opacity': 0}, 0, function () {
            $(this).text(playlist[index].name);
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
        scWidget.load(playlist[index].url, {auto_play: autoplay});
        currentTrackIndex = index;

        if(autoplay){
            isSongPlaying = true;
            $("#playPauseIcon").removeClass().addClass('glyphicon glyphicon-pause');
        }

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

    //Bind widget and set event listeners
    $(function() {
        //Set canvas
        barCanvas = document.getElementById("progressBar");
        ctx = barCanvas.getContext("2d");
        //Resize canvas
        barCanvas.style.width ='100%';
        barCanvas.width  = barCanvas.offsetWidth;
        ctx.canvas.width = barCanvas.width;

        //Set widget
        scWidget = SC.Widget("so");

        redrawBar(0);

        //Listen for song finish
        scWidget.bind(SC.Widget.Events.FINISH, function() {
            playNextSong();
        });

        //Listen for progress of current song
        scWidget.bind(SC.Widget.Events.PLAY_PROGRESS, function (soundInfo ) {
            redrawBar((soundInfo.relativePosition));
        })
    });

    function negMod(num, den) {
        return (((num)%den)+den)%den;
    }

    return {
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