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

var current_id; // Keeping track of the current video in the player.

const channel = Echo.join('presence.chat.' + currentRoom);

video.addEventListener("loadedmetadata", () => {
    progressBar.ariaValueMax = video.duration;
});

video.addEventListener("timeupdate", () => {
    if (!progressBar.ariaValueMax){
        progressBar.ariaValueMax = video.duration;
    }
    progressBar.ariaValueNow = video.currentTime;
    progressBar.style.width = `${Math.floor((video.currentTime * 100) / video.duration)}%`;
    //progressBar.innerHTML = `${progressBar.style.width}`;
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
    }
}

function videoReduce(state) {
    if (!state) {
        video.width = 1280;
        video.height = 720;
    }
}

function setFullscreenData(state) {
    videoReduce(!!state);
    videoContainer.setAttribute("data-fullscreen", !!state);
}


form.addEventListener('submit', function(event){

    event.preventDefault();
    const userInput = inputValue.value;

    axios.post('/input-message', {
        message: userInput,
        room_id: currentRoom
    })

    form.reset();
});

function playPause(username){
    if (video.paused || video.ended) {
        video.play();
        addMessage(username, "User pressed play.");
    } else {
        video.pause();
        addMessage(username, "User pressed pause.");
    }
}

function play(username){
    if (video.paused || video.ended) {
        video.play();
        addMessage(username, "User pressed play.");
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
    })

    .leaving((user) => {
        console.log({user}, 'left')
        addMessage(user.username, 'User has left the room');
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
