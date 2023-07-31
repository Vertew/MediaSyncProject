import axios from 'axios';
import './bootstrap';

const form = document.getElementById('form1');
const alertContainer = document.getElementById("notification-container");

const channel = Echo.join('presence.chat.0');

function addAlert(message){
    const div = document.createElement('div');
    div.classList.add('alert');
    div.classList.add('alert-dismissible');
    div.classList.add('alert-success');
    div.classList.add('fade');
    div.classList.add('show');

    const msgSpan = document.createElement('span');
    const strong = document.createElement('strong');

    strong.textContent = message;
    
    msgSpan.append(strong);

    div.append(msgSpan);

    alertContainer.prepend(div);

    alertContainer.scrollTop = 0;

    setTimeout(function() {
        bootstrap.Alert.getOrCreateInstance(document.querySelector(".alert")).close();
    }, 6000);
}

channel

    .listen('.user-unbanned', (event) => {
        if(AuthUser == event.recipient.id){
            console.log(event);
            addAlert('You have been unbanned from ' + event.room.name + ' by ' + event.user.username + '!');
        }
    })