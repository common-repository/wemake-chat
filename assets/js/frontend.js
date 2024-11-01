jQuery(function($){

    $(document).on("click", ".wmch-popup", function(e){
        e.stopPropagation();
    });

    $(document).on("click", ".wmch-popup-close", function(){
        $(".wmch-popup").removeClass("show");
    });

    $(document).on("click", ".wmch-popup-bt", function(e){
        e.stopPropagation();
        $(".wmch-popup").toggleClass("show");
    });

    let $popup_tooltip = $(".wmch-popup-tooltip");

    window.setTimeout(function(){
        $popup_tooltip.addClass("show");
        window.setTimeout(function(){
            $popup_tooltip.removeClass("show");
        }, 4000);
    }, 7000);

    $(document).on("click", "body", function(){
        $(".wmch-popup-close").trigger("click");
    })

});