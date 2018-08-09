$(function () {

    'use strict';
    function ImageUpload() {

        this.mainSelector = '.upload-images-block';
        this.itemSelector =  '.upload-images-item';
        this.item = '<div class="upload-images-item">' + $(this.itemSelector).html() + '</div>';
        this.minImageCount = 1;
        this.maxImageCount = 5;
        this.imageType = 'image/jpeg';


        this.add = function(){
            if( $(this.itemSelector).length >= this.maxImageCount ){
                alert( 'Max image count - ' + this.maxImageCount );
                return false;
            }
            $(this.mainSelector).append( this.item );
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

        this.sendImages = function (data, userProfileId, type, formData ) {

            var resultArray = '';
            var countUpload = data.length;

            for( var imageData of data ){
                imageData.item.addClass('inProgress');

                if( imageData.blob  ) {


                    var form = new FormData();
                    form.append('image', imageData.blob);
                    form.append('profile_id', userProfileId);


                    console.log('form:',form);

                    $.ajax({
                        url: '/site/add_image/',
                        type: 'POST',
                        data: form,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (res) {
                            if (res) {
                                imageData.item.removeClass('inProgress')
                                countUpload = parseInt(countUpload) - 1;
                                resultArray += res + ',';
                                if (countUpload == 0) {
                                    console.log('END upload', resultArray);
                                    formData.images = resultArray;
                                    createEvent(userProfileId, type, formData);
                                }
                            }
                        },
                        error: function () {
                            alert('Error!');
                        }
                    });

                }

            }


        };
        this.uploadImages = function ( userProfileId, type, formData  ) {

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
                             classThis.sendImages( Images, userProfileId, type, formData );
                         }

                     });
                 }

            });

        };


        this.upload =  function ( file, initItem ) {

            if( file.target.files[0] && initItem){

                if( file.target.files[0].type != this.imageType ) {
                    $(file.target).val('');
                    alert('Image format - ' + this.imageType);
                    return false;
                }

                var reader  = new FileReader();
                reader.onload = function () {
                    initItem.croppie('destroy');
                    initItem.croppie({
                        enableOrientation:true,
                        enableExif: true,
                        viewport: {
                            width: 250,
                            height: 250,
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

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });


    var uploadImage = new ImageUpload();


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






    function validationForm( type ){

        let validResult = {
            'valid': true,
            'empty':[],
            'error_message':'',
            'formData':[]
        };


        let lat = $('.form-item-block-map input[name="lat"]').val();
        let lng = $('.form-item-block-map input[name="lng"]').val();
        let lastVisit = $('.form-item-block-last-visit input[name="last-visit"]').val();

        var images = false;
        if( $('.upload-images-item .upload-images-item-crop.croppie-container').length >= 1){
            images = true;
        }

       /* $('.upload-images-item .upload-images-item-crop.croppie-container').each(function () {

            $(this).croppie('result',{
                    type:'base64',
                    size:{width:1024, height:1024 }
                }
            ).then(function(result) {
                if( result.length > 0 ){
                    images = true;
                    alert(images);
                }
            });


        });*/


        let description = $('.form-item-block-description textarea[name="description"]').val();
        let startTime = $('.form-item-block-start-time input[name="start-time"]').val();
        let finishTime = $('.form-item-block-finish-time input[name="finish-time"]').val();

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

                if( lastVisit.length == 0 ){
                    validResult.valid = false;
                    validResult.empty.push('lastVisit');
                    validResult.error_message += "<li>last visit is empty!</li>";
                }else{
                    validResult.formData.lastVisit= lastVisit;
                }


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

                if( startTime.length == 0 ){
                    validResult.valid = false;
                    validResult.empty.push('startTime');
                    validResult.error_message += '<li>start Time is empty!</li>';
                }else{
                    validResult.formData.startTime= startTime;
                }

                if( finishTime.length == 0 ){
                    validResult.valid = false;
                    validResult.empty.push('finishTime');
                    validResult.error_message += '<li>finish Time is empty!</li>';
                }else{
                    validResult.formData.finishTime= finishTime;
                }



                break;

            default:

                break;
        }

        return validResult;


    }

    function clearFormData(){
        uploadImage.clearAll();
        $('.upload-images-item').removeClass('success');
        $('.upload-images-item').removeClass('inProgress');
        $('.uploadImageForm input').val('');
        $('.uploadImageForm textarea').val('');
    }

    $('body').on('change','#type', function () {


        /*CLASSES
                form-item-block-map
                form-item-block-description
                form-item-block-last-visit
                form-item-block-start-time
                form-item-block-finish-time
                form-item-block-submit
                form-item-block-image
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
                $('.form-item-block-submit').show();


                break;

            case 'WILL_BE_HERE':
                $('.form-item-block-map').show();
                $('.form-item-block-image').show();
                $('.form-item-block-description').show();
                $('.form-item-block-start-time').show();
                $('.form-item-block-finish-time').show();
                $('.form-item-block-submit').show();



                break;

            default:

                break;
        }


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





    // Click to main form submit btn
    $('body').on('click', '.save-image-input' , function (event) {





        $('.success-form').hide();
        $('.error-form').hide();
        $('.validation-form').hide();

        var userProfileId = $('.user-select option:selected').val()
        if( userProfileId.length == 0 ){
            alert('Please select user!');
            return false;
        }

        let type = $('#type option:selected').val();


        if( type.length > 0){
            let valid = validationForm( type );
            if( valid.valid == false ){
                $('.fields-empty').html(valid.error_message);
                $('.validation-form').show();

                return false;
            }

            //valid.formData

            switch (type) {

                case 'PROFILE':

                    createEvent(userProfileId, type, valid.formData);

                    break;

                case 'WAS_HERE':

                    //imageUploader();
                    uploadImage.uploadImages( userProfileId, type, valid.formData );
                    break;

                case 'WILL_BE_HERE':

                    //imageUploader(userProfileId, type, valid.formData);
                    uploadImage.uploadImages( userProfileId, type, valid.formData );

                    break;

                default:

                    break;


            }





        }else{
            alert('Please select type event!');
            return false;
        }


    });


    function createEvent( userProfileId, type, formData ){

        $('.save-image-input').hide();
        $('.save-image-input-loader').show();


        $('.success-form').hide();
        $('.error-form').hide();

        var data = new FormData();
        data.append('type', type);
        data.append('profile_id', userProfileId);
        for (var prop in formData) {
            data.append( prop, formData[prop]);
        }
        $.ajax({
            url: '/site/add_event_to_server/',
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                if (res) {

                    let jsonEvent = JSON.parse(res);
                    if( jsonEvent.id ){
                        $('.success-form').show();
                        /*setTimeout( function () {
                            $('.success-form').hide();
                        }, 4000);*/
                    }else{
                        $('.error-form').show();
                    }
                    clearFormData();
                    //console.log('RES :', res );
                }

                $('.save-image-input').show();
                $('.save-image-input-loader').hide();


            },
            error: function () {
                $('.save-image-input').show();
                $('.save-image-input-loader').hide();
                alert('Error!');
            }
        });


    }

