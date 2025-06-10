let currentReceiverId = null;
let currentGroupId = null;
let pollingInterval = null;
let socket;

function initWebSocket() {
    socket = io('http://localhost:3000');
    
    socket.on('connect', () => {
        console.log('Connected to WebSocket server');
    });

    socket.on('newMessage', (data) => {
        if ((data.groupId && data.groupId === currentGroupId) || 
            (!data.groupId && (data.senderId === currentReceiverId || data.receiverId === currentReceiverId))) {
            const chatBox = document.getElementById('chatBox');
            const div = document.createElement('div');
            div.className = 'chat-message';
            div.innerHTML = `
                <span class="sender">${data.sender_name}</span>: ${data.message_text}
                <span class="timestamp">${data.sent_at}</span>
                ${data.sender_id === getCurrentUserId() || isGroupAdmin(data.groupId) ? `
                    <button onclick="editMessage(${data.id})">Edit</button>
                    <button onclick="deleteMessage(${data.id})">Delete</button>
                ` : ''}
            `;
            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
}

function selectChat(receiverId, groupId, name) {
    currentReceiverId = receiverId;
    currentGroupId = groupId;
    document.getElementById('chatHeader').innerText = `Chatting with ${name}`;
    loadMessages();
    socket.emit('joinChat', { senderId: getCurrentUserId(), receiverId, groupId });
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
}

function sendMessage() {
    const messageText = document.getElementById('messageText').value;
    if (!messageText) return;
    makeRequest('/teamchat/backend/chat/send', 'POST', `receiver_id=${currentReceiverId}&group_id=${currentGroupId}&message_text=${messageText}`, function (response) {
        if (response.success) {
            const data = {
                sender_id: getCurrentUserId(),
                receiver_id: currentReceiverId,
                group_id: currentGroupId,
                message_text: messageText,
                sender_name: 'You', // Replace with actual sender name from session
                sent_at: new Date().toISOString(),
                id: response.message_id // Assume backend returns message_id
            };
            socket.emit('sendMessage', data);
            document.getElementById('messageText').value = '';
        } else {
            alert(response.message);
        }
    });
}

// Helper to get current user ID (implement based on session)
function getCurrentUserId() {
    // Fetch from session via an API call or hidden input
    return document.getElementById('currentUserId').value; // Add a hidden input in HTML
}

// Check if user is group admin
function isGroupAdmin(groupId) {
    let isAdmin = false;
    makeRequest(`/teamchat/backend/chat/check_group_admin?group_id=${groupId}`, 'GET', null, function (response) {
        if (response.success) {
            isAdmin = response.is_admin;
        }
    });
    return isAdmin;
}

document.addEventListener('DOMContentLoaded', () => {
    initWebSocket();
    loadUsersAndGroups();
});

function makeRequest(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    if (method === 'POST' && typeof data === 'string') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            console.log('XHR state:', xhr.readyState, 'status:', xhr.status);
            if (xhr.status === 200) {
                try {
                    callback(JSON.parse(xhr.responseText));
                } catch (e) {
                    console.error('JSON parse error:', e, xhr.responseText);
                    alert('Error processing response');
                }
            } else {
                console.error('Request failed:', xhr.status, xhr.statusText);
                alert('Request failed: ' + xhr.statusText);
            }
        }
    };
    xhr.onerror = function () {
        console.error('Network error');
        alert('Network error occurred');
    };
    xhr.send(data);
}

function login() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    makeRequest('/teamchat/backend/auth/login', 'POST', `email=${email}&password=${password}`, function (response) {
        if (response.success) {
            window.location.href = response.user.role === 'admin' ? '/teamchat/frontend/admin_dashboard.php' : '/teamchat/frontend/dashboard.php';
        } else {
            alert(response.message);
        }
    });
}

function register() {
    console.log('Register function called');
    const formData = new FormData(document.getElementById('registerForm'));
    makeRequest('/teamchat/backend/auth/register', 'POST', formData, function (response) {
        console.log('Register response:', response);
        alert(response.message);
        if (response.success) {
            window.location.href = '/teamchat/frontend/login.php';
        }
    });
}

function testEndpoint() {
    makeRequest('/teamchat/backend/test', 'GET', null, function (response) {
        console.log('Test response:', response);
        alert(response.message);
    });
}

function loadProfile() {
    makeRequest('/teamchat/backend/profile/get', 'GET', null, function (response) {
        if (response.success) {
            const profile = response.data;
            document.getElementById('profileName').innerText = profile.name;
            document.getElementById('profileDesignation').innerText = profile.designation;
            document.getElementById('profileLocation').innerText = profile.location;
            document.getElementById('profileImage').src = '/teamchat/frontend/assets/images/' + profile.profileImage;
        }
    });
}

