import axios from 'axios';
import './bootstrap';

const form = document.getElementById('form1');
const inputValue = document.getElementById('input');
const messageList = document.getElementById('message-list');
const container = document.getElementById('message-container');

const videoContainer = document.getElementById("video-div");
const video = document.getElementById("video_player");
const videoControls = document.getElementById("video-controls");
const playpause = document.getElementById("playpause");
const progress = document.getElementById("progress");
const progressBar = document.getElementById("progress-bar");
const fullscreen = document.getElementById("fs");
const timeText = document.getElementById("time-text");
const volumeSlider = document.getElementById("volume-slider");
const volumeToggle = document.getElementById("volume-toggle");

var current_id; // Keeping track of the current video in the player.

const channel = Echo.join('presence.chat.' + currentRoom);

video.addEventListener("loadedmetadata", () => {
    progressBar.ariaValueMax = video.duration;
    updateBar();
});

video.addEventListener("timeupdate", () => {
    if (!progressBar.ariaValueMax){
        progressBar.ariaValueMax = video.duration;
    }
    updateBar();
});

video.addEventListener("ended", () => {
    playpause.innerHTML = "&#x1F782;";
});

/*
video.addEventListener('play', (e) => {
    axios.post('/play-pause', {
        room_id: currentRoom
    })
});
*/

playpause.addEventListener('click', (e) => {
    axios.post('/play-pause', {
        room_id: currentRoom
    })
});

fullscreen.addEventListener("click", (e) => {
    handleFullscreen();
});

document.addEventListener("fullscreenchange", (e) => {
    setFullscreenData(!!document.fullscreenElement);
});

form.addEventListener('submit', function(event){
    event.preventDefault();
    const userInput = inputValue.value;
    axios.post('/input-message', {
        message: userInput,
        room_id: currentRoom
    })
    form.reset();
});

volumeSlider.addEventListener("change", broadcastVolume);
volumeToggle.addEventListener("click", broadcastMute);

/* 
progress.addEventListener("click", (e) => {
    const rect = progress.getBoundingClientRect();
    const pos = (e.pageX - rect.left) / progress.offsetWidth;
    video.currentTime = pos * video.duration;
});
*/

progress.addEventListener("click", scrub);
let mousedown = false;
progress.addEventListener("mousedown", () => (mousedown = true));
progress.addEventListener("mousemove", (e) => mousedown && scrub(e));
progress.addEventListener("mouseup", (e) => broadcastTime(e));

function scrub(e) {
    const scrubTime = (e.offsetX / progress.offsetWidth) * video.duration;
    video.currentTime = scrubTime;
    updateBar();
}

function broadcastTime(e){
    mousedown = false;
    const time = (e.offsetX / progress.offsetWidth) * video.duration;
    axios.post('/change-time', {
        time: time,
        room_id: currentRoom
    })
}

function updateBar(){
    progressBar.ariaValueNow = video.currentTime;
    progressBar.style.width = `${Math.floor((video.currentTime * 100) / video.duration)}%`;
    timeText.innerHTML = timeTextFormat(video.currentTime) + '/' + timeTextFormat(video.duration);
}

function timeTextFormat(time){
    const hours = formatTime(parseInt(time/(60*60),10));
    const minutes = formatTime(parseInt(time/60,10));
    const seconds = formatTime(Math.floor(time%60));

    if (hours == 0){
        return minutes + ':' + seconds;
    }else{
        return hours + ':' + minutes + ':' + seconds;
    }
}

function broadcastVolume(){
    axios.post('/change-volume', {
        volume: this.value,
        room_id: currentRoom
    })
}

function broadcastMute(){
    axios.post('/mute-unmute', {
        state: !video.muted,
        room_id: currentRoom
    })
}

function handleVolumeUpdate(username, volume) {
    video['volume'] = volume;
    volumeSlider.value = volume;
    if (volume == 0 || video.muted){
        volumeToggle.innerHTML = "&#128264;"; 
    }else{
        volumeToggle.innerHTML = "&#128266;";
    }
    addMessage(username, 'Set volume to ' + volume*100 + "%");
}

function handleFullscreen() {
    if (document.fullscreenElement !== null) {
        document.exitFullscreen();
        videoReduce(false);
        setFullscreenData(false);
    } else {
        videoContainer.requestFullscreen();
        videoEnlarge(true);
        setFullscreenData(true);
    }
}


function videoEnlarge(state) {
    if (state) {
        video.width = window.innerWidth;
        video.height = window.innerHeight;
        timeText.classList.add('text-light');
    }
}

