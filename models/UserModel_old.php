<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Datausers;

class UserModel extends  Model {

    public $http_address =  'http://88.99.189.111:8080';
    public $path_auth_sms = '/auth/sms/';
    public $path_auth_token = '/auth/token/';

    public $number_format =  '380930000001';

    public function generatePhoneNumber () {

        $return_number = false;

        $users_count = Datausers::find()->count();

        if( $users_count ){
            $return_number = $this->number_format + $users_count;
        }else{
            $return_number = $this->number_format;
        }

        return $return_number;
    }

    public function getSmsCode( $number ) {

        if( !$number){
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->http_address . $this->path_auth_sms,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $number,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            //echo "cURL Error #:" . $err;
            $returnSms = false;
        } else {
            //echo $response;
            $returnSms = $response;
        }

        return $returnSms;

    }

    public function addNewUser(){

        $return = false;
        $new_phone_number =  $this->generatePhoneNumber();
        if( $new_phone_number ){
            $sms_code = $this->getSmsCode($new_phone_number);
            if( $sms_code ){
                $token = $this->getUserToken($new_phone_number,$sms_code );


                if( $token ){


                    $profile_id =  $this->createUserProfile($token);

                    if( isset($profile_id) and !empty($profile_id) ){
                        $new_user = new Datausers();
                        $new_user->phone = (string)$new_phone_number;
                        $new_user->profile_id = (int)$profile_id;
                        $new_user->token = (string)$token;
                        $new_user->save();
                        $return = (int)$profile_id;
                    }

                }

            }
        }
        return $return;
    }


