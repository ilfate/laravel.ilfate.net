/**
 * Created by Ilya Rubinchik (ilfate) on 12/09/14.
 */

TCG.Helpers = function (game) {
    this.game = game;




    this.createAuthor = function(author) {
        if ($('.author-' + author.id).length < 1) {
            var el = $('<p class="author-' + author.id + '">' + author.text + '</p>');
            $('.tcg-footer .authors').append(el);
        }
    }

    this.renderInfoCard = function(card) {
        if ($('.info-zone .card.card-id-' + card.cardId).length > 0) {
            return;
        }
        var templateInfo = $('#template-card').html();
        Mustache.parse(templateInfo);   // optional, speeds up future uses
        var renderedInfo = Mustache.render(templateInfo, {card : card, cardType : 'info-card'});
        $('.info-zone').append(renderedInfo);
    }

}