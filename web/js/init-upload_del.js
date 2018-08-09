$(function () {

    'use strict';

    function ImageUpload( itemHtml ) {

        this.item = itemHtml;

        this.add = function(){
            $('.upload-images-block').append( this.item );
        };

        this.remove = function(){
            $(this).parents('.upload-images-item').remove();
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

        this.upload =  function ( file, initItem ) {

            if( file && initItem){
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

                reader.readAsDataURL(file);

            }


        };


    };


    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });


    var uploadItemHtml = '<div class="upload-images-item">' + $('.upload-images-item').html() + '</div>';
    var uploadImage = new ImageUpload(uploadItemHtml);



    $('body').on('click','.upload-images-action-remove', uploadImage.remove );

    $('body').on('click','.upload-images-action-add', function () {
        uploadImage.add();
    } );

    $('body').on('click','.upload-images-action-clear', function () {
        $(this).parents('.upload-images-item').find('.upload-images-item-crop').croppie( 'destroy' );
    });

    $('body').on('click','.upload-images-action-rotate', function () {
        $(this).parents('.upload-images-item').find('.upload-images-item-crop').croppie( 'rotate', 90 );
    });

    $('body').on('click','.upload-images-action-view', function () {
        uploadImage.view( $(this).parents('.upload-images-item').find('.upload-images-item-crop.croppie-container') );
    });

    $('body').on('change','.upload-images-action-upload input', function ( event ) {
        uploadImage.upload( event.target.files[0], $(this).parents('.upload-images-item').find('.upload-images-item-crop') );
    });


})