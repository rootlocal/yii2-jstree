<?php

namespace rootlocal\widgets\jstree;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

/**
 * Class JsTreeAsset
 *
 * @author Alexander Zakharov <sys@eml.ru>
 * @package rootlocal\widgets\jstree
 */
class JsTreeAsset extends AssetBundle
{
    /** @var string[] */
    public $js = ['js/tree.js'];
    /** @var string[] */
    public $css = ['css/tree.css'];
    /** @var string[] */
    public $depends = [
        YiiAsset::class,
        JqueryAsset::class,
        JqueryHotKeysAsset::class,
        JqueryCookieAsset::class,
        JsTreeBundle::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
    }
}