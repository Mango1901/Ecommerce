define(
    ['ko'],
    function (ko) {
        'use strict';
        var pointsSpend = ko.observable(window.checkoutConfig.checkoutRewardsPointsSpend);

        return {
            pointsSpend: pointsSpend,
            getPointsSpend: function () {
                return pointsSpend;
            },
            setPointsSpend: function (pointsSpendData) {
                pointsSpend(pointsSpendData);
            }
        };
    }
);
