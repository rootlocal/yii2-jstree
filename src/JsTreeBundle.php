<?php

namespace rootlocal\widgets\jstree;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class JsTreeBundle
 *
 * @author Alexander Zakharov <sys@eml.ru>
 * @package rootlocal\widgets\jstree
 */
class JsTreeBundle extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@bower/jstree/dist';
    /** @var string[] */
    public $js = [YII_DEBUG ? 'jstree.js' : 'jstree.min.js'];
    /** @var string[] */
    public $depends = [JqueryAsset::class];
}