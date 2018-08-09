<?php

namespace app\controllers;

use app\models\ImageDataModel;
use app\models\PointDataModel;
use Yii;
use yii\web\Controller;
use app\controllers\MainController;


class ImageController extends Controller {


    public function actionCreate()
    {
        if (Yii::$app->request->isAjax and Yii::$app->request->post()) {

            $data = Yii::$app->request->post();
            if( isset( $_FILES['image']['tmp_name'] ) and !empty( $_FILES['image']['tmp_name'] )
                and
                isset( $data['token'] ) and !empty( $data['token'] ) ) {

                $UserDataModel = new ImageDataModel();
                $image_id = $UserDataModel->addImageToServer($_FILES['image']['tmp_name'] ,  $data['token'] );

                if( $image_id ) {
                    return $image_id;
                }
            }
        }

        return false;


    }


}
