import axios from 'axios';
import './bootstrap';

const form = document.getElementById('form1');
const messageList = document.getElementById('message-list');
const container = document.getElementById('message-container');
const mediaContainer = document.getElementById("media-div");
const media = document.getElementById("media-player");
const playpause = document.getElementById("playpause");
const progress = document.getElementById("progress");
const progressBar = document.getElementById("progress-bar");
const fullscreen = document.getElementById("fs");
const timeText = document.getElementById("time-text");
const volumeSlider = document.getElementById("volume-slider");
const volumeToggle = document.getElementById("volume-toggle");
const alertContainer = document.getElementById("alert-container");
const userList = document.getElementById("user-list");
var currentRole;
var currentUsers = [];

// var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
// var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
//   return new bootstrap.Tooltip(tooltipTriggerEl)
// })

var current_id; // Keeping track of the current video in the player.

const channel = Echo.join('presence.chat.' + currentRoom);

media.addEventListener("loadedmetadata", () => {
    progressBar.ariaValueMax = media.duration;
    updateBar();
});

media.addEventListener("timeupdate", () => {
    if (!progressBar.ariaValueMax){
        progressBar.ariaValueMax = media.duration;
    }
    updateBar();
});

media.addEventListener("ended", () => {
    playpause.innerHTML = "&#x1F782;";
});


// This version uses the play event fired by the video player element
// however it causes an infinite loop of events to be fired off if used
// in this manner.
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
    const inputValue = document.getElementById('input');
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
    if(currentRole != 'Restricted'){
        const scrubTime = (e.offsetX / progress.offsetWidth) * media.duration;
        media.currentTime = scrubTime;
        updateBar();
    }
}

function broadcastTime(e){
    mousedown = false;
    const time = (e.offsetX / progress.offsetWidth) * media.duration;
    axios.post('/change-time', {
        time: time,
        room_id: currentRoom
    })
}

function updateBar(){
    progressBar.ariaValueNow = media.currentTime;
    progressBar.style.width = `${Math.floor((media.currentTime * 100) / media.duration)}%`;
    timeText.innerHTML = timeTextFormat(media.currentTime) + '/' + timeTextFormat(media.duration);
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
        state: !media.muted,
        room_id: currentRoom
    })
}

function handleVolumeUpdate(username, volume) {
    media['volume'] = volume;
    volumeSlider.value = volume;
    if (volume == 0 || media.muted){
        volumeToggle.innerHTML = "&#128264;"; 
    }else{
        volumeToggle.innerHTML = "&#128266;";
    }
    addAlert(username, 'Set volume to ' + volume*100 + "%");
}

function handleFullscreen() {
    if (document.fullscreenElement !== null) {
        document.exitFullscreen();
        // videoReduce(false);
        setFullscreenData(false);
    } else {
        mediaContainer.requestFullscreen();
        // videoEnlarge(true);
        setFullscreenData(true);
    }
}


function videoEnlarge(state) {
    if (state) {
        media.width = window.innerWidth;
        media.height = window.innerHeight;
    }
}

function videoReduce(state) {
    if (!state) {
        media.width = 1280;
        media.height = 720;
    }
}

function setFullscreenData(state) {
    // videoReduce(!!state);
    mediaContainer.setAttribute("data-fullscreen", !!state);
}

function playPause(username){
    if (media.paused || media.ended) {
        media.play();
        playpause.innerHTML = "❚❚";
        addAlert(username, "Pressed play.");
    } else {
        media.pause();
        playpause.innerHTML = "&#x1F782;";
        addAlert(username, "Pressed pause.");
    }
}

// Seperate pause and plays functions for certain situations where toggle is bad
function play(username){
    if (media.paused || media.ended) {
        media.play();
        playpause.innerHTML = "❚❚";
    }
}
function pause(){
    if (!(media.paused || media.ending)) {
        media.pause();
        playpause.innerHTML = "&#x1F782;";
    }
}

// Used for adding general alerts regarding media state changes e.g. play/pause
function addAlert(username, message){
    const div = document.createElement('div');
    div.classList.add('alert');
    div.classList.add('alert-dismissible');
    div.classList.add('fade');
    div.classList.add('show');

    if(username == currentUser){
        div.classList.add('alert-primary');
    }else{
        div.classList.add('alert-light');
    }

    const userSpan = document.createElement('span');
    const msgSpan = document.createElement('span');
    const strong = document.createElement('strong');
    
    strong.textContent = username + ": ";
    userSpan.append(strong);
    
    msgSpan.textContent = message;

    div.append(userSpan,msgSpan);

    alertContainer.prepend(div);

    alertContainer.scrollTop = 0;

    setTimeout(function() {
        bootstrap.Alert.getOrCreateInstance(div).close();
    }, 3000);
}

