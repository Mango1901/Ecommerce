define([
    'Magento_Checkout/js/model/step-navigator'
], function(stepNavigator){
    return function(Component){
        return Component.extend({
            isFullMode: function(){
                if (!this.getTotals()) {
                    return false;
                }

                return true;
            }
        })
    }
});
