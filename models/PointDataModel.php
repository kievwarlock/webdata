<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Datausers;

class PointDataModel extends  ServerModel {




    public function createBase( $data, $token ) {

        if( !$token and !$data ){
            return false;
        }

        $end_point = '/profile-base';

        if( isset( $data['lat'] ) and !empty( $data['lat'] ) and  isset( $data['lng'] ) and !empty( $data['lng'] ) ){

            $new_data = array(
                'latitude' =>  $data['lat'],
                'longitude' =>  $data['lng'],
                'title' =>  ( $data['title'] )? $data['title'] : '',
            );
            $new_data = json_encode($new_data);


            if( $new_data and $end_point){



                $return_data = $this->curlRequest(
                    $this->SERVER_PUBLIC_ADRESS,
                    $end_point,
                    $token,
                    'POST',
                    $new_data
                );


                if( is_array($return_data) and $return_data['status'] === true ){
                    return  $return_data['data'];
                }


            }


        }

        return false;
    }

    public function createPoint( $data, $token ){

        if( !$token and !$data ){
            return false;
        }



        $new_data = false;
        $end_point = false;

        switch($data['type']){

            case 'PROFILE':

                if(
                    isset( $data['lat'] ) and !empty( $data['lat'] ) and
                    isset( $data['lng'] ) and !empty( $data['lng'] ) and
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
                    isset( $data['description'] ) and !empty( $data['description'] ) and
                    isset( $data['tags'] ) and !empty( $data['tags'] )
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
                            'topicIds' => $data['tags'],
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
                    isset( $data['description'] ) and !empty( $data['description'] ) and
                    isset( $data['tags'] ) and !empty( $data['tags'] )

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
                            'topicIds' => $data['tags'],
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




    private function updateContentCard( string $idCard, string $token, array $data ){

        if( !$token or !$data or !$idCard ){
            return $return = array(
                'status' => false,
                'error' => 'No data !',
            );
        }

        $update_data = json_encode($data);

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            "/content-card/" . $idCard ,
            $token,
            'PUT',
            $update_data
        );

        return  $return_data;

    }

    public function updatePoint(  $id, $data, $token ){

        if( !$token or !$data or !$id ){
            return $return = array(
                'status' => false,
                'error' => 'No data or token or id',
            );
        }

        $return_update = array(
            'point' => false,
            'content' => false,
        );

        $new_data = false;
        $end_point = false;

        switch($data['type']){

            case 'PROFILE':

                if(
                    isset( $data['lat'] ) and !empty( $data['lat'] ) and
                    isset( $data['lng'] ) and !empty( $data['lng'] ) and
                    isset( $data['lastVisit'] ) and !empty( $data['lastVisit'] )
                ){

                    $end_point = 'profile';

                    $return_update['content'] = array(
                        'status' => true,
                        'data' => '',
                    );


                    $new_data = array(
                        'latitude' =>  $data['lat'],
                        'longitude' =>  $data['lng'],
                        'lastVisit' => $data['lastVisit'],
                    );

                    $new_data = json_encode($new_data);

                }

                break;

            case 'WAS_HERE':

                if(
                    isset( $data['lat'] ) and !empty( $data['lat'] ) and
                    isset( $data['lng'] ) and !empty( $data['lng'] ) and
                    isset( $data['imageIds'] ) and !empty( $data['imageIds'] ) and
                    isset( $data['description'] ) and !empty( $data['description'] ) and
                    isset( $data['tags'] ) and !empty( $data['tags'] )
                ){

                    $end_point = 'was-here';


                    if( is_array( $data['imageIds'] ) and count( $data['imageIds'] ) > 0 ){
                        $imagesId = $data['imageIds'];
                    }else{
                        $imagesId = array();
                    }


                    $new_data = array(
                        'latitude' =>  $data['lat'],
                        'longitude' =>  $data['lng'],
                    );

                    $contentCard = array(
                        'imageIds' => $imagesId,
                        'text'   => $data['description'],
                        'topicIds' => $data['tags'],
                    );


                    $new_data = json_encode($new_data);

                }


                break;

            case 'WILL_BE_HERE':


                if(
                    isset( $data['lat'] ) and !empty( $data['lat'] ) and
                    isset( $data['lng'] ) and !empty( $data['lng'] ) and
                    isset( $data['startTime'] ) and !empty( $data['startTime'] ) and
                    isset( $data['finishTime'] ) and !empty( $data['finishTime'] ) and
                    isset( $data['imageIds'] ) and !empty( $data['imageIds'] ) and
                    isset( $data['description'] ) and !empty( $data['description'] ) and
                    isset( $data['tags'] ) and !empty( $data['tags'] )

                ){


                    $end_point = 'will-be-here';

                    $startTime = str_replace(' ','T',$data['startTime']);
                    $finishTime = str_replace(' ','T',$data['finishTime']);

                    if( is_array( $data['imageIds'] ) and count( $data['imageIds'] ) > 0 ){
                        $imagesId = $data['imageIds'];
                    }else{
                        $imagesId = array();
                    }


                    $new_data = array(
                        'latitude' =>  $data['lat'],
                        'longitude' =>  $data['lng'],
                        'startTime' =>  $startTime,
                        'finishTime' =>  $finishTime,
                    );

                    $contentCard = array(
                        'imageIds' => $imagesId,
                        'text'   => $data['description'],
                        'topicIds' => $data['tags'],
                    );

                    $new_data = json_encode($new_data);

                }

                break;

        }


        if( $new_data and $end_point ){



            $return_data = $this->curlRequest(
                $this->SERVER_PUBLIC_ADRESS,
                "/geo-point/" . $end_point . "/" . $id ,
                $token,
                'PUT',
                $new_data
            );


            if( $return_data['status'] == true ){

                $return_update['point']['status'] = true;
                $return_update['point']['data'] = $return_data['data'];

                $update_data = json_decode( $return_data['data'], true );
                if( is_array( $update_data['contentCard'] ) and isset( $update_data['contentCard']['id'])  and isset($contentCard)  ){
                    $update_content_card = $this->updateContentCard(
                        $update_data['contentCard']['id'],
                        $token,
                        $contentCard
                    );
                    if( $update_content_card['status'] == true ){
                        $return_update['content']['status'] = true;
                        $return_update['content']['data'] = $update_content_card['data'];
                    }
                }
            }

            return $return_update;

        }


        $return_update['error'] = 'Not valid data!';
        return $return_update;


    }



    public function updateBase(  $id, $data, $token ){

        if( !$token or !$data or !$id ){
            return $return = array(
                'status' => false,
                'error' => 'No data or token or id',
            );
        }

        $return_update = array(
            'point' => false,
            'content' => false,
        );

        $new_data = false;
        $end_point = false;

        switch($data['type']){

            case 'PROFILE':

                if(
                    isset( $data['lat'] ) and !empty( $data['lat'] ) and
                    isset( $data['lng'] ) and !empty( $data['lng'] ) and
                    isset( $data['lastVisit'] ) and !empty( $data['lastVisit'] )
                ){

                    $end_point = 'profile';

                    $return_update['content'] = array(
                        'status' => true,
                        'data' => '',
                    );


                    $new_data = array(
                        'latitude' =>  $data['lat'],
                        'longitude' =>  $data['lng'],
                        'lastVisit' => $data['lastVisit'],
                    );

                    $new_data = json_encode($new_data);

                }

                break;

            case 'WAS_HERE':

                if(
                    isset( $data['lat'] ) and !empty( $data['lat'] ) and
                    isset( $data['lng'] ) and !empty( $data['lng'] ) and
                    isset( $data['imageIds'] ) and !empty( $data['imageIds'] ) and
                    isset( $data['description'] ) and !empty( $data['description'] ) and
                    isset( $data['tags'] ) and !empty( $data['tags'] )
                ){

                    $end_point = 'was-here';


                    if( is_array( $data['imageIds'] ) and count( $data['imageIds'] ) > 0 ){
                        $imagesId = $data['imageIds'];
                    }else{
                        $imagesId = array();
                    }


                    $new_data = array(
                        'latitude' =>  $data['lat'],
                        'longitude' =>  $data['lng'],
                    );

                    $contentCard = array(
                        'imageIds' => $imagesId,
                        'text'   => $data['description'],
                        'topicIds' => $data['tags'],
                    );


                    $new_data = json_encode($new_data);

                }


                break;

            case 'WILL_BE_HERE':


                if(
                    isset( $data['lat'] ) and !empty( $data['lat'] ) and
                    isset( $data['lng'] ) and !empty( $data['lng'] ) and
                    isset( $data['startTime'] ) and !empty( $data['startTime'] ) and
                    isset( $data['finishTime'] ) and !empty( $data['finishTime'] ) and
                    isset( $data['imageIds'] ) and !empty( $data['imageIds'] ) and
                    isset( $data['description'] ) and !empty( $data['description'] ) and
                    isset( $data['tags'] ) and !empty( $data['tags'] )

                ){


                    $end_point = 'will-be-here';

                    $startTime = str_replace(' ','T',$data['startTime']);
                    $finishTime = str_replace(' ','T',$data['finishTime']);

                    if( is_array( $data['imageIds'] ) and count( $data['imageIds'] ) > 0 ){
                        $imagesId = $data['imageIds'];
                    }else{
                        $imagesId = array();
                    }


                    $new_data = array(
                        'latitude' =>  $data['lat'],
                        'longitude' =>  $data['lng'],
                        'startTime' =>  $startTime,
                        'finishTime' =>  $finishTime,
                    );

                    $contentCard = array(
                        'imageIds' => $imagesId,
                        'text'   => $data['description'],
                        'topicIds' => $data['tags'],
                    );

                    $new_data = json_encode($new_data);

                }

                break;

        }


        if( $new_data and $end_point ){



            $return_data = $this->curlRequest(
                $this->SERVER_PUBLIC_ADRESS,
                "/geo-point/" . $end_point . "/" . $id ,
                $token,
                'PUT',
                $new_data
            );


            if( $return_data['status'] == true ){

                $return_update['point']['status'] = true;
                $return_update['point']['data'] = $return_data['data'];

                $update_data = json_decode( $return_data['data'], true );
                if( is_array( $update_data['contentCard'] ) and isset( $update_data['contentCard']['id'])  and isset($contentCard)  ){
                    $update_content_card = $this->updateContentCard(
                        $update_data['contentCard']['id'],
                        $token,
                        $contentCard
                    );
                    if( $update_content_card['status'] == true ){
                        $return_update['content']['status'] = true;
                        $return_update['content']['data'] = $update_content_card['data'];
                    }
                }
            }

            return $return_update;

        }


        $return_update['error'] = 'Not valid data!';
        return $return_update;


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

        return false;
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
