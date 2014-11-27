/**
 * Created by Ilya Rubinchik (ilfate) on 12/09/14.
 */

TCG.Helpers = function (game) {
    this.game = game;
    this.messages = [];
    this.popover;

    this.addMessage = function(message, type) {
        this.messages.push({'message' : message, 'type' : type});
    }

    this.showMessages = function() {
        if (this.messages.length > 0) {
            var message = this.messages[0];
            this.createMessage(message.message, message.type)
            this.messages = this.messages.slice(1);
        }
    }

    this.getPopover = function(text, type) {
        //if (!this.popover) {
            var el = $('.field');
            el.popover({'content' : text, 'placement' : 'left'})
            el.popover('show')
            this.popover = el.next();
        //}
        return this.popover;
    }

    this.createMessage = function(text, type) {
//        var template = $('#template-message').html();
//        Mustache.parse(template);   // optional, speeds up future uses
//        var rendered = Mustache.render(template, {text : text, type : type});
//        var obj = $(rendered);
        var popover = this.getPopover(text, type);
        var top = parseInt(popover.css('top'));
        popover.css({top : top*2});
        popover.animate({'top' : top} , {duration:400});
        popover.delay(3000).animate({'top' : 50, 'opacity' : 0.7} , {duration:2000});
        popover.delay(10).animate({'top' : 0, 'opacity' : 0} , {duration:1000, 'complete': function () {
            $(this).css('opacity', 1).remove();
        }});

    }

}