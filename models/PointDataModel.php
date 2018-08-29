<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Datausers;

class PointDataModel extends  ServerModel {


    public function createPoint( $data, $token ){

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


                    // TODO remove when fix bug with type
                    //$new_data['ownerId'] =  $data['id'];
                    //$new_data['type'] =  $data['type'];

                    // End


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
                            'imageIds' => $images,
                            'text'   => $data['description'],
                        ),
                    );



                    // DONE remove when fix bug with type
                    //$new_data['ownerId'] =  $data['id'];
                    //$new_data['type'] =  $data['type'];
                    // End

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
                            'imageIds' => $images,
                            'text'   => $data['description'],
                        ),
                    );


                    // DONE remove when fix bug with type
                    //$new_data['ownerId'] =  $data['id'];
                    //$new_data['type'] =  $data['type'];
                    // End

                    $new_data = json_encode($new_data);

                }

                break;

        }

        if( $new_data and $end_point){



            $return_data = $this->curlRequest(
                $this->SERVER_PUBLIC_ADRESS,
                "/geo-point/" . $end_point,
                $token,
                'POST',
                $new_data
            );


            if( is_array($return_data) and $return_data['status'] === true ){
                return  $return_data['data'];
            }



            /*$curl = curl_init();

            curl_setopt_array($curl, array(

                CURLOPT_URL => $this->SERVER_PUBLIC_ADRESS . "/geo-point/" . $end_point . "",
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
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($err) {
                return $err;
            } else {
                if( $httpcode == 200 ){
                    return $response;
                }else{
                    return 'CODE: ' . $httpcode;
                }
            }*/
        }

        return false;

    }


    // Get point data by id. @type set endpoint
    public function getPointById( $id , $token , $type ){

        if( !$id and !$token){
            return false;
        }

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            '/geo-point/' . strtolower($type) . '/' . $id,
            $token
        );

        return $return_data;


    }

    public function getPoints(  ){


        $return_data = $this->curlRequest( $this->SERVER_PROTECTED_ADRESS,"/geo-point" );

        if( is_array($return_data) and $return_data['status'] === true ){
            $return_data['data'] = json_decode($return_data['data'], TRUE);
        }

        return $return_data;


        /*if( !$token){
            return false;
        }*/
/*
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
        }*/


    }




}
