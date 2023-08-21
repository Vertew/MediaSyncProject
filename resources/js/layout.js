import './bootstrap';

const alertContainer = document.getElementById("notification-container");

const myChannel = Echo.private('private.user.' + AuthUser);

if(document.contains(document.querySelector(".alert-success"))){
    setTimeout(function() {
        bootstrap.Alert.getOrCreateInstance(document.querySelector(".alert-success")).close();
    }, 6000);
}


function addAlert(message, colour='light'){
    const div = document.createElement('div');
    div.classList.add('alert');
    div.classList.add('alert-dismissible');
    div.classList.add('alert-'+colour);
    div.classList.add('fade');
    div.classList.add('show');
    div.classList.add('my-0');
    div.classList.add('rounded-0');

    const msgSpan = document.createElement('span');
    const strong = document.createElement('strong');

    strong.textContent = message;
    
    msgSpan.append(strong);

    div.append(msgSpan);

    alertContainer.prepend(div);

    alertContainer.scrollTop = 0;

    setTimeout(function() {
        bootstrap.Alert.getOrCreateInstance(div).close();
    }, 6000);
}

Livewire
    .on('requestSent', (event) => {
        addAlert('Friend request sent to ' + event + '!', 'success');
    })

Livewire
    .on('requestSendFail', (event) => {
        addAlert('You cannot send another request to ' + event + '.', 'warning');
    })

myChannel

    .listen('.user-unbanned', (event) => {
        addAlert('You have been unbanned from ' + event.room.name + ' by ' + event.user.username + '!', 'success');
    })

    .listen('.request-accepted', (event) => {
        addAlert(event.acceptee + ' accepted your friend request!', 'success');
    })

    .listen('.request-recieved', (event) => {
        addAlert('You have a new friend request from ' + event.sender, 'success');
    })