/**
 * Created by Ilya Rubinchik (ilfate) on 12/09/14.
 */

TCG.Units = function (game) {
    this.game = game;
    this.cellHeight = 120;
    this.cellWidth = 120;
    this.animationsInQueue = [];
    this.units = [];

    this.init = function() {
        $('.field .unit').each(function() {
            TCG.Game.units.setUnit($(this));
        })
    }

    this.setUnit = function(obj) {
        var x = obj.data('x');
        var y = obj.data('y');
        //var active = obj.data('active');
        
        var xy = this.getCellPixels(x, y);
        x = xy[0];
        y = xy[1];
        
        obj.css({
            'left' : x,
            'top' : y
        })
        obj.data('active', 'true');
        
        //armor
        this.checkArmor(obj);
    }
    this.getCellPixels = function(x, y) {
        return [x * this.cellHeight + x * 3, y * this.cellHeight + y * 3]
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
        this.game.fieldCardInFocus = this.getUnitObj(cardId);
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
        var unit = this.getUnitObj(cardId);
        var oldX = unit.data('x');
        var oldY = unit.data('y');
        unit.removeClass('x_' + oldX + ' y_' + oldY);
        unit.addClass('x_' + x + ' y_' + y);
        unit.data('x', x);
        unit.data('y', y);
        //this.setUnit(unit);
        this.addAnimation(cardId, {'type' : 'move', 'x' : x, 'y' : y});
    }

    this.attack = function(cardId, targetId) {
        this.addAnimation(cardId, {'type' : 'attack', 'targetId' : targetId});
        var unit = this.getUnitObj(cardId);
        this.addAnimation(cardId, {'type' : 'move', 'x' : unit.data('x'), 'y' : unit.data('y')});
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
            //this.damageAnimation(unit, damage);
            this.addAnimation(cardId, {'type' : 'damage', 'damage' : damage});
            
        }
    }

    this.armor = function(cardId, armor, dArmor) {
        var unit = this.getUnitObj(cardId);
        unit.find('.armor .value').html(armor);
        this.checkArmor(unit);
        this.addAnimation(cardId, {'type' : 'armor', 'armor' : dArmor});
    }

    this.death = function(cardId) {
        var unit = this.getUnitObj(cardId);
        unit.addClass('dead');
        this.addAnimation(cardId, {'type' : 'death'});
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
    this.bounce = function(cardId) {
        this.addAnimation(cardId, {'type' : 'bounce'});
    }

    this.addAnimation = function(cardId, data) {
        if (this.animationsInQueue[cardId] == undefined) {
                this.animationsInQueue[cardId] = [];
            }
        this.animationsInQueue[cardId].push(data);
    }

    this.runAnimations = function() {
        if (!this.animationsInQueue.length) {
            return;
        }
        for (var cardId in this.animationsInQueue) {
            this.runSingleCardAnimations(cardId);
        
        }
        //this.animationsInQueue = [];
    }
    this.runSingleCardAnimations = function(cardId) {
        if (this.animationsInQueue[cardId].length) {
            var animation = this.animationsInQueue[cardId][0];
            switch (animation.type) {
                case 'damage':
                    this.damageAnimation(cardId, animation.damage);
                    break;
                case 'armor':
                    this.armorAnimation(cardId, animation.armor);
                    break;
                case 'move':
                    this.moveAnimation(cardId, animation.x, animation.y);
                    break;
                case 'attack':
                    this.attackAnimation(cardId, animation.targetId);
                    break;
                case 'death':
                    this.deathAnimation(cardId);
                    break;
                case 'bounce':
                    this.bounceAnimation(cardId);
                    break;
                    
            }

            this.animationsInQueue[cardId] = this.animationsInQueue[cardId].slice(1);
        }
    }

    this.damageAnimation = function(cardId, damage) {

        var unit = this.getUnitObj(cardId);
        var dmgObj = $('<div></div').addClass('damage');
        if (damage < 0) {
            dmgObj.addClass('heal');
            dmgObj.html('+' + (-damage + ''));    
        } else {
            dmgObj.html(-damage);
        }
        unit.prepend(dmgObj);
        dmgObj.animate({
            'opacity' : 0
        }, {
            duration:1500,
            'complete': (function (cardId) {
                return function() {
                    $(this).remove();
                    TCG.Game.units.runSingleCardAnimations(cardId);
                }
            })(cardId)
        });
        return dmgObj;
    }
    this.moveAnimation = function(cardId, x, y) {
        var unit = this.getUnitObj(cardId);
        var xy = this.getCellPixels(x, y);
        x = xy[0];
        y = xy[1];
        unit.animate({
                left : x,
                top : y
            }, {duration:600,
                'complete': (function (cardId) {
                    return function() {
                        TCG.Game.units.runSingleCardAnimations(cardId);
                    }
                })(cardId)
            });
    }
    this.attackAnimation = function(cardId, targetId) {
        var unit = this.getUnitObj(cardId);
        var target = this.getUnitObj(targetId);
        var xy = this.getCellPixels(unit.data('x'), unit.data('y'));
        x = xy[0];
        y = xy[1];
        var xy = this.getCellPixels(target.data('x'), target.data('y'));
        x2 = xy[0];
        y2 = xy[1];
        var dx = Math.round((x2 - x)/2);
        var dy = Math.round((y2 - y)/2);
        unit.animate({
            'left' : x + dx,
            'top' : y +dy,
        }, {duration:100, 
            'complete': (function (cardId) {
                    return function() {
                        TCG.Game.units.runSingleCardAnimations(cardId);
                    }
                })(cardId)
            });
    }
    this.deathAnimation = function(cardId) {
        var unit = this.getUnitObj(cardId);
        unit.animate({
            'opacity' : 0
        }, {
            duration:3000,
            'complete': (function (cardId) {
                    return function() {
                        $( this ).remove();
                        TCG.Game.units.runSingleCardAnimations(cardId);
                    }
                })(cardId)
        });
    }
    this.bounceAnimation = function(cardId) {
        var unit = this.getUnitObj(cardId);
        //unit.stop(true, true);
        var aX = unit.data('x');
        var aY = unit.data('y');
        var xy = this.getCellPixels(aX, aY);
        x = xy[0];
        y = xy[1];
        var n = 4;
        var intence = 3;
        var totalTime = 0;
        for(var i = 0; i <= n; i++) {
            var time = 80+i*5
            totalTime += time;
            var xRand = (rand(0, 1) === 1) ? 1 : -1;
            var yRand = (rand(0, 1) === 1) ? 1 : -1;
            unit.animate({
              // 'top':((i%2===0 ? y + (n-i)*intence : y - (n-i)*intence)+'px'),
              // 'left':((i%2===0 ? x + (n-i)*intence : x - (n-i)*intence)+'px')
              'top':((rand(0, 1) ? y + (n-i)*intence : y - (n-i)*intence)+'px'),
              'left':((rand(0, 1) ? x + (n-i)*intence : x - (n-i)*intence)+'px')
            }, time)
        }
        setTimeout(function() {
                TCG.Game.units.runSingleCardAnimations(cardId);
            }, totalTime);
    }

    this.armorAnimation = function(cardId, dArmor)
    {
        var dmgObj = this.damageAnimation(cardId, -dArmor);
        dmgObj.addClass('armor');
    }

    this.getUnitObj = function(cardId) {
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