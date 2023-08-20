import axios from 'axios';
import './bootstrap';
import { createPicker } from 'picmo'; // Picmo used for the emoji selection menu courtesy of Joe Attardi: https://github.com/joeattardi/picmo

const form = document.getElementById('form1');
const chatInput = document.getElementById('input');
const messageList = document.getElementById('message-list');
const container = document.getElementById('message-container');
const mediaContainer = document.getElementById("media-div");
const videoContainer = document.getElementById("video-container");
const media = document.getElementById("media-player");
const playpause = document.getElementById("playpause");
const progress = document.getElementById("progress");
const progressBar = document.getElementById("progress-bar");
const fullscreen = document.getElementById("fs");
const timeText = document.getElementById("time-text");
const volumeSlider = document.getElementById("volume-slider");
const volumeToggle = document.getElementById("volume-toggle");
const alertContainer = document.getElementById("alert-container");
const reactionList = document.getElementById("reaction-list");
const emojiDropdown = document.getElementById("emoji-dropdown");
const emojiDropdown2 = document.getElementById("emoji-dropdown-2");
const lockButton = document.getElementById("lock-button");
const title = document.getElementById("title");
const playAlertDiv = document.getElementById("play-alert-div");
const mediaControls = document.getElementById("media-controls");

var currentUsers = [];
var previousMessager;
var currentRole;
var current_id; // Keeping track of the current video in the player.
var timeOutID;

const channel = Echo.join('presence.chat.' + currentRoom);

const privateChannel = Echo.private('private.user.' + currentUserId);

var rootElement = document.querySelector('#picker');
const picker = createPicker({
  rootElement,
});

picker.addEventListener('emoji:select', event => {
    bootstrap.Dropdown.getOrCreateInstance(emojiDropdown).toggle()
    axios.post('/reaction-sent', {
        message: event.emoji,
        room_id: currentRoom
    })
});

rootElement = document.querySelector('#picker-2');
const picker2 = createPicker({
  rootElement,
});

picker2.addEventListener('emoji:select', event => {
    bootstrap.Dropdown.getOrCreateInstance(emojiDropdown2).toggle()
    chatInput.value+=event.emoji;
});


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

videoContainer.addEventListener('click', (e) => {
    axios.post('/play-pause', {
        room_id: currentRoom
    })
});

mediaContainer.addEventListener("mousemove", (e) => {
    showControls();
});

mediaContainer.addEventListener("mouseleave", (e) => {
    if (media.paused != true){
        clearTimeout(timeOutID);
        mediaControls.style.opacity = 0;
    }
});

document.addEventListener("keydown", (e) => {
    if(e.code == 'Space' && e.target == document.body) {
        e.preventDefault();
        axios.post('/play-pause', {
            room_id: currentRoom
        })
    }
});

fullscreen.addEventListener("click", (e) => {
    handleFullscreen();
});

document.addEventListener("fullscreenchange", (e) => {
    setFullscreenData(!!document.fullscreenElement);
});