    public function addEvent( $data, $token ){

        if( !$token and !$data ){
            return false;
        }

        $new_data = false;
        $end_point = false;

        switch($data['type']){

            case 'PROFILE':

                if(
                    isset( $data['lat'] ) and !empty( $data['lng'] ) and
                    isset( $data['lat'] ) and !empty( $data['lng'] ) and
                    isset( $data['lastVisit'] ) and !empty( $data['lastVisit'] )
                ){

                    $end_point = 'profile';

                    $last_visit = str_replace(' ','T',$data['lastVisit']);

                    $new_data = array(
                        'latitude' =>  $data['lat'],
                        'longitude' =>  $data['lng'],
                        'lastVisit' => $last_visit,
                    );

                    $new_data = json_encode($new_data);

                }

                break;

            case 'WAS_HERE':

                if(
                    isset( $data['lat'] ) and !empty( $data['lng'] ) and
                    isset( $data['lat'] ) and !empty( $data['lng'] ) and
                    isset( $data['images'] ) and !empty( $data['images'] ) and
                    isset( $data['description'] ) and !empty( $data['description'] )

                ){

                    $end_point = 'was-here';
                    //$images = array( substr( $data['images'], 0, -1) );
                    $images = array_filter( explode(',', $data['images']) );

                    $new_data = array(
                        'latitude' =>  $data['lat'],
                        'longitude' =>  $data['lng'],
                        'contentCard' => array(
                            'images' => $images,
                            'text'   => $data['description'],
                        ),
                    );

                    $new_data = json_encode($new_data);

                }


                break;

            case 'WILL_BE_HERE':


                if(
                    isset( $data['lat'] ) and !empty( $data['lng'] ) and
                    isset( $data['lat'] ) and !empty( $data['lng'] ) and
                    isset( $data['startTime'] ) and !empty( $data['startTime'] ) and
                    isset( $data['finishTime'] ) and !empty( $data['finishTime'] ) and
                    isset( $data['images'] ) and !empty( $data['images'] ) and
                    isset( $data['description'] ) and !empty( $data['description'] )

                ){

                    $end_point = 'will-be-here';

                    $startTime = str_replace(' ','T',$data['startTime']);
                    $finishTime = str_replace(' ','T',$data['finishTime']);

                    //$images = '[' . substr( $data['images'], 0, -1) . ']';
                    $images = array_filter( explode(',', $data['images']) );

                    $new_data = array(
                        'latitude' =>  $data['lat'],
                        'longitude' =>  $data['lng'],
                        'startTime' =>  $startTime,
                        'finishTime' =>  $finishTime,
                        'contentCard' => array(
                            'images' => $images,
                            'text'   => $data['description'],
                        ),
                    );

                    $new_data = json_encode($new_data);

                }

                break;

        }

        if( $new_data and $end_point){


            //return $new_data . 'END P:' . $end_point;

            $curl = curl_init();

            curl_setopt_array($curl, array(

                CURLOPT_URL => $this->http_address . "/geo-point/" . $end_point . "/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $new_data,
                CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache",
                    "Content-Type: application/json",
                    "X-Auth-Token:".$token
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                //echo "cURL Error #:" . $err;
            } else {
                //echo $response;
                return $response;
            }
        }

        return false;

    }

    public function updateUserProfile( $token, $data ){

        // RETURN PROFILE ID
        if( !$token and !$data ){
            return false;
        }

        $return = false;


        $curl = curl_init();

        $new_data = array(
            'fullName' => $data['fullName'],
            'nickname' => $data['nickname'],
            'locale' => $data['locale'],
            'city' => $data['city'],
        );

        $new_data = json_encode($new_data);


        curl_setopt_array($curl, array(

            CURLOPT_URL => $this->http_address . "/profile/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $new_data,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json",
                "X-Auth-Token:".$token
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            //echo "cURL Error #:" . $err;
            return $err;
        } else {

            $res_array = json_decode( $response, TRUE );
            if (json_last_error() === 0) {
                $return = true;

            }else{
                $return = false;
            }



        }

        return $return;

    }

    public function createUserProfile( $token ){

        // RETURN PROFILE ID
        if( !$token ){
            return false;
        }

        $return = false;

        $curl = curl_init();

        $data = array(
            'fullName' => '',
            'nickname' => '',
            'locale' => '',
            'city' => '',
        );
        $data = json_encode($data);

        curl_setopt_array($curl, array(

        CURLOPT_URL => $this->http_address . "/profile/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "X-Auth-Token:".$token
           ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
             //echo "cURL Error #:" . $err;
            return $err;
        } else {

            $res_array = json_decode( $response, TRUE );
            if (json_last_error() === 0) {
                // JSON is valid

                $return = (int)$res_array['id'];
            }else{
                $match = '';
                $exist_id = preg_match('/\[([^\]]*[0-9])/', $response,$match);
                if( $exist_id ){
                    $return = (int)$match[1];
                }

            }



        }

        return $return;

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

            CURLOPT_URL => $this->http_address . "/image/",
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
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            //return "cURL Error #:" . $err;
            return false;
        } else {

            if( $response ){
                return $response;
            }
        }

    }


    public function getAllEvents( $token ){

        if( !$token){
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => $this->http_address . "/geo-point/",
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
            return $response;
        }


    }
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

    public function getEvent( $event_id , $token ){

        if( !$event_id and !$token ){
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => $this->http_address . "/geo-point/" . $event_id,
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
            return $response;
        }


    }

    public function getUserToken( $number, $code ) {

        if( !$number and !$code){
            return false;
        }

        $token = false;

       /* $data = '
            { 
                "phoneNumber": "' . $number . '",
                "activationCode":"' . $code . '"
            }
        ';*/
        $data = array(
            'phoneNumber' => $number,
            'activationCode' => $code,
        );
        $data = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->http_address . $this->path_auth_token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ),

        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            //echo "cURL Error #:" . $err;
        } else {
            //echo $response;
            $token = $response;
        }

        return $token;

    }

    public function getUserProfile($id, $token) {

        if( !$id and !$token){
            return false;
        }
        $return = false;

        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => $this->http_address . '/profile/' . $id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json",
                "X-Auth-Token:" . $token
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $return =  "cURL Error #:" . $err;
        } else {
            $return = $response;
        }

        return $return;

    }


}
