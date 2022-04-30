<?php

namespace rootlocal\widgets\jstree;

use yii\web\AssetBundle;

/**
 * Class JsTreeThemeDefaultBundle
 *
 * @author Alexander Zakharov <sys@eml.ru>
 * @package rootlocal\widgets\jstree
 */
class JsTreeThemeDefaultBundle extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@bower/jstree/dist/themes/default';
    /** @var string[] */
    public $css = [YII_DEBUG ? 'style.css' : 'style.min.css'];
}