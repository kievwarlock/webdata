<?php


if( is_array($point_data) ){?>


    <form name="update-geo-point" action="POST" class="update-geo-point">
        <div class="modal-body">

            <div class="alert alert-danger error-form" role="alert" >
                <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
                <span class="sr-only">Error! </span>
                <div class="error-form-msg">

                </div>
            </div>

            <div class="alert alert-success success-form" role="alert" >
                <span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>
                <span class="sr-only">Success:</span>
                <div class="success-form-msg">

                </div>
            </div>

            <!--<pre>
                <?php /*print_r($point_data); */?>
            </pre>-->
            <!--
            <?php /*if( is_array( $images ) ){*/?>
                <pre>
                    <?php /*print_r($images);*/?>
                </pre>
            --><?php /*} */?>

            <input type="hidden" name="geo-point-type" id="geo-point-type" value="<?=$point_data['type']?>">
            <input type="hidden" name="geo-point-id"  id="geo-point-id" value="<?=$point_data['id']?>">
            <input type="hidden" name="geo-point-user-token"  id="geo-point-user-token" value="<?=$token?>">
            <?php
            if( is_array( $point_data['contentCard']['imageIds'] ) and count( $point_data['contentCard']['imageIds'] ) > 0 ) {
                foreach ( $point_data['contentCard']['imageIds'] as $image_id ) { ?>
                    <input type="hidden" name="geo-point-images" class="geo-point-images"  value="<?=$image_id ?>">
                <?php } ?>

            <?php } ?>


            <?php if( isset($point_data['latitude']) and !empty($point_data['latitude']) and isset($point_data['longitude']) and !empty($point_data['longitude']) ){ ?>
                <div class="map-form-block">
                    <div class="map-form-block-inner">
                        <div id="map" class="map-form mapboxgl-map">
                        </div>
                    </div>
                </div>

                <div class="form-group form-item-block-map">
                    <label for="latitude">Latitude</label>
                    <input type="text" name="lat" class="form-control" value="<?=$point_data['latitude']?>" id="geo-point-latitude" placeholder="latitude" disabled>
                </div>
                <div class="form-group form-item-block-map">
                    <label for="longitude">longitude</label>
                    <input type="text" name="lng" class="form-control" value="<?=$point_data['longitude']?>" id="geo-point-longitude" placeholder="longitude" disabled>
                </div>
                <script>
                    initMap(<?=$point_data['latitude']?>,<?=$point_data['longitude']?>);
                </script>
            <?php } ?>

            <?php if( isset($point_data['lastVisit']) ) { ?>

                <div class="form-group form-item-block-last-visit">
                    <label for="last-visit">Последнее посещение:</label>
                    <input type="text"  name="last-visit"   class="last-visit modal-datetimepicker  form-control" placeholder="Время">
                </div>

                <script>
                    var lastVisit = $('.modal-datetimepicker');
                    if( lastVisit.length > 0 ) {
                        lastVisit.datetimepicker({
                            dateFormat: 'yy-mm-dd',
                        });
                    }
                    var dateLastVisit = new Date(<?=$point_data['lastVisit']?>);
                    $('.last-visit').datetimepicker('setDate', dateLastVisit );

                </script>

            <?php } ?>


            <?php if( isset($point_data['startTime']) ) { ?>

                <div class="form-group form-item-block-start-time" >
                    <label for="startTime">startTime:</label>
                    <input type="text" id="startTime" name="start-time"   class="startTime modal-datetimepicker  form-control" placeholder="startTime">
                </div>

                <script>
                    var startTime = $('.modal-datetimepicker');
                    if( startTime.length > 0 ) {
                        startTime.datetimepicker({
                            dateFormat: 'yy-mm-dd',
                        });
                    }
                    var datestartTime = new Date(<?=$point_data['startTime']?>);
                    $('.startTime').datetimepicker('setDate', datestartTime );
                </script>

            <?php } ?>

            <?php if( isset($point_data['finishTime']) ) {  ?>

                <div class="form-group form-item-block-finish-time">
                    <label for="finishTime">finishTime:</label>
                    <input type="text" id="finishTime" name="finish-time"   class="finishTime modal-datetimepicker  form-control" placeholder="finishTime">
                </div>

                <script>

                    var finishTime = $('.finishTime');
                    if( finishTime.length > 0 ) {
                        finishTime.datetimepicker({
                            dateFormat: 'yy-mm-dd',
                        });
                    }
                    var datefinishTime = new Date(<?=$point_data['finishTime']?>);
                    $('.finishTime').datetimepicker('setDate', datefinishTime );

                </script>

            <?php } ?>



            <?php if( isset($point_data['contentCard']) and is_array($point_data['contentCard']) ) { ?>


                <?php if( is_array($point_data['contentCard']['topicIds']) and is_array( $topic ) ) {?>
                    <div class="form-group form-item-block-tags">
                        <label for="tags">Теги:</label>
                        <?php
                            echo \kartik\select2\Select2::widget([
                                'id' => 'geo-tags',
                                'name' => 'geo-tags',
                                'hideSearch' => false,
                                'data' => \yii\helpers\ArrayHelper::map($topic, 'id', 'text'),
                                'value' => $point_data['contentCard']['topicIds'],
                                'options' => ['multiple' => true, 'placeholder' => 'Select a tags']
                            ]);
                        ?>

                    </div>
                <?php } ?>

                <?php if( isset($point_data['contentCard']['text']) ) {?>
                <div class="form-group form-item-block-description">
                    <label for="text-description">Описание:</label>
                    <textarea name="description"  id="text-description" class="form-control" placeholder="Описание"><?=$point_data['contentCard']['text']?></textarea>
                </div>

                <?php } ?>





                <?php if( is_array( $images ) ){?>

                    <div class="upload-images-item-template">
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

                                    <div class="btn btn-success upload-images-action-add" data-toggle="tooltip" data-placement="left" title="Add">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="image-form-block  form-item-block-image">
                        <div class="upload-images-block">
                        <?php
                        $image_block_id = 1;
                        foreach ( $images as $image ) { ?>


                                <div class="upload-images-item">
                                    <div class="upload-images-item-inner">
                                        <div class="upload-images-item-crop " id="image-crop-item-<?=$image_block_id?>"></div>
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

                                                <div class="btn btn-success upload-images-action-add" data-toggle="tooltip" data-placement="left" title="Add">
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>


                            <script>
                                $(function(){
                                    setTimeout( function () {
                                            $('#image-crop-item-<?=$image_block_id?>').croppie({
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
                                                url:'<?=$image?>'
                                            });
                                        }
                                        , 400
                                    )

                                })
                            </script>
                        <?php
                        $image_block_id++;
                        } ?>

                        </div>

                        <button type="submit" name="geo-point-update-images" class="btn btn-success geo-point-update-images">Update images</button>

                    </div>

                <?php } ?>


            </div>




        <?php } ?>

        </form>

        <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

            <button type="submit" name="geo-point-update" class="btn btn-success geo-point-update">Update geo point</button>


        </div>

    </div>
<?php }else{
    echo 'No data!';
} ?>
