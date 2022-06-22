/**
 * Created by hiennq on 10/13/16.
 */
var selected=[];
console.log(productList);
require([
    'jquery'
], function($){
    $(document).ajaxStart(function() {
        checkProductCheckbox();
        $.ajax({
            type: "POST",
            url: 'http://magento2.local/admin/maplist/location/grid/id/',
            data: {
                'selected_products':JSON.stringify(selected)
            },
            success: function () {

            },
            dataType: 'text',
            //async: false
        });
    });
    function checkProductCheckbox(){
        var id, status;
        $('#maplistProductGrid_table').find('tbody').find('input[type="checkbox"]').each(function(){
            id = $(this).attr('id');
            id = id.replace('id_',"");
            status = $(this).is(":checked");
            if(status) {
                selected.push(id);
            }
        });
    }
});

