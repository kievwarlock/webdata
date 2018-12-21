var currentUserId = $('.point-user-id').val();
var currentUserToken = $('.point-user-token').val();


function ImageUpload() {

    this.mainSelector = '.upload-images-block';
    this.itemSelector =  '.upload-images-item';
    this.itemTemplate = '.upload-images-item-template';
    this.item = '<div class="upload-images-item">' + $(this.itemTemplate).html() + '</div>';
    this.minImageCount = 1;
    this.maxImageCount = 5;
    this.imageType = 'image/jpeg';




    this.add = function(){
        if( $(this.itemSelector).length >= this.maxImageCount ){
            alert( 'Max image count - ' + this.maxImageCount );
            return false;
        }
        $(this.mainSelector).append( '<div class="upload-images-item">' + $(this.itemTemplate).html() + '</div>' );
    };

    this.clear = function (event) {
        $(event.target).parents(this.itemSelector).find('.upload-images-item-crop.croppie-container').croppie( 'destroy' );
    };

    this.clearAll = function () {
        $('.upload-images-item-crop.croppie-container').each( function () {
            $(this).croppie( 'destroy' );
        });
    };

    this.rotate = function (event) {
        $(event.target).parents(this.itemSelector).find('.upload-images-item-crop').croppie( 'rotate', 90 );
    };

    this.remove = function(event){

        if( $(this.itemSelector).length <= this.minImageCount ){
            alert( 'Min image count - ' + this.minImageCount );
            return false;
        }
        $(event.target).parents(this.itemSelector).remove();
    };

    this.view = function ( item ) {

        if( item.length > 0 ){
            item.croppie('result',{
                    type:'base64',
                    size:{width:1024, height:1024 }
                }
            ).then(function(result) {
                $('.upload-image-preview img').attr('src', result );
                $('.upload-image-preview-modal').modal('show');
            });
        }

    };

    this.sendImages = function (data, userToken, type, formData ) {

        var resultArray = '';
        var countUpload = data.length;

        for( var imageData of data ){


            if( imageData.blob  ) {


                var form = new FormData();
                form.append('image', imageData.blob);
                form.append('token', userToken);



                $.ajax({
                    url: '/image/create/',
                    type: 'POST',
                    data: form,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (res) {


                        try {

                            if (res) {

                                // TODO must check image request from API
                                addPushNotification('success', 'Image uploaded. Image id:' + res ,3000 );
                                countUpload = parseInt(countUpload) - 1;
                                resultArray += res + ',';

                                if (countUpload == 0) {
                                    addPushNotification('success', 'All images was uploaded successful!' ,3000 );
                                    formData.images = resultArray;
                                    createEvent(userToken, type, formData);
                                }
                            }else{



                                endLoader();
                                addPushNotification('error', 'Error image upload' , 2000 );


                            }

                        } catch (e) {
                            endLoader();
                            addPushNotification('error', 'Error' . e , 2000 );

                        }
                    },
                    error: function () {
                        endLoader();
                        addPushNotification('error', 'Error' . e, 2000 );
                    }
                });

            }

        }


    };



    this.uploadImages = function ( userToken, type, formData  ) {

        var Images = [];
        var countImages = $('.upload-images-item-crop.croppie-container').length;
        var classThis = this;
        $(this.itemSelector).each( function () {

            let croppieItem = $(this).find('.upload-images-item-crop.croppie-container');
            let currentItem = $(this);
            if (croppieItem.length > 0) {
                croppieItem.croppie('result', {
                    type: 'blob',
                    size: {width: 1024, height: 1024},
                    format:'jpeg',
                }).then( function ( result ) {

                    Images.push({
                        item:currentItem,
                        blob:result,
                    });

                    if( Images.length == countImages ){
                        classThis.sendImages( Images, userToken, type, formData );
                    }

                });
            }

        });

    };

    this.upload =  function ( file, initItem ) {

        if( file.target.files[0] && initItem){

            if( file.target.files[0].type != this.imageType ) {
                $(file.target).val('');
                addPushNotification('error', 'Image format - ' + this.imageType, 3500 );

                return false;
            }

            var reader  = new FileReader();
            reader.onload = function () {
                initItem.croppie('destroy');
                initItem.croppie({
                    enableOrientation:true,
                    enableExif: true,
                    viewport: {
                        width: 300,
                        height: 300,
                    },
                    boundary: {
                        width: 300,
                        height: 300
                    },
                    url:reader.result
                });

            };

            reader.readAsDataURL(file.target.files[0]);

        }


    };

};

