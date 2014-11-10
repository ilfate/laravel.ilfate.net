/**
 * Created by Ilya Rubinchik (ilfate) on 12/09/14.
 */

TCG.Units = function (game) {
    this.game = game;
    this.cellHeight = 110;
    this.cellWidth = 110;

    this.init = function() {
        $('.field .unit').each(function() {
            TCG.Game.units.setUnit($(this));
        })
    }

    this.setUnit = function(obj) {
        var x = obj.data('x');
        var y = obj.data('y');
        obj.css({
            'left' : x * this.cellHeight + x * 3,
            'top' : y * this.cellHeight + y * 3
        })
    }

    this.deploy = function(playerId, card) {
        info(card);
        // remove card from hand
        $('.hand .card.id_' + card.id).remove();

        this.createUnit(card);
    }

    this.createUnit = function(card) {
        var template = $('#template-field-unit').html();
        Mustache.parse(template);   // optional, speeds up future uses
        var rendered = Mustache.render(template, {card : card, x : card.unit.x, y : card.unit.y});
        var obj = $(rendered);
        this.setUnit(obj);
        $('.field .units').append(obj);
    }
}