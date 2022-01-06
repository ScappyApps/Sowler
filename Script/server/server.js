var config = require('./app_start');
const express = require('express');
const path = require('path');

const fs = require('fs');

const app = express();
if (config.ssl == true) {
    const options = {
        key: fs.readFileSync(config.ssl_privatekey_full_path),
        cert: fs.readFileSync(config.ssl_cert_full_path),
    };
    var server = require('https').createServer(options, app);
} else {
    var server = require('http').createServer(app);
}
const io = require('socket.io')(server);

var mysql = require('mysql');
var cookie = require('cookie');
var _ = require('lodash');
var forEach = require('async-foreach').forEach;
var async = require('async');
var read = require('read-file');
var unixTime = require('unix-time');
var connection = require('./mysql/DB');
var functions = require('./functions');

var cookies = [];
var socketCount = 0;
var validUsers = {};
var clients = [];

app.use(express.static(path.join(__dirname, '../themes/default/layout')));
app.set('views', path.join(__dirname, '../themes/default/layout'));
app.engine('html', require('ejs').renderFile);
app.set('view engine', 'html');

app.use('/', (req, res) => {
    res.render('index.html');
});

try {
    server.listen(config.server_port, config.server_ip);
} catch (e) {
    console.log(e);
}

io.on('connection', Conexao);

function Conexao(socket) {
    socketCount++;
    try {

        var cookies = cookie.parse(socket.handshake.headers.cookie);
        if (_.isString(cookies.user_id)) {
            if (!_.includes(validUsers, cookies.user_id)) {
                connection.query(`SELECT user_id FROM so_users WHERE user_id = ? LIMIT 1`, [cookies.user_id], function (error, result, field) {
                    if (error) {
                        throw error;
                    }
                    console.log('Node.js is working correctly.');
                    if (result.length > 0) {
                        var clinetInfo = new Object;
                        validUsers[cookies.user_id] = result[0].user_id;
                        clinetInfo.clientId = socket.id;
                        clients.push(clinetInfo);

                        CheckNotifications(result[0].user_id);
                    }
                });
            }
        }
    } catch (e) {
        console.log(e);
    }

    if (!cookies.user_id) {
        return;
    }

    function CheckNotifications(user_id) {
        var time = Math.round(new Date().getTime() / 999.999995134);

        var notifications = 0;
        var messages = 0;

        connection.query(`UPDATE so_users SET lastseen = ? WHERE user_id = ?`, [time, user_id], function (error, result, field) {
            if (error) {
                throw error;
            }

            connection.query(`SELECT COUNT(*) as count FROM so_notifications WHERE to_id = ? AND seen = 0 LIMIT 100`, [user_id], function (error, result, field) {
                if (result.length > 0) {
                    var notifications = result[0].count;
                }

                connection.query(`SELECT COUNT(*) as count FROM so_messages WHERE to_id = ? AND seen = 0`, [user_id], function (error, result, field) {
                    if (error) {
                        console.log(error);
                        return;
                    }

                    if (result.length > 0) {
                        var messages = result[0].count;
                    }

                    emitChanges('resultNotifications', {
                        status: 200,
                        count_notifications: notifications,
                        count_messages: messages
                    });
                });

            });
        });

        /**connection.query(`SELECT user_id FROM so_users WHERE user_id = ? LIMIT 1`, [user_id], function (error, result, field) {
         if (error) {
         throw error;
         }
         
         if (result.length > 0) {
         var clinetInfo = new Object;
         validUsers[cookies.user_id] = result[0].user_id;
         clinetInfo.clientId = socket.id;
         clients.push(clinetInfo);
         
         CheckNotifications(result[0].user_id);
         }
         });**/
    }

    function emitChanges(emit, data) {
        io.to(socket.id).emit(emit, data);
    }

    socket.on('error', console.log);
    socket.on('disconnect', function (data) {
        socketCount--;
        for (var i = 0, len = clients.length; i < len; ++i) {
            var c = clients[i];

            if (c.clientId == socket.id) {
                clients.splice(i, 1);
                break;
            }
        }
    });

	socket.on('getNotifications', data => {
		CheckNotifications(data.user_id);
	});
	
    socket.on('sendMessage', data => {
        socket.broadcast.emit('receivedMessage', data);
    });

    socket.on('seenMessage', data => {
        socket.broadcast.emit('receivedMessageSeen', data);
    });

    socket.on('sendMessageLivestream', data => {
        socket.broadcast.emit('receivedMessageLivestream', data);
    });

}

server.listen(config.server_port);