// Used for adding chat messages
function addMessage(username, message, auto){
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
    if (auto == true){
        span.textContent = username + message;
    }else{
        span.textContent = username + ': ' + message;
    }
    
    

    li.append(span, timeSpan);

    messageList.append(li);

    container.scrollTop = container.scrollHeight;
}

function setTime(username, time) {
    addAlert(username, 'Set time to ' + timeTextFormat(time));
    media.currentTime = time;
}

function formatTime(number){
    if(number < 10){
        number = '0' + number;
    }
    return number;
}

function muteUnmute(username,state){
    media.muted = state;
    if (media.muted && media.volume != 0){
        addAlert(username, 'Muted the video.');
        volumeToggle.innerHTML = "&#128264;";
    }else if (!media.muted && media.volume != 0){
        addAlert(username, 'Unmuted the video.');
        volumeToggle.innerHTML = "&#128266;"; 
    }
}


channel
    .here((users) => {
        console.log('Subscribed to room channel ' + currentRoom + '!');
        console.log({users});
        currentUsers = [...users];
        //populateUserList();
    })

    .joining((user) => {
        console.log(user.username, 'joined')
        addMessage(user.username, ' joined the room.', true);
        //addUserList(user.username);
        // When someone new joins the room, we want to broadcast the current  media file again to make sure
        // they have the current file in their player. If there is no current file of course, this 
        // will not occur as no file is the default state when entering the room.
        if (current_id != null){
            axios.post('/media-set', {
                file: current_id,
                room_id: currentRoom
            })
        }
        // If a new user joins we want to broadcast the current time of the other users to them so they're caught up.
        // Of course, the default is to be at time 0 so there's no point doing this if the current time for everyone
        // else is 0. The Broadcasts below are essentially the same thing but for the other values we want joining users
        // to be updated with.
        if(media.currentTime != 0){
            axios.post('/change-time', {
                time: media.currentTime,
                room_id: currentRoom
            })
        }
        if(media.volume != 1){
            axios.post('/change-volume', {
                volume: media.volume,
                room_id: currentRoom
            })
        }
        if(media.muted){
            axios.post('/mute-unmute', {
                state: media.muted,
                room_id: currentRoom
            })
        }
        // Pauses the video when someone new joins the room. If the video is already paused, this will do nothing.
        pause();
    })

    .leaving((user) => {
        console.log({user}, 'left')
        addMessage(user.username, ' left the room.', true);
        //removeUserList(user.username);
    })

    .listen('.message-sent', (event) => {
        console.log(event);
        const message = event.message;
        const username = event.user.username;
        addMessage(username, message, false);
    })

    .listen('.media-set', (event) => {
        console.log(event);
        const newSrc = event.file.url;
        const type = event.file.type;
        const title = event.file.title;
        const username = event.user.username;

        // Preventing the same media source from being set again if it's already set.
        // This is mainly for when new users join and the set-video broadcast goes out.
        if (event.file.id != current_id){
            current_id = event.file.id;
            setSrc(newSrc,title,type);
        }
        // Sending the alert regardless so everyone is on the same page (Might change this)   
        addAlert(username,'Set the ' + type + ' to ' + title);
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

    .listen('.role-changed', (event) => {
        console.log(event);
        if(event.user.username == currentUser){
            currentRole = event.role.role;
            console.log(currentRole);
        }
    })

    // .listen('.update-queue', (event) => {
    //     console.log(event);
    //     const username = event.user.username;
    //     addAlert(username, "Updated the queue.");
    // })

    // -- Old user list implementation --
    
    // function populateUserList() {
    //     currentUsers.forEach((user) => {
    //         addUserList(user.username);
    //     })     
    // }

    // function addUserList(username) {
    //     const li = document.createElement('li');
    //     const span = document.createElement('span');
    //     const button = document.createElement('button');
    //     button.classList.add('btn');
    //     button.classList.add('btn-secondary');
    //     button.classList.add('btn-sm');
    //     button.type = 'button';
    //     button.textContent = '⁝';


    //     span.classList.add('list-group-item');
    //     span.textContent = username + " ";
    //     if(username == currentUser){
    //         span.classList.add('text-bg-primary');
    //     }else{
    //         span.classList.add('text-bg-light');
    //     }
    //     li.id = "list-" + username;
    //     span.append(button);
    //     li.append(span);

    //     userList.append(li);
    // }

    // function removeUserList(username) {
    //     const li = document.getElementById("list-" + username);
    //     userList.removeChild(li);
    // }