/*

    function imageUploader( userProfileId, type, formData ){


        var countUpload = 0;
        var resultArray = '';

        uploadImage.uploadImages( );

        /!*$('.image-upload-item').each( function (e) {
            if( $(this).find('img').attr('src') ){
                countUpload = countUpload + 1;
            }
        })*!/

        /!*var imagesArray = uploadImage.getImages();


        if( imagesArray.length == 0){
            alert('plz upload image!');
            return false;
        }



        // Rewrite each TO ARRAY LOOP !
        console.log('array',imagesArray);
        for( var image of imagesArray ){
            console.log(image);
        }
*!/

        $('.image-upload-item').each( function (e) {

            var currentItemSelector = $(this);
            currentItemSelector.addClass('download-false')

            var imageBase64 = $(this).find('img').attr('src');
            if( imageBase64 ) {

                var resizedImage = dataURItoBlob(imageBase64);

                if (resizedImage) {
                    var data = new FormData();
                    data.append('image', resizedImage);
                    data.append('profile_id', userProfileId);


                    $.ajax({
                        url: '/site/add_image/',
                        type: 'POST',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (res) {
                            if (res) {
                                currentItemSelector.removeClass('download-false')
                                currentItemSelector.addClass('download-true')
                                countUpload = parseInt(countUpload) - 1;
                                resultArray += res + ',';
                                if (countUpload == 0) {
                                    console.log('END upload', resultArray);
                                    formData.images = resultArray;
                                    createEvent(userProfileId, type, formData);
                                }
                            }
                        },
                        error: function () {
                            alert('Error!');
                        }
                    });
                }

            }

        })


    }
*/



  /*  function dataURItoBlob(dataURI) {
        // convert base64/URLEncoded data component to raw binary data held in a string
        var byteString;
        if (dataURI.split(',')[0].indexOf('base64') >= 0)
            byteString = atob(dataURI.split(',')[1]);
        else
            byteString = unescape(dataURI.split(',')[1]);

        // separate out the mime component
        var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

        // write the bytes of the string to a typed array
        var ia = new Uint8Array(byteString.length);
        for (var i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }

        return new Blob([ia], {type:mimeString});
    }

*/

 /*  $('body').on('change', '.image-upload-item input' , function (event) {

       if( $('.image-upload-item').length > 5 ){
           alert('Max images 5 !');
           event.preventDefault();
           return false;
       }

       var file = event.target.files[0];
        if( file ){
            if( file.type == "image/jpeg" ){
                canvasResizePreview(file, $(this) );
            }else{
                alert('Not supported format!');
                $(this).val('');
            }
        }else{
            $(this).parents('.image-upload-item').find('img').attr('src','');
        }



   })

    $('body').on('click','.image-upload-item-remove', function () {
        if( $('.image-upload-item').length == 1 ){
            alert('Min 1 images !');
            return false;
        }
        $(this).parents('.image-upload-item').remove();

    })
*/
    /*$('.add-image-input').on('click', function () {

        if( $('.image-upload-item').length >= 5 ){
            alert('Max images 5 !');
            return false;
        }
        let imageFormHtml = '<div class="image-upload-item">\n' +
            '            <div class="image-resize">\n' +
            '                <div class="image-upload-item-remove"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></div>\n' +
            '                <img src="" alt="" style="max-width:100%;" >\n' +
            '            </div>\n' +
            '            <p>\n' +
            '                <input type="file" accept="image/!*"  />\n' +
            '            </p>\n' +
            '        </div>';

        $('.image-uploads').append(imageFormHtml);
    })

   function canvasResizePreview( file , thisItem ){

       let width = 1024;
       let height = 1024;


       if(file.type.match(/image.*!/)) {
           //console.log('An image has been loaded');

           // Load the image
           var reader = new FileReader();
           reader.onload = function (readerEvent) {
               var image = new Image();
               image.onload = function (imageEvent) {

                   // Resize the image
                   var canvas = document.createElement('canvas');

                   canvas.width = width;
                   canvas.height = height;

                   canvas.getContext('2d').drawImage(image, 0, 0, width, height);

                   var dataUrl = canvas.toDataURL('image/jpeg');

                   thisItem.parent().parent().find('img').attr('src', dataUrl );

                   var resizedImage = dataURLToBlob(dataUrl);

                   console.log(resizedImage);

                   /!*$.event.trigger({
                       type: "imageResized",
                       blob: resizedImage,
                       url: dataUrl
                   });*!/
               }
               image.src = readerEvent.target.result;
           }
           reader.readAsDataURL(file);
       }

   }
*/




