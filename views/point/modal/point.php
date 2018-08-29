<?php

?>
<pre>
    <?php print_r($point_data)?>
</pre>
<?php
exit();
if( $point_data ){?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">USER: <i><?php echo ($user_phone) ? $user_phone : $point_data['id'] ?></i></h4>
        
    </div>
    <div>
        <div class="modal-body">
            <?php

            foreach ($point_data as $event_key => $event_value) {
                if( $event_key == 'contentCard'){

                    if( $event_value['text']){
                        ?>
                        <div class="form-group">
                            <label for="text">Description</label>
                            <textarea  name="text" class="form-control"  id="text" placeholder="Text" disabled><?=$event_value['text']?></textarea>
                        </div>
                        <?php
                    }
                    ?>
                <?php }else{ ?>
                <div class="form-group">
                    <label for="<?=$event_key?>"><?=$event_key?></label>
                    <input type="text" name="<?=$event_key?>" class="form-control" value="<?=$event_value?>" id="<?=$event_key?>" placeholder="<?=$event_key?>" disabled>
                </div>
                <?php } ?>
            <?php } ?>

            <?php if( $images ){ ?>

                <label>Images</label>
                <div class="modal-event-images">
                    <?php
                    foreach ($images as $image) { ?>
                        <div class="modal-event-image-item">
                            <img src="<?=$image?>" alt="">
                        </div>
                    <?php } ?>
                </div>

            <?php } ?>
        </div>


        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
<?php }else{
    echo 'No data!';
} ?>
