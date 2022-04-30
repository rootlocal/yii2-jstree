<?php

namespace rootlocal\widgets\jstree;

use yii\web\AssetBundle;

/**
 * Class JsTreeThemeBootstrap3Asset
 *
 * @author Alexander Zakharov <sys@eml.ru>
 * @package rootlocal\widgets\jstree
 */
class JsTreeThemeBootstrap3Asset extends AssetBundle
{
    /** @var string[] */
    public $css = ['jstree-bootstrap3.css'];
    /** @var string[] */
    public $depends = [JsTreeThemeBootstrap3Bundle::class];


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->sourcePath = dirname(__FILE__) . '/assets/themes/bootstrap3';
    }

}