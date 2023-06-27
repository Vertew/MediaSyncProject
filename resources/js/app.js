import axios from 'axios';
import './bootstrap';

const form = document.getElementById('form1');
const inputValue = document.getElementById('input');
const messageList = document.getElementById('message-list');
const container = document.getElementById('message-container');

var current_id; // Keeping track of the current video in the player.

form.addEventListener('submit', function(event){

    event.preventDefault();
    const userInput = inputValue.value;

    axios.post('/input-message', {
        message: userInput,
        room_id: currentRoom
    })

    form.reset();
});


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

const channel = Echo.join('presence.chat.'+currentRoom);

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
        // This is mainly for when new users join and the set video broadcast goes out.
        if (event.file.id != current_id){
            current_id = event.file.id;
            addMessage(username,'Set the ' + type + ' to ' + title);
            setSrc(id,newSrc,title,type);
        }
    })
