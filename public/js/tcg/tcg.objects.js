/**
 * Created by Ilya Rubinchik (ilfate) on 12/09/14.
 */

TCG.Objects = function (game) {
    this.game = game;

    this.objects = [];

    this.init = function() {

    }

    this.setObject = function(obj) {
        var x = obj.data('x');
        var y = obj.data('y');
        //var active = obj.data('active');
        
        var xy = this.game.units.getCellPixels(x, y);
        x = xy[0];
        y = xy[1];
        
        obj.css({
            'left' : x,
            'top' : y
        });
    }

    this.createObject = function(object) {
        var templateCard = $('#template-field-object').html();
        Mustache.parse(templateCard);   // optional, speeds up future uses
        var rendered = Mustache.render(templateCard, {object : object});
        var obj = $(rendered);
        this.setObject(obj);
        $('.field .objects').append(obj);
        obj.on({
            click : function(){ TCG.Game.event('cellClick', $(this)) }
        });
    }

    this.getObject = function(cardId) {
        if (this.units[cardId] == undefined) {
            var unit = $('.field .unit.id_' + cardId);
            this.units[cardId] = unit;    
        } else {
            var unit = this.units[cardId];
        }
        
        if (unit.length != 0) {
            return unit;
        }
        info('There is no unit with Id ' + cardId);
        return false;
    }
}