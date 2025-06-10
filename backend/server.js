const express = require('express');
const app = express();
const http = require('http').createServer(app);
const io = require('socket.io')(http);

io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);

    // Join a chat room (user-to-user or group)
    socket.on('joinChat', (data) => {
        const room = data.groupId ? `group_${data.groupId}` : `chat_${Math.min(data.senderId, data.receiverId)}_${Math.max(data.senderId, data.receiverId)}`;
        socket.join(room);
        console.log(`User ${socket.id} joined ${room}`);
    });

    // Handle new message
    socket.on('sendMessage', (data) => {
        const room = data.groupId ? `group_${data.groupId}` : `chat_${Math.min(data.senderId, data.receiverId)}_${Math.max(data.senderId, data.receiverId)}`;
        io.to(room).emit('newMessage', data);
    });

    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
    });
});

http.listen(3000, () => {
    console.log('WebSocket server running on port 3000');
});