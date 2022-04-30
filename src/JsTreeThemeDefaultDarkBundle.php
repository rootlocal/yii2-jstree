<?php

namespace rootlocal\widgets\jstree;

use yii\web\AssetBundle;

/**
 * Class JsTreeThemeDefaultDarkBundle
 *
 * @author Alexander Zakharov <sys@eml.ru>
 * @package rootlocal\widgets\jstree
 */
class JsTreeThemeDefaultDarkBundle extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@bower/jstree/dist/themes/default-dark';
    /** @var string[] */
    public $css = [YII_DEBUG ? 'style.css' : 'style.min.css'];
}