<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppMapAsset;
use app\assets\AppAsset;
use yii\widgets\Menu;


if( $this->context->module->requestedRoute ==  'point/index' ){
    AppMapAsset::register($this);
}else{
    AppAsset::register($this);
}


?>
<?php $this->beginPage() ?>


<!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>


</head>
<body>
<?php $this->beginBody() ?>

<header class="header">
    <div class="full-container">
        <div class="header-inner">
            <div class="mobile-nav-init" id="menu-init">
                <div class="mobile-nav-init-inner">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="header-logo">
                <img src="/web/img/LOGO.png" alt="Logo">
            </div>
            <div class="header-info">

                <div class="header-info-message">
                    <!--msg will be here-->
                   <!-- <img src="/web/img/icons/ic_message.svg" alt="message" title="messages">-->



                </div>
                <div class="header-info-user">
                    <?php
                    if(Yii::$app->user->isGuest){

                    }else{
                        $user = Yii::$app->user->identity;
                        $user_label = '<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>';
                        ?>
                        <div class="header-info-user-image">
                            <img src="/web/img/user-avatar.jpg" alt="">
                        </div>
                        <div class="header-info-user-name">
                           <?php echo $user->username;?>
                        </div>
                        <?php
                    }
                    ?>



                </div>


            </div>
        </div>
    </div>
</header>

<main class="main">

    <div class="main-sidebar">

        <div class="main-sidebar-inner">
            <div class="sidebar-nav">


                <?php

                if( !Yii::$app->user->isGuest){
                    echo Menu::widget([
                        'options' => ['class' => 'sidebar-nav-menu'],
                        'encodeLabels' => false,
                        'items' => [
                            /* [
                                 'label' => '<span class="glyphicon glyphicon-home" aria-hidden="true"></span><span>DASHBOARD</span>',
                                 'url' => ['site/index'],
                             ],*/
                            [
                                'label' => '<span class="glyphicon glyphicon-user" aria-hidden="true"></span><span>Users</span>',
                                'url' => ['user/index'],
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span>Create profile</span>',
                                'url' => ['point/profile'],
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-th-list" aria-hidden="true"></span><span>Geo points</span>',
                                'url' => ['point/view'],
                            ],
                          /*  [
                                'label' => '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span>Add event</span>',
                                'url' => ['site/event_add'],
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span>Create point</span>',
                                'url' => ['point/index'],
                            ],*/
                            /*[
                                'label' => '<span class="glyphicon glyphicon-upload" aria-hidden="true"></span><span>Images upload</span>',
                                'url' => ['site/images'],
                            ],*/
                            /*[
                                'label' => ' <img src="/web/img/icons/ic_dasboard.svg" alt="DASHBOARD"><span>DASHBOARD</span>',
                                'url' => ['site/about'],
                            ],
                            ['label' => 'Products', 'url' => ['product/index'], 'items' => [
                                ['label' => 'New Arrivals', 'url' => ['product/index', 'tag' => 'new']],
                                ['label' => 'Most Popular', 'url' => ['product/index', 'tag' => 'popular']],
                            ]],*/

                        ],
                    ]);
                }



                ?>



            </div>
           <div class="sidebar-logout">
                <ul class="sidebar-nav-menu">
                    <li>

                        <?php
                        if(Yii::$app->user->isGuest){
                            $user_label = '<img src="/web/img/icons/ic_logout.svg" alt="LOGOUT"><span>LOGIN</span>';?>

                            <?= Html::a($user_label, ['site/login'], ['data' => ['method' => 'post']]) ?>

                            <?php
                        }else{

                            $user_label = '<img src="/web/img/icons/ic_logout.svg" alt="LOGOUT"><span>LOGOUT</span>';
                            ?>


                            <?= Html::a($user_label, ['site/logout'], ['data' => ['method' => 'post']]) ?>

                            <?php
                        }
                        ?>

                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="main-container">
        <div class="main-container-inner">

            <div class="content-window">
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= Alert::widget() ?>

                <div class="content-window-main">
                    <?= $content ?>
                </div>


            </div>



        </div>
    </div>

</main>


<div class="ajax-loader">
    <img src="/web/img/loaders/preloader.gif" alt="">
</div>

<div class="push-notifications" data-count="0" >
    <div class="push-notification-item-default hidden">
        <div class="push-notification-inner">
            <div class="push-notification-inner-close">
                <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
            </div>
            <div class="push-notification-inner-icon">
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            </div>
            <div class="push-notification-inner-message">

            </div>
        </div>
    </div>

</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>