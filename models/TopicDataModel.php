<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Datausers;

class TopicDataModel extends  ServerModel  {


    public function getTopics(  $token  ){

        if( !$token ){
            return false;
        }
         $return_data = $this->curlRequest(
             $this->SERVER_PUBLIC_ADRESS,
             '/topic/',
             $token
         );
        return $return_data;

    }



}
