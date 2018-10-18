$(function () {



    $('body').on('click', '.import-all-file', function () {

        var importItems = $('.item-import');

        var counterImport = 0;


        if( importItems.length > 0 ){

            startLoader();

            importItems.each( function (e) {

                var formData = new FormData();
                var currentItem = $(this);
                currentItem.find('.import-data-item').each(function () {

                    var dataKey = $(this).data('name');
                    var dataValue = $(this).data('value');

                    if( dataKey == 'base' ){
                        formData.append(  dataKey, JSON.stringify(dataValue)  );
                    }else{
                        formData.append(  dataKey, dataValue );
                    }

                });




                try {


                    $.ajax({
                        url: '/import/init',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res){

                            counterImport++;

                            if( importItems.length == counterImport ){
                                endLoader();
                                addPushNotification('', 'Import END', 5000 );
                            }

                            if( res === false ){
                                addPushNotification('error', 'No data !', 3000 );
                            }else{




                                try {

                                    var data = JSON.parse(res);
                                    //console.log(data);

                                    if(  data['user'] ){
                                        if(  data['user'] && data['user'] == true ){

                                            currentItem.find('.import-status').append('<p class="alert alert-success">User create success</p>');
                                            addPushNotification('success', 'User create success!', 2000 );

                                        }else{
                                            currentItem.find('.import-status').append('<p class="alert alert-danger">User create ERROR</p>');
                                            addPushNotification('error', 'User create Error:', 5000 );
                                        }
                                    }

                                    if( data['profile'] ){
                                        if ( data['profile'] == true) {

                                            currentItem.find('.import-status').append('<p class="alert alert-success">Profile updated success</p>');
                                            addPushNotification('success', 'Profile updated successful!', 2300 );

                                        } else {

                                            currentItem.find('.import-status').append('<p class="alert alert-danger">Profile updated ERROR</p>');
                                            addPushNotification('error', 'Profile not updated! Error:' , 5000 );

                                        }

                                    }


                                    if ( data['bases'] !== false ) {

                                        currentItem.find('.import-status').append('<p  class="alert alert-success" >Bases imported success. Count Bases:' + data['bases'].length + '</p>');
                                        addPushNotification('success', 'Bases imported successful! Count Bases:' + data['bases'].length , 2600 );

                                    } else {

                                        currentItem.find('.import-status').append('<p  class="alert alert-danger" >Bases imported ERROR</p>');
                                        addPushNotification('error', 'Bases imported Error:' , 5000 );

                                    }


                                } catch (e) {
                                    addPushNotification('error', 'Parse json error:' + e , 15000 );
                                }
                            }


                        },
                        error: function(){
                            //endLoader();
                            addPushNotification('error', 'Error ajax request!', 5000 );
                        }

                    });

                } catch (error) {
                    //endLoader();
                    addPushNotification('error', 'Code update profile Error :' + error , 5000 );
                }



            })



        }




        return false;


    });

    $('body').on('click', '.item-import', function () {

        /*var formData = new FormData();

        $(this).find('.import-data-item').each(function () {

            var dataKey = $(this).data('name');
            var dataValue = $(this).data('value');

            if( dataKey == 'base' ){
                formData.append(  dataKey, JSON.stringify(dataValue)  );
            }else{
                formData.append(  dataKey, dataValue );
            }

        });


        startLoader();

        try {


            $.ajax({
                url: '/import/init',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res){

                    endLoader();

                    if( res === false ){
                        addPushNotification('error', 'No data !', 3000 );
                    }else{




                        try {

                            var data = JSON.parse(res);
                            console.log(data);

                            if(  data['user'] ){
                                if(  data['user'] && data['user'] == true ){
                                    addPushNotification('success', 'User create success!', 2000 );
                                }else{
                                    addPushNotification('error', 'User create Error:', 5000 );
                                }
                            }

                            if( data['profile'] ){
                                if ( data['profile'] == true) {
                                    addPushNotification('success', 'Profile updated successful!', 2300 );
                                } else {
                                    addPushNotification('error', 'Profile not updated! Error:' , 5000 );
                                }
                            }


                            if ( data['bases'] !== false ) {

                                addPushNotification('success', 'Bases imported successful! Count Bases:' + data['bases'].length , 2600 );

                            } else {
                                addPushNotification('error', 'Bases imported Error:' , 5000 );
                            }


                        } catch (e) {
                            addPushNotification('error', 'Parse json error:' + e , 5000 );
                        }
                    }


                },
                error: function(){
                    endLoader();
                    addPushNotification('error', 'Error ajax request!', 5000 );
                }

            });

        } catch (error) {
            endLoader();
            addPushNotification('error', 'Code update profile Error :' + error , 5000 );
        }


        return false;*/


    })


    $('body').on('click', '.check-selected-csv-file', function() {


        var fileName = $('#imported-files option:selected').val();
        if( !fileName){
            addPushNotification('error', 'File does not exist! ', 2000);
            return false;
        }


        startLoader();
        try {

            $.ajax({
                url: '/import/check/',
                type: 'POST',
                data: {
                    file_name: fileName,
                },
                success: function (res) {

                    endLoader();

                    if (res) {
                        $('.check-import-file').html(res);
                        addPushNotification('success', 'Profile was loaded!', 3000);
                    } else {
                        addPushNotification('error', 'Error, try again!', 3000);
                    }

                },
                error: function () {
                    endLoader();
                    addPushNotification('error', 'Error, try again!', 3000);

                }
            });

        } catch (e) {
            endLoader();
            addPushNotification('error', 'Load profile error: ' + e, 3000);
        }

    });

})