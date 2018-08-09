<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Datausers;

class ImageDataModel extends  ServerModel  {


    public function getImage( $image_id , $token , $size = 'preview' ){

        if( !$image_id and !$token ){
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => $this->http_address . "/image/" . $size . "/" . $image_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "X-Auth-Token:" . $token
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            //echo "cURL Error #:" . $err;
            return false;
        } else {
            //echo $response;
            $base64 = 'data:image/jpeg;base64,' . base64_encode($response);
            return $base64;
        }


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
            return false;
        }
        if( $httpcode == 200 ){
            return str_replace('"','',$response);

        }

        return false;

    }



}