function loadUsersAndGroups() {
    // Load users
    makeRequest('/teamchat/backend/chat/get_users', 'GET', null, function (response) {
        if (response.success) {
            const userList = document.getElementById('userList');
            userList.innerHTML = '';
            response.data.forEach(user => {
                const li = document.createElement('li');
                li.className = 'user-item';
                li.innerHTML = `
                    <img src="/teamchat/frontend/assets/images/${user.profileImage}" alt="${user.name}">
                    ${user.name} <span class="status-${user.status}">[${user.status}]</span>
                `;
                li.onclick = () => selectChat(user.id, null, user.name);
                userList.appendChild(li);
            });
        }
    });

    // Load groups and their members
    makeRequest('/teamchat/backend/chat/get_groups', 'GET', null, function (response) {
        if (response.success) {
            const groupList = document.getElementById('groupList');
            groupList.innerHTML = '';
            response.data.forEach(group => {
                const li = document.createElement('li');
                li.className = 'group-item';
                li.innerHTML = `
                    <img src="/teamchat/frontend/assets/images/${group.group_image}" alt="${group.group_name}">
                    ${group.group_name}
                `;
                li.onclick = () => selectChat(null, group.id, group.group_name);
                groupList.appendChild(li);

                // Fetch group members
                makeRequest(`/teamchat/backend/chat/get_group_members?group_id=${group.id}`, 'GET', null, function (memberResponse) {
                    if (memberResponse.success) {
                        const memberUl = document.createElement('ul');
                        memberUl.className = 'group-members';
                        memberResponse.data.forEach(member => {
                            const memberLi = document.createElement('li');
                            memberLi.className = 'member-item';
                            memberLi.innerHTML = `
                                <img src="/teamchat/frontend/assets/images/${member.profileImage}" alt="${member.name}">
                                ${member.name} ${member.is_admin ? '(Admin)' : ''}
                            `;
                            memberLi.onclick = () => selectChat(member.id, null, member.name);
                            memberUl.appendChild(memberLi);
                        });
                        li.appendChild(memberUl);
                    }
                });
            });
        }
    });
}

function selectChat(receiverId, groupId, name) {
    currentReceiverId = receiverId;
    currentGroupId = groupId;
    document.getElementById('chatHeader').innerText = `Chatting with ${name}`;
    loadMessages();
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
    pollingInterval = setInterval(loadMessages, 5000); // Poll every 5 seconds
}

function sendMessage() {
    const messageText = document.getElementById('messageText').value;
    if (!messageText) return;
    makeRequest('/teamchat/backend/chat/send', 'POST', `receiver_id=${currentReceiverId}&group_id=${currentGroupId}&message_text=${messageText}`, function (response) {
        if (response.success) {
            loadMessages();
            document.getElementById('messageText').value = '';
        } else {
            alert(response.message);
        }
    });
}

function loadMessages() {
    makeRequest('/teamchat/backend/chat/get_messages', 'POST', `receiver_id=${currentReceiverId}&group_id=${currentGroupId}`, function (response) {
        if (response.success) {
            const chatBox = document.getElementById('chatBox');
            chatBox.innerHTML = '';
            response.data.forEach(msg => {
                const div = document.createElement('div');
                div.className = 'chat-message';
                const isSender = msg.sender_id === getCurrentUserId();
                let isGroupAdmin = false;
                if (msg.group_id) {
                    makeRequest(`/teamchat/backend/chat/check_group_admin?group_id=${msg.group_id}`, 'GET', null, function (adminResponse) {
                        if (adminResponse.success && adminResponse.is_admin) {
                            isGroupAdmin = true;
                        }
                        div.innerHTML = `
                            <span class="sender">${msg.sender_name}</span>: ${msg.message_text}
                            <span class="timestamp">${msg.sent_at}</span>
                            ${isSender || isGroupAdmin ? `
                                <button onclick="editMessage(${msg.id})">Edit</button>
                                <button onclick="deleteMessage(${msg.id})">Delete</button>
                            ` : ''}
                        `;
                        chatBox.appendChild(div);
                        chatBox.scrollTop = chatBox.scrollHeight;
                    });
                } else {
                    div.innerHTML = `
                        <span class="sender">${msg.sender_name}</span>: ${msg.message_text}
                        <span class="timestamp">${msg.sent_at}</span>
                        ${isSender ? `
                            <button onclick="editMessage(${msg.id})">Edit</button>
                            <button onclick="deleteMessage(${msg.id})">Delete</button>
                        ` : ''}
                    `;
                    chatBox.appendChild(div);
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            });
        }
    });
}

function editMessage(messageId) {
    const newText = prompt('Enter new message text:');
    if (newText) {
        makeRequest('/teamchat/backend/chat/edit_message', 'POST', `message_id=${messageId}&message_text=${newText}`, function (response) {
            if (response.success) {
                loadMessages();
                socket.emit('sendMessage', {
                    id: messageId,
                    sender_id: getCurrentUserId(),
                    receiver_id: currentReceiverId,
                    group_id: currentGroupId,
                    message_text: newText,
                    sender_name: 'You',
                    sent_at: new Date().toISOString()
                });
            } else {
                alert(response.message);
            }
        });
    }
}

function deleteMessage(messageId) {
    if (confirm('Are you sure you want to delete this message?')) {
        makeRequest('/teamchat/backend/chat/delete_message', 'POST', `message_id=${messageId}`, function (response) {
            if (response.success) {
                loadMessages();
                // Optionally notify other users of deletion via WebSocket
            } else {
                alert(response.message);
            }
        });
    }
}