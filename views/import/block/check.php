<?php

/* @var $this yii\web\View */


$error_count = 0;
?>



<?php if( is_array( $data_array )){ ?>

    <?php if( $data_array['status'] === false  ) { ?>

        <div class="alert alert-danger" role="alert" >
            <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
            <span class="sr-only">Error! Error upload!</span>
            <?=$data_array['error']?>
            <?php if( is_array( $data_array['error_array'] ) ) { ?>
                <pre>
                    <?php print_r( $data_array['error_array'] );?>
                </pre>
            <?php } ?>
        </div>

    <?php } ?>

    <?php if( $data_array['status'] === true  ) { ?>

        <table class="table table-striped">
            <?php if( is_array($data_array['header_keys'] ) ) { ?>
                <thead>
                    <tr>
                        <?php foreach ( $data_array['header_keys'] as $header_key ) { ?>
                            <th>
                                <?=$header_key?>
                            </th>
                        <?php } ?>

                        <th>
                            Import status
                        </th>
                    </tr>
                </thead>
            <?php } ?>

            <tbody>


                <?php foreach ( $data_array['data'] as $data ) { ?>
                    <tr class="item-import" >





                    <?php foreach ( $data_array['header_keys'] as $header_key ) {
                        if( $data[$header_key]['valid'] === false) {
                            $error_count++;
                        }
                        ?>

                        <td class="<?= ($data[$header_key]['valid'])? 'success' : 'danger' ?>"   >
                            <?php

                            switch($header_key){
                                case 'base':

                                    $base_data_json =  htmlspecialchars( json_encode($data[$header_key]['data']), ENT_QUOTES, 'UTF-8');
                                    ?>

                                    <div class='import-data-item' data-name='<?=$header_key?>'  data-value='<?=$base_data_json?>'  ></div>

                                    <?php
                                    foreach ( $data[$header_key]['data'] as $bases ) {
                                        foreach ($bases as $base_pror => $base_val) {

                                            echo  $base_pror . ' : ' . $base_val . '<br>';
                                        }
                                        echo '</hr>';
                                    }
                                    break;

                                default:
                                    echo '<div class="import-data-item" data-name="'. $header_key .'" data-value="'. $data[$header_key]['data'] .'"  ></div>';
                                    echo $data[$header_key]['data'];
                                    break;
                            }


                            ?>
                        </td>

                    <?php } ?>

                        <td class="import-status">

                        </td>
                    </tr>
                <?php } ?>

            </tbody>
        </table>



        <?php if( $error_count > 0  ) { ?>

            <div class="alert alert-danger" role="alert" >
                <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
                <span class="sr-only">Error! Error upload!</span>
                File has validation errors !
                Count errors: <?=$error_count?>
            </div>

        <?php }else{ ?>
            <div class="alert alert-success" role="alert" >
                <span class="glyphicon glyphicon-saved" aria-hidden="true"></span>
                <span class="sr-only">success file!</span>
                File  ready to import ! </br>
                Count row: <b> <?php echo count($data_array['data'])?></b>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success import-all-file">START IMPORT</button>
            </div>
        <?php } ?>

    <?php } ?>

<?php } ?>



