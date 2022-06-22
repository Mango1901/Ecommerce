define([
    'jquery',
    'underscore',
    'uiRegistry',
    'uiComponent',
    'highcharts',
    'uiRegistry'
], function ($,_, uiRegistry,Component,Highcharts,registry) {
    return Component.extend({
        defaults: {
            template:{
                name:"Magenest_NotificationBox/report",
            },
            allSubscribers: 0,
            allUnSubscribers: 0
        },
        initObservable: function () {
            this._super()
                .observe([
                    'allSubscribers',
                    'allUnSubscribers'
                ]);
            return this;
        },
        initialize: function () {
            this._super();
            var self = this;
            self.allSubscribers(self.totalSubscribers);
            self.allUnSubscribers(self.totalUnSubscribers);
            self.getReport(self.to,self.from);
            $('#submit').on('click',function (){
                var from = registry.get('from-date').value(),
                    to = registry.get('to-date').value();

                if(from === '' || to === ''){
                    alert('Please enter all fields');
                }else{
                    if(from > to){
                        alert('From must be less than or equal To');
                    }else{
                        self.getReport(from,to);
                    }
                }
            });
            $('#reset').on('click',function (){
                $('#from-date > input').val('')
                $('#to-date > input').val('')
                self.getReport(self.to,self.from);
            });
        },

        getReport:function (from,to){
            var self = this;
            var data = [];
            if(from !== "" && to !==""){
                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    showLoader: true,
                    url: this.url,
                    data: {
                        from : from,
                        to  : to
                    },
                    success: $.proxy(function (response) {
                        data[0] = ['Day','Subscribers'];
                        $.each(response, function( index, Day ) {
                            var day = Day.day.split('-');
                            day = day['1']+'/'+day[2];
                            data[index+1] =[day,Day.total];
                        });
                        self.drawChartColumn(data);
                    })
                });
            }
        },
        /**
         * init google chart and call function draw
         */
        drawChartColumn: function (response) {
            var self = this;
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(function () {
                self.drowSubscribers(response);
            });
        },

        /**
         * data for pie chart opened email rate
         */
        drowSubscribers: function (response) {
            var element = document.getElementById('container-register');
            var data = google.visualization.arrayToDataTable(response);

            var options = {
                title: 'Line chart show how many subscribers on your site',
                curveType: 'function',
                legend: { position: 'bottom' },
                width:'100%',
                height:'100%'
            };
            var chart = new google.visualization.LineChart(element);

            chart.draw(data, options);
            },
    });
});
