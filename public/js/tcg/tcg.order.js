/**
 * Created by Ilya Rubinchik (ilfate) on 12/09/14.
 */



TCG.Order = function (game) {
    this.game = game;

    this.init = function() {
        
    }

    this.createCard = function(card) {
        var template = $('#template-order-card').html();
        Mustache.parse(template);   // optional, speeds up future uses
        var rendered = Mustache.render(template, {card : card});
        var obj = $(rendered);
        $('.order').append(obj);
        obj.on({
            mouseenter : function(){ TCG.Game.units.mouseenter($(this)) },
            mouseleave : function(){ TCG.Game.units.mouseleave($(this)) },
            click : function(){ TCG.Game.event('unitClick', $(this)) }
        });
    }

    this.setCurrentCard = function(cardId) {
        $('.order .card').removeClass('focus');
        $('.order .card.id_' + cardId).addClass('focus');
    }

    this.removeCard = function(cardId) {
        $('.order .card.id_' + cardId).remove();
    }


}