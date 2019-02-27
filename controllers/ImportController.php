<?php

namespace app\controllers;

use app\components\XLSXWriter;
use app\models\PointDataModel;
use app\models\UserDataModel;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Datausers;
use app\models\UserModel;
use yii\web\UploadedFile;
use app\components\SimpleXLSX;


class ImportController extends MainController
{
    protected $import_dir = 'import/';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {

        if( $action->id === 'index' and Yii::$app->request->isPost){
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }



    public function actionInit() {



        if( Yii::$app->request->isAjax){


            $result_array = [
                'user' => false,
                'profile' => false,
                'bases' => false,
            ];


            $data = Yii::$app->request->post();

            if( $data ){



                $userDataModel = new UserDataModel();

                $data_profile = [
                    'fullName'  =>  ( isset( $data['user_name'] ) ) ? $data['user_name'] : 'NO name' ,
                    'city'      => ( isset( $data['city'] ) ) ? $data['city'] : 'NO city' ,
                    'locale'    => ( isset( $data['locale'] ) ) ? $data['locale'] : 'ru' ,
                    'visible'   =>  ( isset( $data['visible_status'] ) and  $data['visible_status'] == true  ) ? 'true' : 'false' ,
                ];
                //return var_dump( $data['profile_coordinates']);
                $newUser = $userDataModel->addNewUser( $data['phone'], $data_profile  );

                $coordinates = false;
                if( is_array($newUser) ){
                    $result_array['user'] = true;
                    $result_array['profile'] = true;



                    if( isset( $newUser['token'] ) ) {
                        $profile_coordinates  = json_decode( $data['profile_coordinates'], true );

                        if( $data['visible_status'] == true){
                            $visibility = $userDataModel->updateProfileVisibility( $newUser['token'], true );
                            if( $visibility ){
                                $result_array['profile_visibility'] = $visibility['status'];
                            }
                        }


                        $coordinates = $userDataModel->updateProfileCoordinates( $newUser['token'], $profile_coordinates['lat'], $profile_coordinates['lng'] );
                        if( $coordinates ){
                            $result_array['profile_coordinates'] = $coordinates['status'];
                        }
                        if( $data['base'] ){

                            $data_bases  = json_decode($data['base'], true );
                            $point = new PointDataModel();

                            if( is_array( $data_bases ) ){

                                foreach ($data_bases as $base_item ) {
                                    $base_item_data = [
                                        'lat' => $base_item['lat'],
                                        'lng' => $base_item['lng'],
                                        'title' => $base_item['title'],


                                    ];

                                    $new_base = $point->createBase( $base_item_data, $newUser['token'] );

                                    if( isset($new_base) and $new_base != false  ){
                                        $result_array['bases'][] = json_encode($new_base);
                                    }


                                }
                            }

                        }

                        /* if( $data['base'] ){

                             $data_bases  = json_decode($data['base'], true );
                             $point = new PointDataModel();

                             if( is_array( $data_bases ) ){

                                 foreach ($data_bases as $base_item ) {
                                     $base_item_data = [
                                         'type' => 'PROFILE',
                                         'lat' => $base_item['lat'],
                                         'lng' => $base_item['lng'],
                                         'lastVisit' => strtotime( $base_item['last_visit']),

                                     ];

                                     $new_base = $point->createPoint( $base_item_data, $newUser['token'] );

                                     if( isset($new_base) and $new_base != false  ){
                                         $result_array['bases'][] = json_encode($new_base);
                                     }


                                 }
                             }

                         }*/

                    }
                }
                //return var_dump($coordinates);

                //return var_dump($newUser);
                if( is_array($newUser) ){

                    $result_array['user'] = true;

                    if( isset( $newUser['token'] ) ) {

                       /* $data_update_profile = [
                            'fullName'  =>  ( isset( $data['user_name'] ) ) ? $data['user_name'] : 'NO name' ,
                            'city'      => ( isset( $data['city'] ) ) ? $data['city'] : 'NO city' ,
                            'locale'    => ( isset( $data['locale'] ) ) ? $data['locale'] : 'ru' ,
                            'visible'   =>  ( isset( $data['visible_status'] ) and  $data['visible_status'] == true  ) ? 'true' : 'false' ,
                        ];
                        $user_profile = $userDataModel->updateUserProfile( $newUser['token'], $data_update_profile);
                        $result_array['profile'] = $user_profile['status'];


                        if( $data['base'] ){
                            $data_bases  = json_decode($data['base'], true );

                            $point = new PointDataModel();
                            if( is_array( $data_bases ) ){
                                foreach ($data_bases as $base_item ) {
                                    $base_item_data = [
                                        'type' => 'PROFILE',
                                        'lat' => $base_item['lat'],
                                        'lng' => $base_item['lng'],
                                        'lastVisit' => strtotime( $base_item['last_visit']),

                                    ];

                                    $new_base = $point->createPoint( $base_item_data, $newUser['token'] );

                                    if( isset($new_base) and $new_base != false  ){
                                        $result_array['bases'][] = json_encode($new_base);
                                    }


                                }
                            }

                        }*/

                    }
                }

            }
            return json_encode($result_array);
        }


        return false;


    }



    public function actionIndex()
    {





        $upload_status = false;

        if( Yii::$app->request->isPost){

            $upload_status = [
                'status' => false,
                'error' => '',
                'error_type' => ''
            ];

            $files = scandir($this->import_dir);

            if ( !in_array($_FILES["csv-file"]['name'], $files) ) {

                if( $_FILES["csv-file"]['type'] ==  "application/octet-stream" and  strpos( $_FILES["csv-file"]['name'], '.xlsx' ) !== false ){
                    $file = UploadedFile::getInstanceByName('csv-file');
                    if( isset( $file ) ){
                        $file->saveAs($this->import_dir . $file->baseName . '.' . $file->extension);
                        $upload_status['status'] = true;
                    }
                }else{

                    $upload_status['status'] = false;
                    $upload_status['error'] = 'Error type! Select only XLSX';
                    $upload_status['error_type'] = 'danger';

                }

            }else{
                $upload_status['status'] = false;
                $upload_status['error'] = 'File exist!';
                $upload_status['error_type'] = 'warning';
            }

        }



        $return_files = array();
        $files = scandir($this->import_dir);

        foreach ($files as $file_import ) {
            if( strpos( $file_import, '.xlsx' ) !== false ){
                $return_files[] = $file_import;
            }
        }

        return $this->render('index',[
            'import_files' => $return_files,
            'upload_file' => $upload_status
        ]);


    }

    public function actionGenerateMarkers() {

        $data = false;
        $dataMarkers = false;
        if (Yii::$app->request->isPost) {

            $data = Yii::$app->request->post();
            $dataMarkers = $this->generateInit($data['markersCoordinates'], $data['markerSection']);
        }
        //$dataMarkers = $this->generateInit();
        return $this->render('generate-markers',[
            'data' => $data,
            'markers' => $dataMarkers,
        ]);
    }


    public function actionGenerate()
    {

        $file = false;

        if (Yii::$app->request->isPost) {

            $data = Yii::$app->request->post();

            if(
                isset($data['base-count']) and !empty($data['base-count']) and
                isset($data['file-name']) and !empty($data['file-name']) and
                isset($data['user-count']) and !empty($data['user-count']) ){

                $gen_array = $this->generateXmlFile($data['user-count'], $data['base-count']);


                try {

                    $writer = new XLSXWriter();
                    $writer->writeSheet($gen_array);
                    $writer->writeToFile('import/' . $data['file-name'] .'.xlsx');
                    $file = $data['file-name'] .'.xlsx';

                } catch ( Exception $exception ) {
                    throw new \ErrorException('Файл импорта не создался!' . $exception);
                }


            }


        };


        return $this->render('gen',[
            'data' => $file,
        ]);


    }



    protected function XlsxToValidArray( $file_name ){

        $return_data = [
            'status' => false,
            'header_keys' => '',
            'data' => '',
            'error' => '',
            'error_array' => '',
        ];
        
        $xlsx = new SimpleXLSX( './' . $this->import_dir . $file_name );

        // Get first worksheet id
        $first_worksheet_id =  key($xlsx->sheetNames());
        
        if( !isset($xlsx) or !isset($first_worksheet_id) ){
            $return_data['error'] = 'SimpleXLSX error';
            return $return_data;
        }
        
        $xlsx_array = $xlsx->rows($first_worksheet_id);

        if( is_array($xlsx_array)) {


            // keys we need . And its sort
            $validation_keys = [
                'phone',
                'user_name',
                'locale',
                'city',
                'visible_status',
                'base',
                'profile_coordinates',
            ];

            // visible_status  we need
            $validation_visible_status = [
                '0',
                '1',
            ];

            // Base data we need
            $validation_base = [
                'lat',
                'lng',
                'title',
            ];

            // Locale  we need
            $validation_locale = [
                'UKRAINIAN',
                'GERMAN',
                'ENGLISH',
                'FRENCH',
                'SPANISH',
                'ITALIAN',
                'RUSSIAN',
            ];




            // exist diff with keys
            $diff_valid = array_diff($validation_keys, $xlsx_array['0']);
            
            if( is_array($diff_valid) and count($diff_valid) > 0 ){
                $return_data['error'] = 'Validation header error !';
                $return_data['error_array'] = $diff_valid;
                return $return_data;
            }
            
            
            // Change key and value
            $keys_header = array_flip($xlsx_array['0']);

            // Unset header
            unset($xlsx_array['0']);


            $data_xlsx = array();

            foreach ($xlsx_array as $xlsx_item) {

                $data_xlsx_row = array();

                foreach ( $validation_keys as $key ) {

                    $key_head = $keys_header[$key];

                    if ( isset( $xlsx_item[$key_head] )  ) {


                        $data_xlsx_row[$key]['data'] = $xlsx_item[$key_head];
                        $data_xlsx_row[$key]['valid'] = false;

                        // Validation data
                        switch ($key) {

                            case 'phone':

                                $data_xlsx_row[$key]['valid'] = true;

                                break;

                            case 'user_name':

                                $data_xlsx_row[$key]['valid'] = true;

                                break;

                            case 'locale':

                                if (in_array($xlsx_item[$key_head], $validation_locale)) {
                                    $data_xlsx_row[$key]['valid'] = true;
                                }

                                break;

                            case 'city':

                                $data_xlsx_row[$key]['valid'] = true;

                                break;
                            case 'profile_coordinates':

                                $profile_coordinates_decode = json_decode($xlsx_item[$key_head], true);
                                if( is_array($profile_coordinates_decode) and !empty($profile_coordinates_decode['lat']) and  $profile_coordinates_decode['lng'] ){
                                    $data_xlsx_row[$key]['valid'] = true;
                                    $data_xlsx_row[$key]['data']  = $profile_coordinates_decode;
                                }

                                break;
                            case 'visible_status':

                                if ( in_array($xlsx_item[$key_head], $validation_visible_status)) {
                                    $data_xlsx_row[$key]['valid'] = true;
                                }

                                break;

                            case 'base':


                                $bases = explode(';', $xlsx_item[$key_head]);

                                if (is_array($bases) and count($bases) > 0) {

                                    $data_xlsx_row[$key]['data'] = array();

                                    foreach ($bases as $base) {
                                        if (isset($base) and !empty($base)) {
                                            $decode_base = json_decode($base, true);
                                            if ($decode_base and !is_null($decode_base)) {

                                                $decode_base_array = array();
                                                foreach ( $decode_base as $key_base => $decode_base_item) {
                                                    if (in_array( $key_base, $validation_base)) {
                                                        $decode_base_array[$key_base] = $decode_base_item;
                                                    }
                                                }
                                                if ( count($decode_base_array) == count($validation_base) ) {
                                                    $data_xlsx_row[$key]['data'][] = $decode_base_array;
                                                    $data_xlsx_row[$key]['valid'] = true;
                                                }

                                            }

                                        }
                                    }
                                }

                                break;

                        }

                    }



                }
                $data_xlsx[] = $data_xlsx_row;

            }

            $return_data['status'] = true;
            $return_data['header_keys'] = $validation_keys;
            $return_data['data'] = $data_xlsx;


        }

        return $return_data;

    }



    public function actionCheck()
    {


        if( Yii::$app->request->isAjax){

            $data = Yii::$app->request->post();

            if( $data['file_name'] ){

                $return_array = $this->XlsxToValidArray( $data['file_name']  );

                if( is_array($return_array) ){

                    return $this->renderAjax('block/check',[
                        'data_array' => $return_array,
                    ]);
                }

            }



        }
        return false;



    }






    protected function generateUserNames( $names_array , $surname, $count = 100 ){

        if(
            is_array($names_array) and is_array($surname) and
            count($names_array) > 0 and count($surname) > 0  )
        {
            $result_array = array();
            for( $i = 1; $i <= $count ; $i++ ){
                $result_array[] = $names_array[array_rand($names_array, 1)] . ' ' .  $surname[array_rand($surname, 1)];
            }
            return $result_array;
        }

        return false;
    }

    protected function generateAddresses( $args = array() ){


        $api_url = 'https://api.foursquare.com/v2/venues/explore';
        $default_args = [
            'client_id' => 'YPEQBLY4CXWUXQYU3BPUOSG2VSRV0DEPNOH1VZFBX2XI4IDO',
            'client_secret' => '14NPWVABOLT3TSXP4ZGFBNS50ER5ILO1SXY3O4OYE1HDSWPK',
            //'client_id' => 'KHTR2DNXDI0C1BVONHO5E554BVPCHTGBBD54EJA0UM5CMF3P',
            //'client_secret' => 'VRZOHJ3G0JZJDQ4VM1BES1KM04AR3YTPGTQDGVKWXVARJS4A',
            'll' => '50.457527,30.514467',
            'radius' => 100000,
            'v' => '20180323',
            'limit' => 100,
            'offset' => 0,
        ];


        $address_array = array();

        $arguments = array_merge( $default_args, $args);




        if( is_array($arguments)){

            $url_args = '';
            foreach ($arguments as $key => $value ) {
                $url_args .= '&' . $key . '=' . $value;
            }
            $url_args = ltrim($url_args, '&');
            if( !empty($url_args) ){
                $request_url = $api_url . '?' . $url_args;
            }else{
                $request_url = $api_url;
            }

            // https://api.foursquare.com/v2/venues/explore?client_id=YPEQBLY4CXWUXQYU3BPUOSG2VSRV0DEPNOH1VZFBX2XI4IDO&client_secret=14NPWVABOLT3TSXP4ZGFBNS50ER5ILO1SXY3O4OYE1HDSWPK&ll=50.60744894668227,30.270378721332662&radius=100000&v=20180323&limit=100&offset=0%C2%A7ion=food


            $geo_file = file_get_contents($request_url);

            if( $geo_file !== false and !is_null($geo_file) ){
                $geo_file = json_decode($geo_file, true);

                //echo $geo_file;
                //return $geo_file;

                if( $geo_file !== false ) {
                    if ($geo_file['meta']['code'] == 200 and count($geo_file['response']) > 0) {

                        $file_array = $geo_file['response']['groups'][0]['items'];
                        if (is_array($file_array)) {

                            foreach ($file_array as $item) {

                                $val_address = $item['venue']['location']['lat'] . ',' . $item['venue']['location']['lng'];
                                if( !in_array( $val_address, $address_array ) ){
                                    $str = $item['venue']['location']['lat'] . ',' . $item['venue']['location']['lng'] . ',' . $item['venue']['name'];
                                    $address_array[] =  mb_convert_encoding($str, "UTF-8", "auto"); ;
                                }
                            }

                            return array_unique($address_array);


                        }
                    }
                }
            }

        }

        return false;
    }

    protected function generateInit($points, $sections){
        if( !is_array($points) or !is_array($sections) ){
            return false;
        };
        /*$kiev_near_points = array(
            '50.543506925300534,30.398525070236104',
            '50.55077743564644,30.47518404684348',
            '50.49986031825645,30.35962350002265',
            '50.45690178707932,30.40195756172139',
            '50.48967030139309,30.503788142605487',
            '50.38035357370674,30.531248074533977',
            '50.50568219813428,30.653673604357806',
            '50.44087335748691,30.647952785215466',
            '50.393484910619776,30.66625940649783',
            '50.415362392259254,30.756648349066012',
        );
        $kiev_near_points2 = array(
            '50.60744894668227,30.270378721332662',
            '50.52605312618934,30.395092578803826',
            '50.435772263924065,30.470607391606052',
            '50.35188986858404,30.54726636822201',
            '50.51441367230774,30.677701044854075',
            '50.44433918969031,30.684898474059736',
            '50.35546185208676,30.742065519635986',
            '50.32670407709682,30.060842929587295',
            '50.34410673937825,31.087856429921544',
        );
        $sections = array(
            'food',
            'drinks',
            'coffee',
            'shops',
            'arts',
            'outdoors',
            'sights',
            'trending',
            'nextVenues'
        );*/
        $array_address = array();

        foreach ($points as $point ) {
            foreach ($sections as $section) {
                $array_address = array_merge($array_address, $this->generateAddresses(
                    [
                        'll' => $point,
                        'section' => $section,
                    ]
                ) );
            }
        }
        /*   for( $i = 1; $i <= 1; $i++ ){
        $array_address = array_merge($array_address, generateAddresses(
                [
                    'section' => 1,
                ]
        ) );
        }*/

        if( is_array($array_address) and count($array_address) > 0 ){
            //return $array_address;
            if( file_put_contents('gen-data/address-2-data.json', utf8_encode(json_encode( $array_address )) ) ) {
                return count($array_address);
            };

        }
        return false;
    }

    protected function generateXmlFile( $count_users, $max_base ){

        $russian_names = file_get_contents('./gen-data/names.json');
        $russian_names_arrays =  json_decode($russian_names, true);
        $names = \yii\helpers\ArrayHelper::map($russian_names_arrays,'ID','Name');


        $russian_surnames = file_get_contents('./gen-data/surnames.json');
        $russian_surnames_arrays =  json_decode($russian_surnames, true);
        $surnames = \yii\helpers\ArrayHelper::map($russian_surnames_arrays,'ID','Surname');


        $address = file_get_contents('./gen-data/address-data.json');
        $address_array =  json_decode($address, true);

        $default_row = [
            'phone' => 0,
            'user_name' => 1,
            'locale' => 2,
            'city' => 3,
            'visible_status' => 4,
            'base' => 5,
            'profile_coordinates' => 6,
        ];



        $generate_array = array();
        $generate_array[] = array_flip($default_row);


        $phone_mask = '38000000001';
        for( $i = 1; $i <= $count_users; $i++ ){

            $base_array = array();
            for( $base_cnt = 1; $base_cnt <= $max_base; $base_cnt++ ){

                $base_random_item = explode(',', $address_array[array_rand($address_array, 1)] );
                $base_array[] = json_encode([
                    'lat' => $base_random_item[0],
                    'lng' => $base_random_item[1],
                    'title' => $base_random_item[2],
                ]);

            }

            $base_in_row = implode(';', $base_array);

            $profile_coordinates_item = explode(',', $address_array[array_rand($address_array, 1)] );
            $profile_coordinates = json_encode([
                'lat' => $profile_coordinates_item[0],
                'lng' => $profile_coordinates_item[1],
            ]);


            $generate_array[] =  [
                $default_row['phone'] => $phone_mask++,
                $default_row['user_name'] =>  $names[array_rand($names, 1)] . ' ' .  $surnames[array_rand($surnames, 1)],
                $default_row['locale'] => 'RUSSIAN',
                $default_row['city'] => 'Kiev',
                $default_row['visible_status'] => '1',
                $default_row['base'] => $base_in_row,
                $default_row['profile_coordinates'] => $profile_coordinates,

            ];

            //var_dump($generate_array);


        }

        return $generate_array;


    }



}
