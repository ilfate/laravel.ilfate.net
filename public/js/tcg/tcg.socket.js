/**
 * Created by Ilya Rubinchik (ilfate) on 12/09/14.
 */

TCG.Socket = function (game) {
    this.game = game;
    this.subscriptionKey;
    this.conn;

    this.initConnection = function(subscriptionKey) {
        info(subscriptionKey);
        this.subscriptionKey = subscriptionKey;
//        this.conn = new WebSocket('ws://localhost:8080');
//        this.conn.onopen = function(e) {
//            console.log("Connection established!");
//        };
//
//        this.conn.onmessage = function(e) {
//            console.log(e.data);
//        };
        var conn = new ab.Session('ws://localhost:8080',
            function() {
                conn.subscribe(this.subscriptionKey, function(topic, data) {
                    // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
                    //console.log('New article published to category "' + topic + '" : ' + data.title);
                    info(topic);
                    info(data);
                });
            },
            function() {
                console.warn('WebSocket connection closed');
            },
            {'skipSubprotocolCheck': true}
        );

        //conn.send('Hello World!');

    }

}