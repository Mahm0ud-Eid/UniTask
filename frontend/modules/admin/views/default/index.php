<?php

use yii\db\Query;

$this->title = 'Admin';
$this->params['breadcrumbs'] = [['label' => $this->title]];
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">Queues</div>
                    <div class="card-body">
                        <h4 class="text-center">Normal Queues</h4>
                        <p><b>Queue One: </b><?= (new Query())->select('id')->from('queue')->count('id') ?></p>
                        <p><b>Queue Two: </b><?= (new Query())->select('id')->from('queue2')->count('id') ?></p>
                        <p><b>Queue Three: </b><?= (new Query())->select('id')->from('queue3')->count('id') ?></p>
                        <p><b>Queue Four: </b><?= (new Query())->select('id')->from('queue4')->count('id') ?></p>
                        <p><b>Queue Five: </b><?= (new Query())->select('id')->from('queue5')->count('id') ?></p>
                    </div>
                </div>
        </div>
    </div>
</div>