// Main form validation
function validationForm( type ){

    let validResult = {
        'valid': true,
        'empty':[],
        'error_message':'',
        'formData':[]
    };


    let lat = $('.form-item-block-map input[name="lat"]').val();
    let lng = $('.form-item-block-map input[name="lng"]').val();
    //let lastVisit = Date.parse( $('.form-item-block-last-visit input[name="last-visit"]').val() + ' GMT');

    //let lastVisit = Date.parse( $('.form-item-block-last-visit input[name="last-visit"]').datetimepicker('getDate') );



    var images = false;
    if( $('.upload-images-item .upload-images-item-crop.croppie-container').length >= 1){
        images = true;
    }


    var tags = false;
    if( $("#geo-tags").length > 0 ){
        tags = $("#geo-tags").select2("data");
    }


    let description = $('.form-item-block-description textarea[name="description"]').val();
    //let startTime = Date.parse( $('.form-item-block-start-time input[name="start-time"]').val() + ' GMT');
    //let finishTime = Date.parse( $('.form-item-block-finish-time input[name="finish-time"]').val() + ' GMT');

    let startTime = Date.parse( $('.form-item-block-start-time input[name="start-time"]').datetimepicker('getDate') );
    let finishTime = Date.parse( $('.form-item-block-finish-time input[name="finish-time"]').datetimepicker('getDate') );


    switch(type){

        case 'PROFILE':


            if( lat.length == 0 ){
                validResult.valid = false;
                validResult.empty.push('lat');
                validResult.error_message += "<li>Lat is empty!</li>";
            }else{
                validResult.formData.lat= lat;
            }
            if( lng.length == 0 ){
                validResult.valid = false;
                validResult.empty.push('lng');
                validResult.error_message += "<li>lng is empty!</li>";
            }else{
                validResult.formData.lng= lng;
            }

            /*if( lastVisit.length == 0 || isNaN(lastVisit) === true ){
                validResult.valid = false;
                validResult.empty.push('lastVisit');
                validResult.error_message += "<li>last visit is empty!</li>";
            }else{
                validResult.formData.lastVisit= lastVisit;
            }*/


            break;

        case 'WAS_HERE':


            if( lat.length == 0 ){
                validResult.valid = false;
                validResult.empty.push('lat');
                validResult.error_message += "<li>last visit is empty!</li>";
            }else{
                validResult.formData.lat= lat;
            }




            if( lng.length == 0 ){
                validResult.valid = false;
                validResult.empty.push('lng');
                validResult.error_message += "<li>lng is empty!</li>";
            }else{
                validResult.formData.lng= lng;
            }

            if( description.length == 0 ){
                validResult.valid = false;
                validResult.empty.push('description');
                validResult.error_message += "<li>description is empty!</li>";
            }else{
                validResult.formData.description= description;
            }

            if( images == false ){
                validResult.valid = false;
                validResult.empty.push('images');
                validResult.error_message += "<li>images is empty!</li>";
            }

            if( tags == false ){
                validResult.valid = false;
                validResult.empty.push('tags');
                validResult.error_message += "<li>Tags is empty!</li>";
            }else{
                validResult.formData.tags= tags;
            }


            break;

        case 'WILL_BE_HERE':


            if( lat.length == 0 ){
                validResult.valid = false;
                validResult.empty.push('lat');
                validResult.error_message += '<li>lat is empty!</li>';
            }else{
                validResult.formData.lat= lat;
            }

            if( lng.length == 0 ){
                validResult.valid = false;
                validResult.empty.push('lng');
                validResult.error_message += '<li>lng is empty!</li>';
            }else{
                validResult.formData.lng= lng;
            }

            if( description.length == 0 ){
                validResult.valid = false;
                validResult.empty.push('description');
                validResult.error_message += '<li>description is empty!</li>';
            }else{
                validResult.formData.description= description;
            }

            if( images == false ){
                validResult.valid = false;
                validResult.empty.push('images');
                validResult.error_message += '<li>images is empty!</li>';
            }

            if( startTime.length == 0 || isNaN(startTime) === true ){
                validResult.valid = false;
                validResult.empty.push('startTime');
                validResult.error_message += '<li>start Time is empty!</li>';
            }else{
                validResult.formData.startTime= startTime;
            }

            if( finishTime.length == 0 || isNaN(finishTime) === true ){
                validResult.valid = false;
                validResult.empty.push('finishTime');
                validResult.error_message += '<li>finish Time is empty!</li>';
            }else{
                validResult.formData.finishTime= finishTime;
            }


            if( tags == false ){
                validResult.valid = false;
                validResult.empty.push('tags');
                validResult.error_message += "<li>Tags is empty!</li>";
            }else{
                validResult.formData.tags= tags;
            }


            break;

        default:

            break;
    }

    return validResult;


}

