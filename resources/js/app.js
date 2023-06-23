import axios from 'axios';
import './bootstrap';

const form = document.getElementById('form1');
const inputValue = document.getElementById('input');
const messageList = document.getElementById('message-list');

form.addEventListener('submit', function(event){

    event.preventDefault();
    const userInput = inputValue.value;

    axios.post('/input-message', {
        message: userInput
    })

});

const channel = Echo.channel('public.chat.1');

channel.subscribed(() => {
    console.log('subscribed!');
}).listen('.message-sent', (event) => {
    console.log(event);
    const message = event.message;

    const li = document.createElement('li');
    li.textContent = message;

    messageList.append(li);
})
