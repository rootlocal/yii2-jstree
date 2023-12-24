<?php

namespace rootlocal\widgets\jstree;

use lo\widgets\modal\ModalAjax;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Class JsTreeWidget
 *
 * @link https://www.jstree.com/api/
 * @link http://servicespouraines.com/fileadmin/templates/skin_as_aines/js/jstree_git/docs/files/jstree-js.html
 *
 * @property-read string $jsOptions
 * @property-read array|string[] $classMaps
 * @property-read string $hash
 */
class JsTreeWidget extends Widget
{
    /** @var string The name of the jQuery plugin to use for this widget. */
    public const PLUGIN_NAME = 'tree';
    /** @var string Default theme */
    public const THEME_DEFAULT = 'default';
    /** @var string */
    public const THEME_DEFAULT_DARK = 'default-dark';
    /** @var string Bootstap3 theme */
    public const THEME_BOOTSTRAP3 = 'proton';

    /** @var string */
    public string $theme = 'default';
    /**
     * @var string
     *
     * ```php
     * 'url' => Url::to(['/tree/explorer-request'])
     * ```
     */
    public string $url;
    /**
     * ```php
     * new JsExpression('function (node) {return {"id": node.id};}')
     * ```
     *
     * json data:
     * ```
     * [
     *      { "id" : "ajson1", "parent" : "#", "text" : "Simple root node" },
     *      { "id" : "ajson2", "parent" : "#", "text" : "Root node 2" },
     *      { "id" : "ajson3", "parent" : "ajson2", "text" : "Child 1" },
     *      { "id" : "ajson4", "parent" : "ajson2", "text" : "Child 2" },
     * ]
     * ```
     *
     * @var string|JsExpression
     */
    public string $data = 'function (node) {return {"id": node.id};}';
    /**
     * determines what happens when a user tries to modify the structure of the tree
     * If left as false all operations like create, rename, delete, move or copy are prevented.
     * You can set this to true to allow all interactions or use a function to have better control.
     *
     * определяет, что происходит, когда пользователь пытается изменить структуру дерева
     * Если оставить значение false, все операции, такие как создание, переименование, удаление, перемещение или копирование, будут запрещены.
     * Вы можете установить для этого параметра значение true, чтобы разрешить все взаимодействия, или использовать функцию для лучшего контроля.
     * @var bool|JsExpression
     *
     * ```javascript
     *      function (operation, node, node_parent, node_position, more) {
     *          // operation can be create_node, edit, delete_node, move_node, copy_node or edit
     *          // in case of rename_node node_position is filled with the new node name
     *          console.log(operation);
     *          return operation === "edit" ? true : false;
     *      }')
     * ```
     */
    public bool $check_callback = true;
    /**
     * the open/close animation duration in milliseconds - set this to false to disable the animation (default is 200)
     * @var int
     */
    public int $animation = 100;
    /**
     * a boolean indicating if multiple nodes can be selected
     * @var bool
     */
    public bool $multiple = false;
    /**
     * @var array
     *
     * ```php
     * 'actions' => [
     *          'create' => [
     *          'url' => Url::to(['/tree/create-node']),
     *          'label' => Yii::t('tree', 'Create node'),
     *          'icon' => '',
     *      ],
     *      'rename' => [
     *          'url' => Url::to(['/tree/rename-node']),
     *          'label' => Yii::t('tree', 'Rename'),
     *          'icon' => 'glyphicon glyphicon-pencil',
     *      ],
     *      'remove' => [
     *          'url' => Url::to(['/tree/delete-node']),
     *          'label' => Yii::t('tree', 'Remove'),
     *          'icon' => 'glyphicon glyphicon-remove',
     *      ],
     *      'select' => [
     *          'url' => Url::to('/tree/update'),
     *      ],
     *      'move' => [
     *          'url' => Url::to(['/tree/move-node']),
     *      ]
     * ]
     * ```
     */
    public array $actions = [];
    /** @var array */
    public array $types = [];
    /**
     * [core contextmenu dnd state wholerow themes html_data ui search types]
     * @var array
     */
    public array $plugins = [];
    /** @var array */
    public array $jsOptions = [];
    /**
     * @var array the HTML attributes for the input tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public array $options = ['class' => 'tree'];

    public ?ModalAjax $modalAjax = null;

    /** @var string the hashed variable to store the pluginOptions */
    private string $_hash;
    /** @var string */
    private string $_jsOptions;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->url)) {
            $this->url = Url::to(['/tree/explorer-request']);
        }

        if (empty($this->data)) {
            $this->data = 'function (node) {return {"id": node.id};}';
        }

        $this->plugins[] .= 'themes';

        if (!array_search('core', $this->plugins)) {
            $this->plugins[] .= 'core';
        }

        if (!empty($this->actions)) {
            if (!array_search('contextmenu', $this->plugins)) {
                $this->plugins[] .= 'contextmenu';
            }
        }

        if (!array_search('ui', $this->plugins)) {
            $this->plugins[] .= 'ui';
        }

        if (empty($this->types)) {
            $this->types = [
                'child' => ['icon' => 'glyphicon glyphicon-leaf'],
                'root' => ['icon' => 'glyphicon glyphicon-folder-open'],
                //'default' => ['icon' => 'fa fa-angle-right fa-fw'],
            ];
        }

        if (!array_search('types', $this->plugins)) {
            $this->plugins[] .= 'types';
        }

        $view = $this->getView();
        switch ($this->theme) {

            case self::THEME_DEFAULT_DARK:
                JsTreeThemeDefaultDarkBundle::register($view);
                $this->jsOptions['core']['themes'] = [
                    'name' => 'default-dark',
                    'responsive' => true,
                ];
                break;

            case self::THEME_BOOTSTRAP3:
                JsTreeThemeBootstrap3Asset::register($view);
                BootstrapAsset::register($view);
                $this->jsOptions['core']['themes'] = [
                    'name' => 'proton',
                    'responsive' => true,
                ];
                break;

            default:
                JsTreeThemeDefaultBundle::register($view);
                $this->jsOptions['themes'] = [
                    'name' => 'default',
                    'responsive' => true,
                ];
                break;
        }

        $this->jsOptions = ArrayHelper::merge([
            'core' => [
                'data' => [
                    'url' => $this->url,
                    //'type' =>   'json', // or "xml_nested" or "xml_nested"
                    'async' => true,
                    //'data' => new JsExpression('function (node) {return {"id": node.id};}'),
                    //'async_data' => new JsExpression('function (NODE) { return { id : $(NODE).attr("id") || 0, my_param : "my_value" } }'),
                    'data' => new JsExpression('function (node) { return { "id" : $(node).attr("id") }; }'),
                ],
                'check_callback' => $this->check_callback,
                'animation' => $this->animation,
                'multiple' => $this->multiple,
            ],

            'types' => $this->types,

        ], $this->jsOptions);

        $this->registerClientScript($view);

    }

    /**
     * Registers the needed client script and options.
     */
    public function registerClientScript(View $view)
    {
        $this->hashPluginOptions($view);
        JsTreeBundle::register($view);
        JsTreeAsset::register($view);
        $js = sprintf('jQuery("#%s").%s(%s);', $this->hash, self::PLUGIN_NAME, $this->hash);
        $view->registerJs(new JsExpression($js));
    }

    /**
     * Register JS variable $this::PLUGIN_NAME
     * @param $view View
     */
    protected function hashPluginOptions(View $view)
    {
        $js = sprintf('var %s = %s;', $this->hash, $this->getJsOptions());
        $view->registerJs(new JsExpression($js), $view::POS_HEAD);
    }

    /**
     * Generates a hashed variable to store the plugin
     * @return string
     */
    public function getHash(): string
    {
        if (empty($this->_hash)) {
            $this->_hash = $this::PLUGIN_NAME . '_' . hash('crc32',
                    $this->id . $this->getJsOptions());
        }

        return $this->_hash;
    }

    /**
     * @return string
     */
    public function getJsOptions(): string
    {
        if (empty($this->_jsOptions)) {
            $defaultJsOptions['modalAjaxId'] = $this->modalAjax !== null ? $this->modalAjax->getId() : null;
            $defaultJsOptions['pjaxContainer'] = $this->modalAjax !== null ? $this->modalAjax->pjaxContainer : null;
            $defaultJsOptions['id'] = $this->id;
            $defaultJsOptions['actions'] = $this->actions;
            $defaultJsOptions['jstree'] = $this->jsOptions;
            $defaultJsOptions['jstree']['plugins'] = $this->plugins;
            $this->_jsOptions = new JsExpression(Json::htmlEncode($defaultJsOptions));
        }

        return $this->_jsOptions;
    }

    /**
     * @return string
     */
    public function run(): string
    {
        $html = "";
        $this->initWidgetOptions();

        if ($this->modalAjax !== null) {
            $html .= $this->modalAjax->run();
        }

        $html .= Html::tag('div', '', $this->options);

        return $html;
    }

    /**
     * Initializes client options
     */
    public function initWidgetOptions()
    {
        $this->options = ArrayHelper::merge([
            'id' => $this->getHash(),
            'data-plugin-' . self::PLUGIN_NAME => $this->getHash(),
            'data-hash' => $this->getHash(),
        ], $this->options);
    }
}