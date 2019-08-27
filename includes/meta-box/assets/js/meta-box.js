(function($){
    $('document').ready(function() {
        var $parent_container = [];
        jQuery( '*[data-dependency="on"]' ).each(function(){
            var $this = jQuery( this );
            var $parent_id = $this.data( 'did' );
            $parent = jQuery( '*[name="' + $parent_id +'"]' );
            if($this.data( 'dependency' ) != 'on'){
                dependency( $this, $parent);
            }else{
                mul_dependency( $this, $parent);
            }

            if( jQuery.inArray( $parent_id, $parent_container ) == -1 ){
                $parent.change( function() {
                    var $e_parent = jQuery( this );
                    jQuery( '*[data-did="' + $e_parent.attr( 'name' ) + '"]' ).each(function(){
                        var $this    = jQuery(this);
                        if($this.data( 'dependency' ) != 'on'){
                            dependency( $this, $e_parent);
                        }else{
                            mul_dependency( $this, $e_parent);
                        }
                    });
                });
                $parent_container.push($parent_id);
            }
        });

    });
    function mul_dependency( $this, $parent ){
        //Check child existence
        $childen = jQuery( '*[data-did="' + $this.attr( 'name' ) + '"]' );
        if( $childen.length < 1 && $parent.data( 'dependency' ) != 'on' ){
            dependency($this, $parent);
        }else{
            $childen.each(function () {
                var $child = jQuery(this);
                var $row_c = $child.closest( '.rwmb-field' );
                $row_c.slideUp();
            });
            if( $childen.length > 0 || $parent.hasClass('active')){
                dependency($this, $parent);
            }
        }
    }
    function dependency( $this, $parent ){
        var equalval = $this.data( 'dval' ).toString().split(',');
        var operator = $this.data( 'operator' );
        var $row = $this.closest( '.rwmb-field' );

        if( $parent.attr( 'type' ) == 'radio' ) {
            var parent_val = jQuery( '*[name="' + $this.data( 'did' ) +'"]:checked' ).val();
        }else {
            var parent_val = jQuery( '*[name="' + $this.data( 'did' ) +'"]' ).val();
        }
        if( operator == 'equal' ){
            if( jQuery.inArray( parent_val, equalval ) != -1 ){
                $row.slideDown();
                $this.addClass('active');
                $this.removeClass('close');
            }else{
                $row.slideUp();
                $this.removeClass('active');
                $this.addClass('close');
            }
        }else{
            if( jQuery.inArray( parent_val, equalval ) == -1 ){
                $row.slideDown();
                $this.addClass('active');
                $this.removeClass('close');
            }else{
                $row.slideUp();
                $this.removeClass('active');
                $this.addClass('close');
            }
        }
        $this.trigger( "change" );
    }
})(jQuery);