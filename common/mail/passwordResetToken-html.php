<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var string $token */

$resetLink = 'https://panel.tasks.devlevi.live/site/reset-password?token=' . $token;
?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->name) ?>,</p>

    <p>Follow the link below to reset your password:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