/*

    window.uploadPhotos = function(url){
        // Read in file
        var file = event.target.files[0];

        // Ensure it's an image
        if(file.type.match(/image.*!/)) {
            console.log('An image has been loaded');

            // Load the image
            var reader = new FileReader();
            reader.onload = function (readerEvent) {
                var image = new Image();
                image.onload = function (imageEvent) {

                    // Resize the image
                    var canvas = document.createElement('canvas'),
                        max_size = 300,// TODO : pull max size from a site config
                        width = image.width,
                        height = image.height;

                        console.log('width', width );
                        console.log('height', height );

                    /!*if (width > height) {
                        if (width > max_size) {
                            height *= max_size / width;
                            width = max_size;
                        }
                    } else {
                        if (height > max_size) {
                            width *= max_size / height;
                            height = max_size;
                        }
                    }
                    canvas.width = width;
                    canvas.height = height;*!/
                    canvas.width = 1024;
                    canvas.height = 1024;
                    canvas.getContext('2d').drawImage(image, 0, 0, 1024, 1024);
                    var dataUrl = canvas.toDataURL('image/jpeg');

                    $('.image-origin img').attr('src', dataUrl );

                    var resizedImage = dataURLToBlob(dataUrl);

                    console.log(resizedImage);

                    $.event.trigger({
                        type: "imageResized",
                        blob: resizedImage,
                        url: dataUrl
                    });
                }
                image.src = readerEvent.target.result;
            }
            reader.readAsDataURL(file);
        }
    };

*/



    /* Utility function to convert a canvas to a BLOB */
  /*  var dataURLToBlob = function(dataURL) {
        var BASE64_MARKER = ';base64,';
        if (dataURL.indexOf(BASE64_MARKER) == -1) {
            var parts = dataURL.split(',');
            var contentType = parts[0].split(':')[1];
            var raw = parts[1];

            return new Blob([raw], {type: contentType});
        }

        var parts = dataURL.split(BASE64_MARKER);
        var contentType = parts[0].split(':')[1];
        var raw = window.atob(parts[1]);
        var rawLength = raw.length;

        var uInt8Array = new Uint8Array(rawLength);

        for (var i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }


        //console.log('Image dataURLToBlob');

        return new Blob([uInt8Array], {type: contentType});


    }
    /!* End Utility function to convert a canvas to a BLOB      *!/
*/
    /* Handle image resized events */
/*    $(document).on("imageResized", function (event) {
        var data = new FormData($("form[id*='uploadImageForm']")[0]);
        if (event.blob && event.url) {
            data.append('image_data', event.blob);

            /!*$.ajax({
                url: event.url,
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(data){
                    //handle errors...
                }
            });*!/
        }
    });*/


})