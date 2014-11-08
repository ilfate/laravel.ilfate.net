

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
}