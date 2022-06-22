require([
    'jquery',
    'mage/url',
    'mage/translate',
], function ($,url,$t) {
    let citySelect = $('#account_city_id');
    let districtSelect = $('#account_district_id');
    let wardSelect = $('#account_ward_id')
    citySelect.on('change', function () {
        let cityIdSelected = this.value;
        let type = 'get_district';
        var option_default_ward = "<option value=''>" + $t('Please select ward') + "</option>";
        districtSelect.find('option').remove();
        wardSelect.find('option').remove();
        wardSelect.append(option_default_ward);
        $.ajax({
            url: url.build('/admin/affiliate/account/directory'),
            type: 'POST',
            dataType: 'json',
            data: {
                id: cityIdSelected,
                type: type
            },
            showLoader: true,
            success: function (response) {
                var dataReturn = JSON.parse(response);
                var total = dataReturn.length;
                if (total > 0) {
                    var option_default = "<option value=''>" + $t('Please select district') + "</option>";
                    districtSelect.append(option_default);
                    for (var i = 0; i < total; i++) {
                        var id = dataReturn[i].district_id;
                        var name = dataReturn[i].default_name;
                        var option = '<option value=' + id + '>' + name + '</option>';
                        districtSelect.append(option);
                    }
                }
            }

        });
    });

    districtSelect.on('change', function () {
        let districtIdSelected = this.value;
        let type = 'get_ward';
        $.ajax({
            url: url.build('/admin/affiliate/account/directory'),
            type: 'POST',
            dataType: 'json',
            data: {
                id: districtIdSelected,
                type: type
            },
            showLoader: true,
            success: function (response) {
                var dataReturn = JSON.parse(response);
                var total = dataReturn.length;
                if (total > 0) {
                    for (var i = 0; i < total; i++) {
                        var id = dataReturn[i].ward_id;
                        var name = dataReturn[i].default_name;
                        var option = '<option value=' + id + '>' + name + '</option>';
                        wardSelect.append(option);
                    }
                }
            }

        });
    });


});
