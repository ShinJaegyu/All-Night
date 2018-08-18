var express = require('express')
    , app = express()
    , http = require('http')
    , server = http.createServer(app)
    , io = require('socket.io').listen(server);

server.listen(8080);

// routing
app.get('/', function (req, res) {
    res.sendfile(__dirname + '/index.html');
});

// usernames which are currently connected to the chat
var usernames = {};

// rooms which are currently available in chat
var rooms = ['경제/경영','인문','역사','문화','정치','과학','공학','미술','음악','가정/생활','건강','여행','외국어','IT'];

io.sockets.on('connection', function (socket) {

    // when the client emits 'adduser', this listens and executes
    socket.on('adduser', function(username){
        // store the username in the socket session for this client
        socket.username = username;
        // store the room name in the socket session for this client
        socket.room = '경제/경영';
        // add the client's username to the global list
        usernames[username] = username;
        // send client to room 1
        socket.join('경제/경영');
        // echo to client they've connected
        socket.emit('updatechat', 'SERVER', '경제/경영 카테고리로 접속했습니다');
        // echo to room 1 that a person has connected to their room
        socket.broadcast.to('room1').emit('updatechat', 'SERVER', username + ' 이(가) 접속하셨습니다.');
        socket.emit('updaterooms', rooms, '경제/경영');
    });

    socket.on('sendchat', function (data) {
        // we tell the client to execute 'updatechat' with 2 parameters
        io.sockets.in(socket.room).emit('updatechat', socket.username, data);
    });

    socket.on('switchRoom', function(newroom){
        socket.leave(socket.room);
        socket.join(newroom);
        socket.emit('updatechat', 'SERVER', newroom+' 카테고리로 접속했습니다');
        // sent message to OLD room
        socket.broadcast.to(socket.room).emit('updatechat', 'SERVER', socket.username+'이(가) 퇴장하셨습니다.');
        // update socket session room title
        socket.room = newroom;
        socket.broadcast.to(newroom).emit('updatechat', 'SERVER', socket.username+'이(가) 퇴장하셨습니다.');
        socket.emit('updaterooms', rooms, newroom);
    });


    // when the user disconnects.. perform this
    socket.on('disconnect', function(){
        // remove the username from global usernames list
        delete usernames[socket.username];
        // update list of users in chat, client-side
        io.sockets.emit('updateusers', usernames);
        // echo globally that this client has left
        socket.broadcast.emit('updatechat', 'SERVER', socket.username + '이(가) 퇴장하셨습니다.');
        socket.leave(socket.room);
    });
});