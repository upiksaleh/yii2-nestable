<?php
/**
 * CodeUP yihai using Yii Framework
 * @link http://codeup.orangeit.id/yihai
 * @copyright Copyright (c) 2018 OrangeIT.ID
 * @author Upik Saleh <upxsal@gmail.com>
 */

namespace codeup\widgets\nestable;

/**
 * Class NestableAsset
 * @package codeup\widgets\nestable
 */
class NestableAsset extends \yii\web\AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';

    public $css = [
        'nestable.css'
    ];

    public $js = [
        'nestable.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        parent::init();
        $this->publishOptions['forceCopy'] = true;
    }
}