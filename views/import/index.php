<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
$this->title = 'Users import';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="content-window-title">
    <?=$this->title?>
</div>
<div class="content-window-inner">
    <div class="content-window-inner">


        <?php if( is_array($upload_file) ) { ?>

            <?php if( $upload_file['status'] === false  ) { ?>
                <div class="alert alert-<?=$upload_file['error_type'] ?>" role="alert" >
                    <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error! Error upload!</span>
                    <?=$upload_file['error']?>
                </div>
            <?php } ?>

            <?php if( $upload_file['status'] === true  ) { ?>
                <div class="alert alert-success" role="alert" >
                    <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
                    <span class="sr-only">Success upload!</span>
                    Success upload!
                </div>
            <?php } ?>
        <?php } ?>


        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>File</label>
                <input type="file" class="form-control" name="csv-file" value="">
            </div>
            <div class="form-group">
                    <button type="submit" class="btn btn-primary">LOAD FILE</button>
            </div>
        </form>



        <?php if( is_array($import_files)){?>
            <hr>
            <div class="form-group">
                <label>Existing csv files:</label>
                <select id="imported-files" name="imported-files" class="form-control">
                    <option value="" selected> Select file</option>
                    <?php foreach ($import_files as $imported_file ){
                        echo '<option value="' . $imported_file . '">' . $imported_file . '</option>';
                    } ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-warning check-selected-csv-file">Check file</button>
            </div>

        <?php } ?>

       <div class="check-import-file">

       </div>



    </div>

</div>

