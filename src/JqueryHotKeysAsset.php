<?php

namespace rootlocal\widgets\jstree;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class JqueryHotKeysAsset
 *
 * @author Alexander Zakharov <sys@eml.ru>
 * @package rootlocal\widgets\jstree
 */
class JqueryHotKeysAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@bower/jquery.hotkeys';
    /** @var string[] */
    public $js = ['jquery.hotkeys.js'];
    /** @var string[] */
    public $depends = [JqueryAsset::class];
}