<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Image uploads';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="content-window-title">
    <?=$this->title?>
</div>
<div class="content-window-inner">


    <div class="upload-images-block">
        <div class="upload-images-item">
            <div class="upload-images-item-inner">
                <div class="upload-images-item-crop"></div>
                <div class="upload-images-item-crop-placeholder"></div>
                <div class="upload-images-item-actions">
                    <div class="btn-group-vertical text-left">
                        <div class="btn btn-danger upload-images-action-remove" data-toggle="tooltip" data-placement="left" title="Remove" >
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                        </div>
                        <div class="btn btn-info upload-images-action-clear" data-toggle="tooltip" data-placement="left" title="Clear">
                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                        </div>
                        <div class="btn btn-primary upload-images-action-rotate" data-toggle="tooltip" data-placement="left" title="Rotate">
                            <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>
                        </div>
                        <div class="btn btn-warning upload-images-action-upload" data-toggle="tooltip" data-placement="left" title="Upload" >
                            <input type="file">
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                        </div>
                        <div class="btn btn-primary upload-images-action-view" data-toggle="tooltip" data-placement="left" title="View">
                            <span class="glyphicon glyphicon-picture" aria-hidden="true"></span>
                        </div>
                        <div class="btn btn-success upload-images-action-add" data-toggle="tooltip" data-placement="left" title="Add">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade upload-image-preview-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="upload-image-preview">
                    <img src="" alt="">
                </div>
            </div>
        </div>
    </div>



   <!-- <div class="window-title">Users</div>-->
    <div class="window-description">
        <!--<p>
            Image uploads. Recomended image size  - 1024 - 1024.
            <?php
/*
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_PORT => "8080",
                CURLOPT_URL => "http://88.99.189.111:8080/image/preview/8",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"image\"\r\n\r\n\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
                CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache",
                    "X-Auth-Token: 3:z3vf9939hTfqa4FA",
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {

                $base64 = 'data:image/;base64,' . base64_encode($response);
                echo '<img src="'.$base64.'">';
            }

            */?>
        </p>
        <p>Max image count - 5</p>
        <p>
            <button type="button" class="btn btn-success add-image-input"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> add image</button>
        </p>-->
    </div>
    <div class="image-uploads">

        <div class="image-upload-item">
            <div class="image-resize">
                <div class="image-upload-item-remove"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></div>
                <img src="" alt="" style="max-width:100%;" >
            </div>
            <p>
                <input type="file" accept="image/*"  />
            </p>
        </div>


    </div>
    <p>
        <form class="uploadImageForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" name="city" class="form-control"  placeholder="city">
            </div>
            <div class="form-group">
                <label for="city">Description</label>
                <input type="text" name="name"  class="form-control"  placeholder="description" />
            </div>
            <div class="form-group">
                <label for="city">Name</label>
                <input type="text" name="name" class="form-control"  placeholder="Name" />
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-success save-image-input"> Confirm images</button>
            </div>
        </form>

    </p>

    <!--
           <p>
               <input name="imagefile[]" type="file" id="takePictureField" accept="image/*" onchange="uploadPhotos()" />
           </p>
           -->

    <!--<form id="uploadImageForm" enctype="multipart/form-data">
        <input id="name" value="#{name}" />
        ... a few more inputs ...
    </form>-->


</div>


