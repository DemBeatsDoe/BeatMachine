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

    //Function to add a song to the playlist
    function addSongToPlaylist(songName, trackID) {
        playlist.push({name: songName, url:"http://api.soundcloud.com/tracks/"+trackID});
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
        redrawBar(0);
        $("#songTitleLabel").text(playlist[index].name);
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
        //Clear the canvas
        ctx.clearRect(0, 0, barCanvas.width, barCanvas.height);

        //Draw base line
        ctx.globalAlpha = 0.4;
        ctx.strokeStyle = "#fefefe";
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(0, barCanvas.height/2);
        ctx.lineTo(barCanvas.width, barCanvas.height/2);
        ctx.closePath();
        ctx.stroke();

        //Draw progress line
        ctx.globalAlpha = 1;
        ctx.beginPath();
        ctx.moveTo(0, barCanvas.height/2);
        ctx.lineTo(barCanvas.width*percentageComplete, barCanvas.height/2);
        ctx.closePath();
        ctx.stroke();


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

    return {
        emptyPlaylist: emptyPlaylist,
        addSongToPlaylist: addSongToPlaylist,
        playSongAt: playSongAt,
        togglePlay: togglePlay,
        playNextSong: playNextSong,
        playPrevSong: playPrevSong,
        getCurrentTrackIndex: getCurrentTrackIndex
    }

})($);