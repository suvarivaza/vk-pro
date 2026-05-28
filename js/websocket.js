var ws = {
    socket: null,
    init: function()
    {
        ws.socket = new WebSocket("ws://vk-pro.top/ws:80");

        ws.socket.onopen = function(){ ws.opened(); };

        ws.socket.onclose = function() { ws.closed(); };

        ws.socket.onmessage = function(evt) { ws.message(evt) };
    },
    opened: function () {
        alert('opened');
    },
    closed: function()
    {
        alert('closed');
    },
    message: function (evt) {
        var data = evt.data;
        alert(data);
    },
    send: function(data)
    {
        ws.socket.send(data);
    }
};
$(document).ready(function(){
    ws.init();
});