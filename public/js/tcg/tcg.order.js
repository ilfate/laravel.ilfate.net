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
    }

    this.setCurrentCard = function(cardId) {
        $('.order .card').removeClass('focus');
        $('.order .card.id_' + cardId).addClass('focus');
    }

}