// Clear form fiels
function clearFormData(){
    uploadImage.clearAll();
    $('.upload-images-item').removeClass('success');

    $('.uploadImageForm input').val('');
    $('.uploadImageForm textarea').val('');
}

// Create event
function createEvent( userToken, type, formData ){


    try {

        var data = new FormData();
        data.append('type', type);
        data.append('token', userToken);


        // TODO remove when ownerId not need
        if( currentUserId.length <= 0){
            alert('currentUserId not found!');
            return false;
        }
        data.append('id', currentUserId);

        for (var prop in formData) {

            if( prop == "tags" ){
                for (var tags in formData[prop]) {
                    data.append( 'tags[]', formData[prop][tags]['id'] );
                }
            }else{
                data.append( prop, formData[prop]);
            }

        }


        $.ajax({
            url: '/point/create/',
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {

                endLoader();

                if (res) {
                    try {

                        let jsonEvent = JSON.parse(res);
                        if( jsonEvent.id ){
                            addPushNotification('success', 'New Base was created successful!' );
                            clearFormData();
                        }else{
                            throw new SyntaxError("Ошибка в данных json");
                        }

                    } catch (error) {
                        addPushNotification('error', 'Error:' + error, 4000 );
                    }


                }else{
                    throw new SyntaxError("Ошибка в данных ответа!");
                }


            },
            error: function () {
                endLoader();
                addPushNotification('error', 'Error ajax!', 4000 );
            }
        });


    } catch (e) {
        endLoader();
        addPushNotification('error', 'Error: code' + e, 4000 );
    }



}

// Update geo point
function updateGeoPoint( idPoint, userToken, type, formData ){


    var data = new FormData();
    data.append('id', idPoint);
    data.append('type', type);
    data.append('token', userToken);

    if( $('.geo-point-images').length > 0 ){
        $('.geo-point-images').each(function (e) {
            data.append('imageIds[]', $(this).val() );
        })
    }

    for (var prop in formData) {
        if( prop == "tags" ){
            for (var tags in formData[prop]) {
                data.append( 'tags[]', formData[prop][tags]['id'] );
            }
        }else{
            data.append( prop, formData[prop]);
        }
    }


    try {

        startLoader();

        $.ajax({
            url: '/point/update/',
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (result) {

                endLoader();

                if (result) {

                    try {

                        var resultData =  JSON.parse(result);

                        if (!resultData['point_status'] || !resultData['content_status'] ) {
                            throw new SyntaxError("Ошибка в данных ответа!");
                        }

                        if( resultData['point_status'] == true ){
                            addPushNotification('success', resultData['point_msg'],4000 );
                        }else{
                            addPushNotification('error', resultData['point_msg'],4000 );
                        }

                        if( resultData['content_status'] == true ){
                            addPushNotification('success', resultData['content_msg'],4000 );
                        }else{
                            addPushNotification('error', resultData['content_msg'],4000 );
                        }

                    } catch ( error ) {
                        addPushNotification('error', 'Code error:' + error,4000 );
                    }


                }


            },
            error: function () {
                endLoader();
                addPushNotification('error', 'Code error:' + error,4000 );
            }

        });


    } catch (e) {
        endLoader();
        addPushNotification('error', 'Code error:' + error,4000 );
    }


}


var uploadImage = new ImageUpload();


