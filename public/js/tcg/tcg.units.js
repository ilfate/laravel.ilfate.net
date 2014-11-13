/**
 * Created by Ilya Rubinchik (ilfate) on 12/09/14.
 */

TCG.Units = function (game) {
    this.game = game;
    this.cellHeight = 110;
    this.cellWidth = 110;
    this.animationsInQueue = [];

    this.init = function() {
        $('.field .unit').each(function() {
            TCG.Game.units.setUnit($(this));
        })
    }

    this.setUnit = function(obj) {
        var x = obj.data('x');
        var y = obj.data('y');
        var active = obj.data('active');
        x = x * this.cellHeight + x * 3;
        y= y * this.cellHeight + y * 3;
        if (active == false) {
            obj.css({
                'left' : x,
                'top' : y
            })
            obj.data('active', 'true');
        } else {
            obj.animate({
                left : x,
                top : y
            }, 600);
        }
        //armor
        this.checkArmor(obj);
    }

    this.checkArmor = function(unit) {
        var armor = parseInt(unit.find('.armor .value').html())
        if (armor <= 0 || !armor) {
            unit.find('.armor').hide();
        } else {
            unit.find('.armor').show();
        }
    }

    this.deploy = function(playerId, card) {
        this.createUnit(card);
    }

    this.focusUnit = function(cardId)
    {
        this.game.fieldCardInFocus = $('.field .unit.id_' + cardId);
        this.game.fieldCardInFocus.addClass('focus');
    }
    this.removeFocus = function() {
        $('.field .unit').removeClass('focus');
        $('.field .cell').removeClass('focus');
    }

    this.createUnit = function(card) {
        var templateCard = $('#template-field-unit').html();
        Mustache.parse(templateCard);   // optional, speeds up future uses
        var rendered = Mustache.render(templateCard, {card : card, x : card.unit.x, y : card.unit.y});
        var obj = $(rendered);
        this.setUnit(obj);
        var templateInfo = $('#template-info-card').html();
        Mustache.parse(templateInfo);   // optional, speeds up future uses
        var renderedInfo = Mustache.render(templateInfo, {card : card});
        obj.find('.info').popover({
            'template' : renderedInfo
        });
        $('.field .units').append(obj);
    }

    this.move = function(cardId, x ,y) {
        var unit = $('.field .unit.id_' + cardId);
        var oldX = unit.data('x');
        var oldY = unit.data('y');
        unit.removeClass('x_' + oldX + ' y_' + oldY);
        unit.addClass('x_' + x + ' y_' + y);
        unit.data('x', x);
        unit.data('y', y);
        this.setUnit(unit);
    }

    this.attack = function(cardId, targetId) {
        var unit = this.getUnitObj(cardId);
        var target = this.getUnitObj(targetId);
        var x = unit.data('x') * this.cellWidth;
        var y = unit.data('y') * this.cellHeight;
        var x2 = target.data('x') * this.cellWidth;;
        var y2 = target.data('y') * this.cellHeight;;
        var dx = Math.round((x2 - x)/2);
        var dy = Math.round((y2 - y)/2);
        unit.animate({
            'left' : x + dx,
            'top' : y +dy,
        }, 500, function(el) {
            TCG.Game.units.setUnit($(this));
        });
    }

    this.damage = function(cardId, health, damage) {
        var unit = this.getUnitObj(cardId);
        var healthObj = unit.find('.health .value');
        var currentHealth = parseInt(healthObj.html());
        if (currentHealth - damage != health) {
            info ('wtf damage is wrong');
            return;
        }
        healthObj.html(health);
        if (damage != 0) {
            this.damageAnimation(unit, damage);
        }
    }

    this.armor = function(cardId, armor, dArmor) {
        var unit = this.getUnitObj(cardId);
        unit.find('.armor .value').html(armor);
        this.checkArmor(unit);
        this.armorAnimation(unit, dArmor);
    }

    this.death = function(cardId) {
        var unit = this.getUnitObj(cardId);
        unit.addClass('dead');
        unit.animate({
            'opacity' : 0
        }, {
            duration:3000,
            'complete': function(el) {
                $( this ).remove();
            }
        });
    }

    this.change = function(cardId, dataType, data) {
        var keywordsObj = this.getUnitObj(cardId).find('.keywords');
        switch (dataType) {
            case 'keyword':
                keywordsObj.html('');
                keywordsString = '';
                for (var key in data.words) {
                    var word = data.words[key];
                    if (data['word'] !== undefined) {
                        word += ' ' + data['word'];
                    }
                    keywordsString += '<span class="keyword">' + word + '</span> ';
                }
                keywordsObj.html(keywordsString);
                break;
        }
    }

    this.damageAnimation = function(unit, damage) {
        var dmgObj = $('<div></div').addClass('damage');
        if (damage < 0) {
            dmgObj.addClass('heal');
            dmgObj.html('+' + (-damage + ''));    
        } else {
            dmgObj.html(-damage);
        }
        unit.prepend(dmgObj);
        var queue = 'damage-unit-' + unit.data('id');
        dmgObj.animate({
            'opacity' : 0
        }, {
            duration:3000,
            'queue': queue,
            'complete': function(el) {
                $( this ).remove();
            }
        });
        dmgObj.dequeue(queue);
        return dmgObj;
    }
    this.armorAnimation = function(unit, dArmor)
    {
        var dmgObj = this.damageAnimation(unit, -dArmor);
        dmgObj.addClass('armor');
    }

    this.getUnitObj = function(cardId) {
        var unit = $('.field .unit.id_' + cardId);
        if (unit.length != 0) {
            return unit;
        }
        info('There is no unit with Id ' + cardId);
        return false;
    }
}