<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Datausers;

class ImageDataModel extends  ServerModel  {


    public function getImage( $image_id , $token , $size = 'preview' ){

        if( !$image_id and !$token){
            return false;
        }

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            '/image/' . $size . '/' . $image_id,
            $token
        );

        return $return_data;



    }

    public function addImageToServer( $file_image_tmp_name, $token ){

        if( !$file_image_tmp_name ){
            return false;
        }

        $content = file_get_contents( $file_image_tmp_name );
        $boundary = uniqid();

        $data = '';
        $eol = "\r\n";
        $delimiter = '----' . $boundary;
        $data .= "--" . $delimiter . $eol
            . 'Content-Disposition: form-data; name="image"; filename="' . $_FILES['image']['tmp_name'] . '"' . $eol
            . 'Content-Type: image/jpeg'.$eol
        ;
        $data .= $eol;
        $data .= $content . $eol;
        $data .= $eol . "--" . $delimiter . "--";


        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => $this->SERVER_PUBLIC_ADRESS . "/image/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "X-Auth-Token:".$token,
                "Content-Type: multipart/form-data; boundary=" . $delimiter,
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            //return 'CODE:' . $httpcode . ' RESP: ' . $response . ' ERROR: ' .  $err;
            return false;
        }
        if( $httpcode == 200 ){
            $data = json_decode($response, true);
            if( isset( $data['id'] ) ){
                return $data['id'];
            }
        }
        return false;
        //return 'CODE:' . $httpcode . ' RESP: ' . $response . ' ERROR: ' .  $err;

    }



}
