(function ($) {
    "use strict"; // Start of use strict
    /* ---------------------------------------------
     Custom variations
     --------------------------------------------- */
    function variations_custom() {
        $('.variations').find('.data-val').html('');
        $('select[data-attributetype="color"],select[data-attributetype="photo"]').each(function () {
            var $this = $(this);
            $this.hide();
            $this.find('option').each(function () {
                var _this       = $(this),
                    _ID         = _this.parent().attr('id'),
                    _data       = _this.data(_ID),
                    _value      = _this.attr('value'),
                    _data_type  = _this.data('type'),
                    width       = 30,
                    height      = 30;

                if (_value !== '') {
                    if (_data_type == 'color' || _data_type == 'photo' || _data_type == 'label') {
                        $this.parent().find('.data-val').show();
                        var itemclass = 'change-value type-display-'+_data_type;
                        if( _this.is(':selected')){
                            itemclass += ' selected';
                        }
                        if( _data_type == 'label'){
                            $this.parent().find('.data-val').append('<a class="'+itemclass+'" href="#"  data-value="' + _value + '"><span style="display: inline-block;">'+_value+'</span></a>');
                        }else{
                            $this.parent().find('.data-val').append('<a class="'+itemclass+'" href="#"  data-value="' + _value + '"><span style="background: ' + _data + '; background-size: cover; min-width:'+ width +'px; height:'+ height +'px; display: inline-block; font-size: 0;">'+_value+'</span></a>');
                        }
                    }
                }
            });
            $this.closest('table.variations').addClass('kutetheme-atts-swatches');
        });
    };

    $(document).ready(function() { 
        variations_custom();
    })
    .on('qv_loader_stop', function(){
        variations_custom();
    })
    .on('click','.reset_variations',function () { 
        variations_custom();
    })
    .on('change','.variations_form select',function () {
        variations_custom();
    })
    .on('click','.change-value',function(){
        var _this   = $(this),
            _change = _this.data('value');
        _this.parent().parent().children('select').val(_change).trigger('change');
        _this.closest('.data-val').find('.change-value').removeClass('selected');
        _this.addClass('selected');
        return false;
    });
})
(jQuery); // End of use strict