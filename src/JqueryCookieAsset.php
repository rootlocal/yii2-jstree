<?php

namespace rootlocal\widgets\jstree;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class JqueryCookieAsset
 *
 * @author Alexander Zakharov <sys@eml.ru>
 * @package rootlocal\widgets\jstree
 */
class JqueryCookieAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@bower/jquery.cookie';
    /** @var string[] */
    public $js = ['jquery.cookie.js'];
    /** @var string[] */
    public $depends = [JqueryAsset::class];
}