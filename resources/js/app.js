import axios from 'axios';
import './bootstrap';

const form = document.getElementById('form1');
const inputValue = document.getElementById('input');
const messageList = document.getElementById('message-list');
const container = document.getElementById('message-container');
const today = new Date();

form.addEventListener('submit', function(event){

    event.preventDefault();
    const userInput = inputValue.value;

    axios.post('/input-message', {
        message: userInput
    })

    form.reset();

});

function addMessage(username, message){

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

    const time = today.getHours() + ":" + minutes;

    const timeSpan = document.createElement('span');
    timeSpan.textContent = time + "        ";
    
    const span = document.createElement('span');
    span.textContent = username + ': ' + message;

    li.append(span, timeSpan);

    messageList.append(li);

    container.scrollTop = container.scrollHeight;
}

const channel = Echo.join('presence.chat.1');

channel
    .here((users) => {
        console.log('subscribed!');
        console.log({users});
    })

    .joining((user) => {
        console.log({user}, 'joined')
        addMessage(user.username, 'User has joined the room');
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
