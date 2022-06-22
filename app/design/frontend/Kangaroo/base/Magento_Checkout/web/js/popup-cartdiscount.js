require(["jquery", "Magento_Ui/js/modal/modal"], function($, modal) {
    var options = {
        type: 'popup',
        responsive: true,
        title: 'Chọn voucher',
        buttons: [{
            class: '',
            click: function () {
                this.closeModal();
            }
        }]
    };

    var popup = modal(options, $('#modal'));
    $("#buttonClick").click(function() {
        $('#modal').modal('openModal');
    });
});
