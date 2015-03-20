/**
 * Created by Ilya Rubinchik (ilfate) on 18/04/15.
 */

function rand(min, max)
{
    return Math.floor(Math.random()*(max-min+1)+min);
}
function info(data)
{
    console.info(data);
}
function debug(data) {
    //info(data);
    // desabled
}
function isInt(n){
    return typeof n== "number" && isFinite(n) && n%1===0;
}
function is_object(obj) {
    return typeof obj === 'object';
}

function Vortex () {

}
Vortex = new Vortex();

$(document).ready(function() {
    Vortex.Game = new Vortex.Game();


});

Vortex.Game = function () {

};