$(function () {


    $('body').on('click', '.create-geo-profile' , function (event) {

        var userToken = $('.point-user-token').val();
        let type = 'PROFILE';

        if( userToken.length == 0 ){
            addPushNotification('error', 'Please select user!', 4000 );
            return false;
        }


        let valid = validationForm( type );
        if( valid.valid == false ){
            addPushNotification('error', valid.error_message , 6000 );
            return false;
        }

        startLoader();
        createEvent(userToken, type, valid.formData);




    });

})

