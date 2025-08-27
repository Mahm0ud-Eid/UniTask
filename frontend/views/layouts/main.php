<?php

/* @var $this View */
/* @var $content string */

use frontend\assets\AppAsset;
use yii\helpers\Html;
use frontend\assets\CustomAsset;
use yii\web\View;

$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');
AppAsset::register($this);
//\hail812\adminlte3\assets\AdminLteAsset::register($this);
CustomAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title>
        <?= Html::encode($this->title) ?>
    </title>
    <?php $this->head() ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <?php $this->beginBody() ?>
    <?php
    if (!Yii::$app->user->isGuest) {
        echo '<script>
window.btu = {};
window.btu.api_base = "//api.' . getenv('BASE_DOMAIN') . '/v1/";
window.btu.panel_url = "//panel.' . getenv('BASE_DOMAIN') . '";
window.btu.api_key="' . Yii::$app->user->identity->generateScopedKey('WEB') . '";
</script>';
    }
    ?>

    <div class="wrapper">
        <!-- Navbar -->
        <?= $this->render('navbar') ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?= $this->render('sidebar') ?>

        <!-- Content Wrapper. Contains page content -->
        <?= $this->render('content', ['content' => $content]) ?>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <?= $this->render('footer') ?>
    </div>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>