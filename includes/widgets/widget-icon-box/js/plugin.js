jQuery(document).ready( function($) {
    jQuery(document).on('click','.frm_input .btn_save', function (e) {
    // jQuery('.frm_input .btn_save').click(function (e) {
        e.preventDefault();
        var obj = jQuery(this);
        var input_data = obj.parent().find('input.input_content');
        var data = [];
        if(input_data.val() != ''){
            data = JSON.parse(input_data.val());
        }
        var temp_obj = {id:0,font_icon:'', title:'', sub_title:'', desc:'',url:''};
        temp_obj.font_icon = obj.parent().find('input[name="font_icon"]').val();
        temp_obj.title = obj.parent().find('input[name="title"]').val();
        temp_obj.sub_title = obj.parent().find('input[name="sub_title"]').val();
        temp_obj.desc = obj.parent().find('textarea[name="desc"]').val();
        temp_obj.url = obj.parent().find('input[name="url"]').val();
        temp_obj.id = get_id(data);
        data.push(temp_obj);
        input_data.val(JSON.stringify(data));
        rend_new_item(temp_obj);
    });
    jQuery(document).on('click','.frm_input .btn_clear', function (e) {
    // jQuery('.frm_input .btn_clear').click(function (e) {
        e.preventDefault();
        var obj = jQuery(this);
        obj.parent().find('input[name="font_icon"]').val('');
        obj.parent().find('input[name="title"]').val('');
        obj.parent().find('input[name="sub_title"]').val('');
        obj.parent().find('textarea[name="desc"]').val('');
        obj.parent().find('input[name="url"]').val('');
    });

    jQuery(document).on('click','.result .btn_delete', function (e) {
        e.preventDefault();
        var input_data = jQuery(this).closest('div.widget-content').find('p.frm_input input.input_content');
        var id = jQuery(this).data('id');
        var data = [];
        if(input_data.val() != ''){
            data = JSON.parse(input_data.val());
        }
        data = data.filter(function(item) {
            if(item.id != id){
                return item;
            }
        });
        input_data.val(JSON.stringify(data));
        jQuery(this).parent().remove();
    });

    function get_id(data) {
        if(data.length == 0){
            return 0;
        }else {
            var max = data[0].id;
            for (var i = 0; i < data.length; i++){
                if(data[i].id > max){
                    max = data[i].id;
                }
            }
            return ++max;
        }
        return 10;
    }

    function rend_new_item(obj) {
        var html ='<p class="item-box">';
            html +='<label >Font icon class:</label>';
            html +='<input class="txt_info" value="'+obj.font_icon+'" name="_font_icon" disabled>';
            html +='<label >Title:</label>';
            html +='<input class="txt_info" value="'+obj.title+'"  name="_title" disabled>';
            html +='<label >Subtitle:</label>';
            html +='<input class="txt_info" type="text" value="'+obj.sub_title+'"  name="_sub_title" disabled>';
            html +='<label >Describer:</label>';
            html +='<textarea class="txt_info" name="_desc" disabled>'+obj.desc+'</textarea>';
            html +='<label >Link to page:</label>';
            html +='<input class="txt_info "  name="_url" value="'+obj.url+'" disabled />';
            html +='<a  class="button btn_delete" data-id="'+obj.id+'" style="display: inline-block; float: left; text-align: center; font-size: 14px;">Delete</a>';
            html +='</p>';
            jQuery('div.result').append(html);
    }
});