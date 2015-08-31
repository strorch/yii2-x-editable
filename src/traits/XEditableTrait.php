<?php

/*
 * X-editable extension for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-x-editable
 * @package   yii2-x-editable
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (https://hiqdev.com/)
 */

namespace hiqdev\xeditable\traits;

use hipanel\helpers\ArrayHelper;
use hiqdev\xeditable\assets\XEditableAsset;
use vova07\select2\Select2Asset;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Class XEditableTrait
 * @package hiqdev\xeditable\traits
 */
trait XEditableTrait
{
    public $pluginOptions = [];

    /**
     * @var string library to be used: bootstrap, jqueryui, plain jquery
     */
    public $library;

    protected $_view;

    public function getView()
    {
        if ($this->_view === null) {
            $this->_view = Yii::$app->getView();
        }

        return $this->_view;
    }

    public function registerAssets()
    {
        $xea = new XEditableAsset();
        if ($this->library) {
            $xea->library = $this->library;
        }
        $xea::register($this->view);
        if ($this->pluginOptions['type'] === 'select2') {
            Select2Asset::register($this->view);
        }
    }

    public function init()
    {
        parent::init();
        $this->registerAssets();
    }

    public function registerMyJs($data)
    {
        if (!$this->pluginOptions['title']) {
            $this->pluginOptions['title'] = $data['model']->getAttributeLabel($data['attribute']);
        }
        $value = $data['model']->{$data['attribute']};
        if (is_array($value)) {
            if (ArrayHelper::isAssociative($value)) {
                $value = array_keys($value);
            }
        } else {
            $value = [$value];
        }
        $this->pluginOptions['value'] = $value;
        if ($this->pluginOptions['url']) {
            $this->pluginOptions['url'] = Url::to($this->pluginOptions['url']);
        }
        $this->view->registerJs('$(".editable[data-name=' . $data['attribute'] . ']").editable(' . Json::htmlEncode($this->pluginOptions) . ');');
    }

    public function prepareHtml($data)
    {
        $this->registerMyJs($data);
        $params = [
            'class'      => 'editable',
            'data-name'  => $data['attribute'],
            'data-pk'    => $data['model']->primaryKey,
//            'data-value' => $data['model']->{$data['attribute']},
        ];

        return Html::a('', '#', $params);
    }
}