$(function () {

    'use strict';








    // Init all image control event
    $('body').on('click','.upload-images-action-remove', function (e) {
        uploadImage.remove(e);
    } );

    $('body').on('click','.upload-images-action-add', function () {
        uploadImage.add();
    } );

    $('body').on('click','.upload-images-action-clear', function (event) {
        uploadImage.clear(event);
    });

    $('body').on('click','.upload-images-action-rotate', function () {
        uploadImage.rotate(event);
    });

    $('body').on('click','.upload-images-action-view', function () {
        uploadImage.view( $(this).parents('.upload-images-item').find('.upload-images-item-crop.croppie-container') );
    });

    $('body').on('change','.upload-images-action-upload input', function ( event ) {
        uploadImage.upload( event, $(this).parents('.upload-images-item').find('.upload-images-item-crop') );
    });





    // Change controls by selected TYPE
    $('body').on('change','#type', function () {


        /*CLASSES
                form-item-block-map
                form-item-block-description
                form-item-block-last-visit
                form-item-block-start-time
                form-item-block-finish-time
                form-item-block-submit
                form-item-block-image

                form-item-block-tags
        */

        clearFormData();

        var typeEvent = $(this).val();


        $('.form-item-block').hide();

        switch(typeEvent){

            case 'PROFILE':

                $('.form-item-block-map').show();
                $('.form-item-block-last-visit').show();
                $('.form-item-block-submit').show();


                break;

            case 'WAS_HERE':
                $('.form-item-block-map').show();
                $('.form-item-block-image').show();
                $('.form-item-block-description').show();


                $('.form-item-block-tags').show();

                $('.form-item-block-submit').show();


                break;

            case 'WILL_BE_HERE':
                $('.form-item-block-map').show();
                $('.form-item-block-image').show();
                $('.form-item-block-description').show();
                $('.form-item-block-start-time').show();
                $('.form-item-block-finish-time').show();

                $('.form-item-block-tags').show();

                $('.form-item-block-submit').show();



                break;

            default:

                break;
        }


    });




    // Init TimePicker and tooltips

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    var lastVisit = $('.time-ui[name="last-visit"]');
    var startDateTextBox = $('.time-ui[name="start-time"]');
    var endDateTextBox = $('.time-ui[name="finish-time"]');
    if( lastVisit.length > 0 ) {
        lastVisit.datetimepicker(
        {
            dateFormat: 'yy-mm-dd'
        });
    }
    if( startDateTextBox.length > 0 && endDateTextBox.length > 0 ) {
        $.timepicker.datetimeRange(
            startDateTextBox,
            endDateTextBox,
            {
                minInterval: (1000*60*60*24), // 1hr
                dateFormat: 'yy-mm-dd',
                //timeFormat: 'HH:mm',
                start: {}, // start picker options
                end: {} // end picker options
            }
        );
    }
    // End Init TimePicker




    // Click to main form submit btn
    $('body').on('click', '.save-image-input' , function (event) {


        var userToken = currentUserToken;

        if( userToken.length == 0 ){

            addPushNotification('error', 'Please select user!', 4000 );
            return false;
        }

        let type = $('#type option:selected').val();


        if( type.length > 0){
            let valid = validationForm( type );
            if( valid.valid == false ){
                addPushNotification('error', valid.error_message , 6000 );
                return false;
            }


            switch (type) {

                case 'PROFILE':

                    startLoader();
                    createEvent(userToken, type, valid.formData);

                    break;

                case 'WAS_HERE':

                    startLoader();
                    uploadImage.uploadImages( userToken, type, valid.formData );


                    break;

                case 'WILL_BE_HERE':


                    startLoader();
                    uploadImage.uploadImages( userToken, type, valid.formData );

                    break;

                default:

                    break;


            }



        }else{
            addPushNotification('error', 'Please select type event!', 4000 );
            return false;
        }


    });



    // Update geo point
    $('body').on('click', '.geo-point-update' , function (event) {

        event.preventDefault();

        var userToken = $('#geo-point-user-token').val();
        var pointType = $('#geo-point-type').val();
        var pointId = $('#geo-point-id').val();



        if (userToken.length == 0) {
            alert('NO user token!');
            return false;
        }
        if (pointType.length == 0) {
            alert('NO geo point type!');
            return false;
        }
        if (pointId.length == 0) {
            alert('NO geo point id!');
            return false;
        }

        let valid = validationForm(pointType);
        if (valid.valid == false) {

            addPushNotification('error', valid.error_message, 5000 );
            return false;
        }

        updateGeoPoint( pointId, userToken, pointType, valid.formData);




    });





})