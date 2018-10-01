<?php
/**
 * CodeUP yihai using Yii Framework
 * @link http://codeup.orangeit.id/yihai
 * @copyright Copyright (c) 2018 OrangeIT.ID
 * @author Upik Saleh <upxsal@gmail.com>
 */

namespace codeup\widgets\nestable;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Class NestableWidget
 * @package codeup\widgets\nestable
 */
class NestableWidget extends \yii\base\Widget
{
    /** @var array items list */
    public $items = [];
    /** @var int max sub nestable */
    public $maxDepth = 5;
    /** @var array clientOptions for nestable */
    public $settings = [];
    /** @var bool show output nestable serialize data */
    public $output = true;
    /** @var bool show expand button menu */
    public $menuExpand = true;
    /** @var array options for expand menu html */
    public $menuExpandOptions = ['class' => 'btn-group'];
    /** @var array options for expand menu button html */
    public $menuExpandButtonOptions = ['class' => 'btn btn-default'];
    /** @var array options for textarea html output */
    public $outputOptions = [];
    /** @var array html options for list */
    public $listOptions = [];
    /** @var array html options for item*/
    public $itemOptions = [];
    /** @var array html options for drag handle */
    public $dragOptions = [];
    /** @var array html options for content */
    public $contentOptions = [];
    /** @var array html attribute data-id */
    public $outputData = ['id'];

    /** @var string html id untuk menampilkan output */
    public $outputToId = null;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->outputOptions = ArrayHelper::merge([
            'class' => 'form-control',
            'id' => $this->getNestableId('_output')
        ], $this->outputOptions);

        if (!isset($this->settings['maxDepth'])) {
            $this->settings['maxDepth'] = $this->maxDepth;
        }
        // set list html options
        $this->listOptions = ArrayHelper::merge(['class' => 'dd-list'], $this->listOptions);
        // set item html options
        $this->itemOptions = ArrayHelper::merge(['class' => 'dd-item dd3-item'], $this->itemOptions);
        // set drag html options
        $this->dragOptions = ArrayHelper::merge(['class' => 'dd-handle dd3-handle'], $this->dragOptions);
        // set content html options
        $this->contentOptions = ArrayHelper::merge(['class' => 'dd3-content'], $this->contentOptions);
        echo Html::beginTag('div', ['class' => 'dd', 'id' => $this->getNestableId()]);
        // render expand menu
        if ($this->menuExpand) {
            $this->_renderMenuExpand();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {

        $this->_renderNestable($this->items);
        if ($this->output && $this->outputToId === null) {
            $this->_renderOutput();
        }
        echo Html::endTag('div');
        $this->registerAssets();
    }

    /**
     * {@inheritdoc}
     */
    public function registerAssets()
    {
        $view = $this->getView();

        $settings = Json::encode($this->settings);
        $js = "jQuery('#{$this->id}').nestable($settings)";
        $outputToId = ($this->outputToId !== null ? $this->outputToId : $this->outputOptions['id']);
        if ($this->output) {
            $js .= ".on('change', function(){
            var output = jQuery('#{$outputToId}');
            if (window.JSON) {
               output.val(window.JSON.stringify(jQuery('#{$this->getNestableId()}').nestable('serialize')));
            }
            else{
            output.val('JSON browser support required for this demo.');
            }
            });";
            $js .= "jQuery('#{$outputToId}').val(window.JSON.stringify(jQuery('#{$this->getNestableId()}').nestable('serialize')));";
        }
        NestableAsset::register($view);
        $view->registerJs("$js;");
    }

    /**
     * {@inheritdoc}
     */
    protected function getNestableId($append = '')
    {
        return $this->id . $append;
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderNestable($items)
    {
        echo Html::beginTag('ol', $this->listOptions);
        $itemOptions = $this->itemOptions;
        foreach ($items as $item) {
            foreach($this->outputData as $od){
                if(isset($item[$od])){
                    $itemOptions['data-'.$od] = $item[$od];
                }
            }

            echo Html::beginTag('li', $itemOptions);
            echo Html::tag('div', 'Drag', $this->dragOptions);
            echo Html::tag('div', $item['title'], $this->contentOptions);
            if(isset($item['children'])){
                $this->_renderNestable($item['children']);
            }
            echo Html::endTag('li');
        }
        echo Html::endTag('ol');
    }
    protected function _itemsRender($item){

    }

    /**
     * {@inheritdoc}
     */
    protected function _renderOutput()
    {
//        if($this->outputToId)
//            $this->outputOptions['id']
        echo Html::textarea('', '', $this->outputOptions);
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderMenuExpand()
    {
        $menuId = $this->getNestableId('_nestable_menu');
        $options = ArrayHelper::merge([
            'id' => $menuId
        ], $this->menuExpandOptions);

        echo Html::beginTag('div', $options);

        echo Html::button('Expand All', ArrayHelper::merge(['data-action' => "expand-all"], $this->menuExpandButtonOptions));
        echo Html::button('Collapse All', ArrayHelper::merge(['data-action' => "collapse-all"], $this->menuExpandButtonOptions));

        echo Html::endTag('div');
        $this->getView()->registerJs("jQuery('#{$menuId} > [data-action=\"expand-all\"]').click(function(){jQuery('#{$this->getNestableId()}').nestable('expandAll')});");
        $this->getView()->registerJs("jQuery('#{$menuId} > [data-action=\"collapse-all\"]').click(function(){jQuery('#{$this->getNestableId()}').nestable('collapseAll')});");
    }
}