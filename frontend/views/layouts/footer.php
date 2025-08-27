<footer class="main-footer">
    <strong>Copyright &copy; 2022-2023 <a href="#">University System</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <?= Yii::t('app', 'Version') ?>: &nbsp;
        <span title="Built on <?= Yii::$app->cache->getOrSet('app-version-date', static function () {
            $composerJson = json_decode(file_get_contents('../../composer.json'), true);
            return $composerJson['date'];
        }, 60 * 60) ?>">
            <?php
            $version = Yii::$app->cache->getOrSet('app-version', static function () {
                $composerJson = json_decode(file_get_contents('../../composer.json'), true);
                return $composerJson['version'];
            }, 60 * 60);
            $versionArr = explode('.', $version);
            $lastVersionNumber = array_pop($versionArr);
            $otherVersionNumbers = implode('.', $versionArr);
            echo $otherVersionNumbers . '.';
            ?><a href="#" id="easter">
                <?= $lastVersionNumber ?>
            </a></span>
    </div>
</footer>