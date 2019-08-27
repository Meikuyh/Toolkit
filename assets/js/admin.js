(function ($) {
    "use strict";
    function autocomplete() {
        $('.worldmart_taxonomy').each(function () {
            if( $(this).length > 0){
                $(this).chosen();
            }
        })
    }
    $(document).ready(function () {
        $(document).on('change', '.worldmart_select_preview', function () {
            var url = $(this).find(':selected').data('img');
            $(this).closest('.container-select_preview').find('.image-preview img').attr('src', url);
        });
        autocomplete();
    });
    $(document).ajaxComplete(function (event, xhr, settings) {
        autocomplete();
    });

})(jQuery);