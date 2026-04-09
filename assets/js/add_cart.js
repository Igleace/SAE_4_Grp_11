(function($) {
    $('.addCart').click(function(event){
        event.preventDefault();
        $.get($(this).attr('href'), {}, function(data){
            if(data.error){
                alert(data.message);
            }else{
                //alert(data.message); pop-up pour confirmer que l'article a bien été ajouté au panier
                $('#total').empty().append(data.total);
                $('#count').empty().append(data.count);
            }
        },'json');
    return false;
    });

})(jQuery);
