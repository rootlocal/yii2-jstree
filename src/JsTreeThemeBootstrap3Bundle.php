<?php

namespace rootlocal\widgets\jstree;

use yii\web\AssetBundle;

/**
 * Class JsTreeThemeBootstrap3Bundle
 *
 * @author Alexander Zakharov <sys@eml.ru>
 * @package rootlocal\widgets\jstree
 */
class JsTreeThemeBootstrap3Bundle extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@bower/jstree-bootstrap-theme/dist/themes/proton';
    /** @var string[] */
    public $css = [YII_DEBUG ? 'style.css' : 'style.min.css'];
}