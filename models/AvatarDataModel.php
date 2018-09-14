<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Datausers;

class AvatarDataModel extends  ServerModel  {


    public function getAvatar( $image_id , $token , $size = 'preview' ){

        if( !$image_id and !$token ){
            return false;
        }

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            '/avatar/'  . $size . '/' . $image_id,
            $token
        );

        return $return_data;

    }

    public function setAvatar( $file_image_tmp_name, $token ){

        if( !$file_image_tmp_name ){
            return false;
        }

        $return_data = [
            'status' => true,
            'data' => '',
            'error' => '',
        ];

        $file_name = $file_image_tmp_name;

        $content = file_get_contents( $file_image_tmp_name );



        $boundary = uniqid();

        $data = '';
        $eol = "\r\n";
        $delimiter = '----' . $boundary;
        $data .= "--" . $delimiter . $eol
            . 'Content-Disposition: form-data; name="avatar"; filename="' . $file_name . '"' . $eol
            . 'Content-Type: image/jpeg'.$eol
        ;
        $data .= $eol;
        $data .= $content . $eol;
        $data .= $eol . "--" . $delimiter . "--";


        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => $this->SERVER_PUBLIC_ADRESS . "/avatar/",
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
            $return_data['status'] = false;
            $return_data['error'] = $err . '  CODE : '. $httpcode;
        }
        if( $httpcode == 200 ){
            $return_data['data'] = json_decode( $response, TRUE );;
        }else{
            $return_data['status'] = false;
            $return_data['error'] = $err . '  CODE : '. $httpcode;
        }

        return $return_data;

    }



}
