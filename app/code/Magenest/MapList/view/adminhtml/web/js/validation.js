require([
        'jquery',
        'mage/translate',
        'jquery/validate'
    ],
    function($){
        $.validator.addMethod(
            'validate-opening-hours', function (v) {
                //allows blank hours (day off)
                if(v.length === 0) return true;

                var removeSpace = v.replace(' ','');
                var arrHours = removeSpace.split('-');

                if(arrHours.length>1 && arrHours.length<3){
                    var openHours = arrHours[0];
                    var closeHours = arrHours[1];
                    if(checkHours(openHours) && checkHours(closeHours) && checkCloseHours(openHours,closeHours)){
                        return true;
                    }else{
                        return false;
                    }
                }
                function checkCloseHours(openHours,closeHours) {
                    var openTime = parseInt(openHours.split(':')[0],10);
                    var closeTime = parseInt(closeHours.split(':')[0],10);
                    if(openTime<closeTime){
                        return true;
                    }else{
                        return false;
                    }
                }
                function checkHours(v) {
                    if(v == "" || v.indexOf(":")<0){
                        return false;
                    }else{
                        var sHours = v.split(':')[0];
                        var sMinutes = v.split(':')[1];
                        if(sHours == "" || isNaN(sHours) || parseInt(sHours)>23)
                        {
                            return false;
                        }
                        else if(parseInt(sHours) == 0)
                            sHours = "00";
                        else if (sHours <10)
                            sHours = "0"+sHours;

                        if(sMinutes == "" || isNaN(sMinutes) || parseInt(sMinutes)>59)
                        {
                            return false;
                        }
                        else if(parseInt(sMinutes) == 0)
                            sMinutes = "00";
                        else if (sMinutes <10)
                            sMinutes = "0"+sMinutes;
                        v = sHours + ":" + sMinutes;
                    }
                    return true;
                }
            }, $.mage.__('Please enter the correct field format!'));
    }
);