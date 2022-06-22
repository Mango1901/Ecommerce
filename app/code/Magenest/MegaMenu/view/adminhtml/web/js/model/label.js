define([
        'uiClass',
        'jquery',
    ],
    function (Element, $) {
        var _isLoaded = false,
            _labels = [];
        return Element.extend({
            defaults: {
                labels: []
            },
            initialize: function () {
                this._super();
                if (!_isLoaded) {
                    this.setLabels(this.labels);
                    this.setIsLoaded(true);
                }
            },
            setLabels: function (labels) {
                _labels = labels;
            },
            getLabels: function () {
                return _labels;
            },
            fetchLabels: function () {
            },
            setIsLoaded: function (isLoaded) {
                _isLoaded = isLoaded;
            }
        });
    }
);