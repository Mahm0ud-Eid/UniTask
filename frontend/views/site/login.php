<?php

use common\models\LoginForm;
use kartik\growl\Growl;
use yii\helpers\Html;

/**
 *  @var LoginForm $model
 */
?>

<div class="header">
    <div class="banner"><img class="btu" src="/img/btu-logo-ar-en.jpg" alt="BTU logo in ar & en"  ></div>
    <div id="my-element1" class="el lt">Student Affair</div>
    <div id="my-element2" class="el rt">Tasks</div>
    <div id="my-element" class="active">Quizzes</div>
    <div id="my-element3" class="active">Study Materials</div>
</div>
<div class="content">
    <div class="title">Log In As 
        <div class="slider">
            <div> Student</div>
            <div> Instructor</div>
            <div> Administrator</div>
        </div>
    </div>
    <?= Yii::$app->session->hasFlash('danger') ? Growl::widget([
        'type' => Growl::TYPE_DANGER,
        'closeButton' => [
            'tag' => 'button',
            'class' => 'btn-close',
            'label' => '',
        ],
        'body' => Yii::$app->session->getFlash('danger'),
        'delay' => 500,
        'pluginOptions' => [
            'placement' => [
                'from' => 'top',
                'align' => 'right',
            ]
        ]
    ]) : '' ?>
</div>
<div id="loginform" class="animate__animated animate__fadeIn main">

    <h3 class="heading">Login</h3>
    <?php $form = \yii\bootstrap4\ActiveForm::begin([
            'id' => 'login-form',
    ])?>
    <?= $form->field($model, 'email')
                ->textInput(['placeholder' => $model->getAttributeLabel('email'),
                    'class' => 'userinput',
                    ])?>
    <?=  $form->field($model, 'password')
                ->passwordInput(['placeholder' => $model->getAttributeLabel('password'),
                    'class' => 'userinput',
                ])?>
    <div class="h-captcha" data-sitekey="<?= getenv('HCAPTCHA_SITE_KEY') ?>"></div>
    <div class="remember">
        <?= $form->field($model, 'rememberMe')->checkbox([
            'labelOptions' => [
                'class' => 'name',
            ],
            'id'=>'rememberMe',
            'uncheck' => null,
            'class' => 'userinput',
        ])?>
    </div>
    <?= Html::submitButton('Sign In', ['class' => 'btn btn-primary btn-block click'])?>
    <?php \yii\bootstrap4\ActiveForm::end();?>
    <a href="#" class="forget">Forgot my password</a>
</div>