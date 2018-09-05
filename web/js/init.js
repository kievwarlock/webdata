$(function () {


    /**** pjax:success ****/

    $(document).on('pjax:success', function() {
        if( $('.sort-table').length > 0 ){
            $('.sort-table').DataTable( {
                "order": [[ 0, "desc" ]]
            } );
        }
    });

    /**** END pjax:success ****/



    $('#menu-init').on('click', function () {
        $(this).toggleClass('active');
        $('.main-sidebar').toggleClass('active');
    })


    if( $('.sort-table').length > 0 ){
        $('.sort-table').DataTable( {
            "order": [[ 0, "desc" ]]
        } );
    }







    $('body').on('click', '.view-point-data', function(){

        startLoader();

        try {

            $('#view-event .modal-content').html('Loading...');
            let pointId = $(this).data('point');
            let userToken = $(this).data('token');
            let pointType = $(this).data('type');

            $.ajax({
                url: '/point/view-item/',
                type: 'POST',
                data: {
                    owner_user_token: userToken,
                    point_id: pointId,
                    point_type: pointType,
                },
                success: function(res){

                    endLoader();

                    if( res ){

                        $('#view-event').modal('show');
                        setTimeout(function () {
                            $('#view-event .modal-content').html(res);
                            addPushNotification('success', 'Geo point loaded success!',2000 );
                        }, 200)


                    }

                },
                error: function(){

                    endLoader();
                    addPushNotification('error', 'Geo point not loaded! Please try again!',3500 );

                }
            });

        } catch (error) {
            endLoader();
            addPushNotification('error', 'Code error! Please try again! Error: ' + error,3500 );
        } finally {

        }








    })



    function loadProfile( userId, userToken, userPhone ){



        $.ajax({
            url: '/user/view/',
            type: 'POST',
            data: {
                user_id: userId,
                user_token: userToken,
                user_phone: userPhone,
            },
            success: function(res){

                if( res ){
                    $('#edit-user-profile .modal-content').html(res);
                    $('#edit-user-profile').modal('show');

                }else{
                    addPushNotification('error', 'Error, try again!', 3000 );
                }

            },
            error: function(){
                addPushNotification('error', 'Error, try again!', 3000 );
                $('#edit-user-profile .modal-content').html('Error!');
                $('#edit-user-profile').modal('hide');
            }
        });
    }


    $('body').on('click', '.edit-user-profile', function(){


        let userId = $(this).data('id');
        let userToken = $(this).data('token');
        let userPhone = $(this).data('phone');

        $('#edit-user-profile .modal-content').html('Loading...');

        loadProfile(userId, userToken, userPhone);

    })



    $('.pjax-container').on('pjax:start',   function() {
        startLoader();
    });

    $('.pjax-container').on('pjax:end',   function() {
        endLoader();
        addPushNotification('success', 'Creating user success!', 2000 );
        $('.new-user.edit-user-profile').trigger('click');
    });





    $('body').on('click', '.generateUser', function(){

        startLoader();
        addPushNotification('', 'Creating user, wait please...', 2000 );

       /* $('.success-form').hide();
        $('.error-form').hide();
        console.log('ADD USER!');
        $('.window-description').html('Creating user, wait please...');*/

        $.ajax({
            url: '/site/add/',
            type: 'POST',
            data: '',
            success: function(res){

                endLoader();

                if( res ){


                    $('.content-window-main').html(res);
                    if( $('.sort-table').length > 0 ){
                        $('.sort-table').DataTable( {
                            "order": [[ 1, "desc" ]]
                        } );
                    }

                    let userPhone = $('.new-user').data('phone');


                    // Создаётся объект promise
                    let promise = new Promise((resolve, reject) => {

                        $.ajax({
                            url: '/site/edit_user_profile/',
                            type: 'POST',
                            data: {
                                user_phone: userPhone,
                            },
                            success: function(res){
                                //console.log(res);
                                if( res ){
                                    $('#edit-user-profile .modal-content').html(res);
                                    $('#edit-user-profile').modal('show');

                                    resolve( 'true' );
                                }

                            },
                            error: function(){
                                //alert('Error!');
                                $('#edit-user-profile .modal-content').html('Error!');
                                reject( 'false' );
                            }
                        });


                    });

                    promise
                        .then(
                            function (e) {
                                $('.success-form').show();
                            },
                            function (err) {
                                $('.error-form').show();
                            }

                        );


                }else{
                    $('.error-form').show();
                }

            },
            error: function(){
                $('.error-form').show();
                //alert('Error!');
            }
        });
        return false;

    });





    function updateProfile( blob = false) {


        var formData = new FormData( $('.save-user-profile')[0] );

        if( blob instanceof Blob){
            formData.append('image', blob );
        }


        $.ajax({
            url: '/user/update',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){

                var showSuccessModal = false;
                var showDangerModal = false;
                $('.save-user-profile-status.bg-success').text('');
                $('.save-user-profile-status.bg-danger').text('');

                if( res === false ){
                    showDangerModal = true;
                    $('.save-user-profile-status.bg-danger').text('No data changes!');
                }else{
                    var data = JSON.parse(res);


                    console.log('DATA:',data);
                    if( typeof(data.status_avatar) != "undefined" && data.status_avatar !== null ){
                        if( data.status_avatar == true ){
                            showSuccessModal = true;
                            $('.save-user-profile-status.bg-success').text( $('.save-user-profile-status.bg-success').text() + 'Avatar updated successful! ')
                        }else{
                            showDangerModal = true;
                            $('.save-user-profile-status.bg-danger').text( $('.save-user-profile-status.bg-danger').text() + 'Avatar not updated! Error:' + data.error_avatar);
                        }
                    }
                    if( typeof(data.status_fields) != "undefined" && data.status_fields !== null ) {
                        if (data.status_fields == true) {
                            showSuccessModal = true;
                            $('.save-user-profile-status.bg-success').text($('.save-user-profile-status.bg-success').text() + ' Profile updated successful!')
                        } else {
                            showDangerModal = true;
                            $('.save-user-profile-status.bg-danger').text($('.save-user-profile-status.bg-danger').text() + 'Profile not updated! Error:' + data.error_fields);
                        }
                    }
                }

                if( showSuccessModal == true){
                    $('.save-user-profile-status.bg-success').show();
                    /*setTimeout(function () {
                        $('#edit-user-profile').modal('hide');
                    }, 1500 );*/
                }
                if( showDangerModal == true){
                    $('.save-user-profile-status.bg-danger').show();
                }

            },
            error: function(){
                alert('Error!');
            }

        });

        return false;

    };

    function getBlob( croppieItem ) {

        if (croppieItem.length > 0) {

            croppieItem.croppie('result', {
                type: 'blob',
                size: {width: 1024, height: 1024},
                format:'jpeg',
            }).then( function ( blob ) {

                updateProfile(blob);

            });
        }


    };

    $('body').on('submit', '.save-user-profile', function (event) {

        event.preventDefault();
        $('.save-user-profile-status').hide();

        var croppieItem = $(this).find('.upload-images-item-crop.croppie-container');

        if( croppieItem.length > 0 ){

            getBlob( croppieItem );
        }else{
            updateProfile();
        }


    });





})