function videoReduce(state) {
    if (!state) {
        video.width = 1280;
        video.height = 720;
        timeText.classList.remove('text-light');
    }
}

function setFullscreenData(state) {
    videoReduce(!!state);
    videoContainer.setAttribute("data-fullscreen", !!state);
}

function playPause(username){
    if (video.paused || video.ended) {
        video.play();
        playpause.innerHTML = "❚❚";
        addMessage(username, "Pressed play.");
    } else {
        video.pause();
        playpause.innerHTML = "&#x1F782;";
        addMessage(username, "Pressed pause.");
    }
}

// Seperate pause and plays functions for certain situations where toggle is bad
function play(username){
    if (video.paused || video.ended) {
        video.play();
        playpause.innerHTML = "❚❚";
    }
}
function pause(){
    if (!(video.paused || video.ending)) {
        video.pause();
        playpause.innerHTML = "&#x1F782;";
    }
}

function addMessage(username, message){
    const today = new Date();
    const li = document.createElement('li');
    li.classList.add('list-group-item');
    li.classList.add('d-flex');
    li.classList.add('justify-content-between');
    li.classList.add('align-items-center');

    if(username == currentUser){
        li.classList.add('text-bg-primary');
    }

    if(String(today.getMinutes()).length == 1){
        var minutes = '0' + today.getMinutes();
    }else{
        var minutes = today.getMinutes();
    }

    var time = today.getHours() + ":" + minutes;

    const timeSpan = document.createElement('span');
    timeSpan.textContent = time + "        ";
    
    const span = document.createElement('span');
    span.textContent = username + ': ' + message;

    li.append(span, timeSpan);

    messageList.append(li);

    container.scrollTop = container.scrollHeight;
}

function setTime(username, time) {
    addMessage(username, 'Set time to ' + timeTextFormat(time));
    video.currentTime = time;
}

function formatTime(number){
    if(number < 10){
        number = '0' + number;
    }
    return number;
}

function muteUnmute(username,state){
    video.muted = state;
    if (video.muted && video.volume != 0){
        addMessage(username, 'Muted the video.');
        volumeToggle.innerHTML = "&#128264;";
    }else if (!video.muted && video.volume != 0){
        addMessage(username, 'Unmuted the video.');
        volumeToggle.innerHTML = "&#128266;"; 
    }
}


channel
    .here((users) => {
        console.log('Subscribed to room channel ' + currentRoom + '!');
        console.log({users});
    })

    .joining((user) => {
        console.log(user.username, 'joined')
        addMessage(user.username, 'User has joined the room');
        // When someone new joins the room, the broadcast goes out to set the files again to make sure
        // they have the current file in their player. If there is no current file of course, this 
        // will not occur.
        if (current_id != null){
            axios.post('/video-set', {
                file: current_id,
                room_id: currentRoom
            })
        }
        if(video.currentTime != 0){
            axios.post('/change-time', {
                time: video.currentTime,
                room_id: currentRoom
            })
        }
        if(video.volume != 1){
            axios.post('/change-volume', {
                volume: video.volume,
                room_id: currentRoom
            })
        }
        if(video.muted){
            axios.post('/mute-unmute', {
                state: video.muted,
                room_id: currentRoom
            })
        }
        
        // Pauses the video when someone new joins the room
        pause();
    })

    .leaving((user) => {
        console.log({user}, 'left')
        addMessage(user.username, 'User has left the room.');
    })

    .listen('.message-sent', (event) => {
        console.log(event);
        const message = event.message;
        const username = event.user.username;
        addMessage(username, message);
    })

    .listen('.video-set', (event) => {
        console.log(event);
        const newSrc = event.file.url;
        const type = event.file.type;
        const title = event.file.title;
        const id = type + "_player";
        const username = event.user.username;

        // Preventing the same media source from being set again if it's already set.
        // This is mainly for when new users join and the set-video broadcast goes out.
        if (event.file.id != current_id){
            current_id = event.file.id;
            addMessage(username,'Set the ' + type + ' to ' + title);
            setSrc(id,newSrc,title,type);
        }    
    })

    .listen('.play-pause', (event) => {
        console.log(event);
        const username = event.user.username;
        playPause(username);
    })

    .listen('.time-change', (event) => {
        console.log(event);
        const username = event.user.username;
        const time = event.time;
        setTime(username,time);
    })

    .listen('.volume-change', (event) => {
        console.log(event);
        const username = event.user.username;
        const volume = event.volume;
        handleVolumeUpdate(username, volume);
    })

    .listen('.mute-unmute', (event) => {
        console.log(event);
        const username = event.user.username;
        const state = event.state;
        muteUnmute(username, state);
    })

