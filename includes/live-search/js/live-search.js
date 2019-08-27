(function($){
    "use strict"; // Start of use strict
    var $document   = $(document),
        $body       = $('body'),
        delay       = (function () {
            var timer = 0;
            return function (callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();

    $document.on( 'keyup','.ovic-live-search-form .txt-livesearch',function( e ) {
        var _this = $(this);
        delay(function () {
            var container       = _this.closest( '.ovic-live-search-form' ),
                list_products   = container.find( '.products-search .product-search' ),
                keyword         = _this.val(),
                product_cat     = _this.closest('.ovic-live-search-form').find('select[name="product_cat"]').val();

            if (typeof product_cat === "undefined" || product_cat == 0) {
                product_cat = '';
            }

            if( keyword.length < ovic_ajax_live_search.ovic_live_search_min_characters ) return false;

            var data = {
                action      :   'ovic_live_search',
                security    :   ovic_ajax_live_search.security,
                keyword     :   keyword,
                product_cat :   product_cat
            };

            container.addClass('loading');

            $.post(ovic_ajax_live_search.ajaxurl, data, function(response){
            container.removeClass('loading');
            container.find( '.suggestion-search-data' ).remove();
            container.find( '.live-search-overlay' ).remove();
            container.find( '.not-results-search' ).remove();
            container.find( '.products-search' ).remove();
            /*Prepare response.*/
            if ( response.message ) {
                container.find( '.results-search' ).append( '<div class="not-results-search">' + response.message + '</div><div class="live-search-overlay"></div>' );
            } else {
                container.find( '.results-search' ).append( '<div class="products-search"></div><div class="live-search-overlay"></div>' );
                /*Show suggestion.*/
                if ( response.suggestion ) {
                    container.find( '.results-search' ).append( '<div class="suggestion-search suggestion-search-data">' + response.suggestion + '</div>' );
                }
                /*Show results.*/
                $.each( response.list_product, function( key, value ) {
                    container.find( '.products-search' ).append( '<div class="product-search-item"><div class="product-image">' + value.image + '</div><div class="product-title-price"><div class="product-title"><a class="mask-link" href="' + value.url + '">' + value.title.replace( new RegExp( '(' + keyword + ')', 'ig' ), '<span class="keyword-current">$1</span>') + '</a></div><div class="product-price">' + value.price + '</div></div></div>' );
                } );
                container.find( '.products-search' ).append( '<div class="product-search view-all button">'+ovic_ajax_live_search.view_all_text+'</div>' );
            }
        });

        }, 1000);
    });

    $document.click(function(event) {
        var container = $(event.target).closest(".ovic-live-search-form")
        if ( container.length <= 0 ) {
            container.hide();
        }
    });

    $body.on( 'click', '.ovic-live-search-form .view-all', function() {
        $(this).closest( '.ovic-live-search-form ' ).submit();
    });

    $body.on( 'click', '.live-search-overlay', function() {
        var container = $(this).closest('.ovic-live-search-form');
        container.removeClass('loading');
        container.find('.suggestion-search-data').hide();
        container.find('.not-results-search').hide();
        container.find('.products-search').fadeOut();
        container.find('.live-search-overlay').hide();
    } );

})(jQuery); // End of use strict