form.addEventListener('submit', function(event){
    event.preventDefault();
    let userInput = chatInput.value;
    if(currentRole != 'Restricted'){
        axios.post('/input-message', {
            message: userInput,
            room_id: currentRoom
        })
    }else{
        addMessage(currentUser, 'You do not have permission to type in chat.', true);
    }
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

function showControls(){
    clearTimeout(timeOutID);
    mediaControls.style.opacity = 1;
    if (media.paused != true){
        timeOutID = setTimeout(function() {
            mediaControls.style.opacity = 0;
        }, 4000);
    }
}


function toggleLock(){
    if(lockButton.innerText == "Lock Room"){
        lockButton.innerText = "Unlock Room";
        lockButton.classList.replace("btn-danger","btn-success");
        lockButton.title = "Allow users to join the room";
        title.innerText = title.innerText.replace("Open", "Locked");
        title.classList.replace("bg-success","bg-danger");
    }else{
        lockButton.innerText = "Lock Room";
        lockButton.classList.replace("btn-success","btn-danger");
        lockButton.title = "Prevent users from joining the room";
        title.classList.replace("bg-danger","bg-success");
        title.innerText = title.innerText.replace("Locked", "Open");
    }
}

progress.addEventListener("click", scrub);
let mousedown = false;
let originalTime = 0;
progress.addEventListener("mousedown", () => (mousedown = true) && (originalTime = media.currentTime));
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

    if(time > originalTime){
        var symbol='⏩';
    }else if(time < originalTime){
        var symbol='⏪';
    }else{
        // This doesn't work in chrome because chrome rounds the media.currentTime value down for some reason unlike firefox
        var symbol='noAlert'
    }

    axios.post('/change-time', {
        time: time,
        symbol: symbol,
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
    if (media['volume'] >= volume){
        var symbol = '🔉';
    }else{
        var symbol = '🔊';
    }
    media['volume'] = volume;
    volumeSlider.value = volume;
    if (volume == 0 || media.muted){
        volumeToggle.innerHTML = "&#128264;"; 
    }else{
        volumeToggle.innerHTML = "&#128266;";
    }
    addAlert(username, 'set volume to ' + Math.round(volume*100) + "% " + symbol);
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


// function videoEnlarge(state) {
//     if (state) {
//         media.width = window.innerWidth;
//         media.height = window.innerHeight;
//     }
// }

// function videoReduce(state) {
//     if (!state) {
//         media.width = 1280;
//         media.height = 720;
//     }
// }

function setFullscreenData(state) {
    // videoReduce(!!state);
    mediaContainer.setAttribute("data-fullscreen", !!state);
}

function playPause(username){
    if (media.paused || media.ended) {
        media.play();
        playpause.innerHTML = "❚❚";
        addAlert(username, "pressed play 🞂");
        playPauseAlert("&#x1F782;");
    } else {
        media.pause();
        playpause.innerHTML = "&#x1F782;";
        addAlert(username, "pressed pause ❚❚");
        playPauseAlert("❚❚");
    }
    showControls();
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
        playpause.innerHTML = "&#x1F782; ";
        playPauseAlert("❚❚");
    }
}

function playPauseAlert(message) {
    const div = document.createElement('div');
    div.classList.add('alert');
    div.classList.add('alert-light');
    div.classList.add('fade');
    div.classList.add('show');

    const h1 = document.createElement('h1');
    h1.innerHTML = message;
    div.append(h1);

    while (playAlertDiv.firstChild) {
        playAlertDiv.removeChild(playAlertDiv.firstChild);
    }

    playAlertDiv.append(div);

    setTimeout(function() {
        bootstrap.Alert.getOrCreateInstance(div).close();
    }, 1000);

}

// Used for adding general alerts regarding media state changes e.g. play/pause
function addAlert(username, message, colour='light'){
    const div = document.createElement('div');
    div.classList.add('alert');
    div.classList.add('alert-'+colour);
    div.classList.add('alert-dismissible');
    div.classList.add('fade');
    div.classList.add('show');
    div.classList.add('m-0');
    div.classList.add('py-1');
    div.classList.add('px-0');

    if(username == currentUser){
        div.classList.add('text-bg-primary');
    }

    const userSpan = document.createElement('span');
    const msgSpan = document.createElement('span');
    const strong = document.createElement('strong');
    
    strong.textContent = username + " ";
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
function addMessage(username, message, auto, name = username){

    let displayname = username;
    if(myFriends.includes(username)){
        displayname = name;
    }

    const today = new Date();
    const li = document.createElement('li');
    const liHeader = document.createElement('li');

    li.classList.add('list-group-item');
    li.classList.add('d-flex');
    li.classList.add('justify-content-between');
    li.classList.add('align-items-center');
    li.classList.add('rounded-0');
    li.classList.add('border-0');

    liHeader.classList.add('list-group-item');
    liHeader.classList.add('d-flex');
    liHeader.classList.add('justify-content-between');
    liHeader.classList.add('align-items-center');
    liHeader.classList.add('border-top');
    liHeader.classList.add('border-bottom-0');
    liHeader.classList.add('border-end-0');
    liHeader.classList.add('border-start-0');
    

    if(username == currentUser){
        li.classList.add('text-bg-primary');
        liHeader.classList.add('text-bg-primary');
        displayname = name;
    }

    if(String(today.getMinutes()).length == 1){
        var minutes = '0' + today.getMinutes();
    }else{
        var minutes = today.getMinutes();
    }

    var time = today.getHours() + ":" + minutes;

    const timeSpan = document.createElement('span');
    timeSpan.classList.add('small');
    timeSpan.textContent = time + "        ";
    
    const span = document.createElement('span');
    const spanHeader = document.createElement('span');
    const strong = document.createElement('strong');

    if(auto){
        let reactionText = document.createElement('strong');
        reactionText.textContent = message;
        span.append(reactionText);
        span.classList.add('small');
    }else{
        span.textContent = message;
    }

    strong.textContent = displayname;

    spanHeader.append(strong);
    liHeader.append(spanHeader);

    li.append(span, timeSpan);

    if(previousMessager == username){
        messageList.append(li);
    }else{
        messageList.append(liHeader);
        messageList.append(li);
    }

    container.scrollTop = container.scrollHeight;

    previousMessager = username;
}

function setTime(username, time , symbol) {
    if(symbol != 'noAlert'){
        addAlert(username, 'set time to ' + timeTextFormat(time) + ' ' + symbol);
    }
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
        addAlert(username, 'muted the video 🔈');
        volumeToggle.innerHTML = "&#128264;";
    }else if (!media.muted && media.volume != 0){
        addAlert(username, 'unmuted the video 🔊');
        volumeToggle.innerHTML = "&#128266;"; 
    }
}

function addReaction(username, emoji){
    const div = document.createElement('div');
    div.classList.add('alert');
    div.classList.add('alert-light');
    div.classList.add('fade');
    div.classList.add('show');

    if(username == currentUser){
        div.classList.add('text-bg-primary');
    }

    const h1 = document.createElement('h1');
    h1.textContent = emoji;

    div.textContent = username;
    div.append(h1);

    reactionList.append(div);

    setTimeout(function() {
        bootstrap.Alert.getOrCreateInstance(div).close();
    }, 3000);

}

privateChannel
    .listen('.request-accepted', (event) => {
        myFriends.push(event.acceptee);
    })

    .listen('.friend-removed', (event) => {
        let indexToRemove = myFriends.indexOf(event.user);
        myFriends.splice(indexToRemove,1);
    })
    

channel
    .here((users) => {
        console.log('Subscribed to room channel ' + currentRoom + '!');
        console.log({users});
        currentUsers = [...users];

        // Setting the file in the media player upon loading the page.
        current_id = initialFile.id;
        console.log(initialFile);
        setSrc(initialFile.url,initialFile.title,initialFile.type);

        //populateUserList();
    })

    .joining((user) => {
        console.log(user.username, 'joined')
        addMessage(user.username, ' User joined the room.', true);

        // If a new user joins we want to broadcast the current video time to them so they're caught up with the others.
        // Of course, the default is to be at time 0 so there's no point doing this if the current time for everyone
        // else is 0. The other broadcasts below are essentially the same thing but for the sound.
        if(media.currentTime != 0){
            axios.post('/change-time', {
                time: media.currentTime,
                symbol: 'noAlert',
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
        addMessage(user.username, 'User left the room.', true);
        //removeUserList(user.username);
    })

    .listen('.message-sent', (event) => {
        console.log(event);
        const message = event.message;
        const username = event.user.username;
        const name = event.name;
        addMessage(username, message, false, name);
    })

    .listen('.media-set', (event) => {
        console.log(event);
        const newSrc = event.file.url;
        const type = event.file.type;
        const title = event.file.title;
        const username = event.user.username;

        // Preventing the same media source from being set again if it's already set.
        // Arguably not necessary but it's here for now.
        if (event.file.id != current_id){
            current_id = event.file.id;
            setSrc(newSrc,title,type);
            addAlert(username,'set the ' + type + ' to ' + title);
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
        const symbol = event.symbol;
        setTime(username,time,symbol);
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

    .listen('.reaction-sent', (event) => {
        console.log(event);
        const emoji = event.message;
        const username = event.user.username;
        addReaction(username, emoji);
        addMessage(username,'User reacted with ' + emoji, true);
    })

    .listen('.lock-toggled', (event) => {
        console.log(event);
        const username = event.user.username;
        const type = event.type;
        toggleLock();
        if(type){
            addAlert(username, "locked the room.");
        }else{
            addAlert(username, "unlocked the room.");
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


