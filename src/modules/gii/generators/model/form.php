<?php

use dezero\modules\gii\generators\model\Generator;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\model\Generator */

// Repeat default values
$generator->baseClass = 'dezero\db\ActiveRecord';
$generator->queryBaseClass = 'dezero\db\ActiveQuery';
$generator->queryNs = 'app\queries';

echo $form->field($generator, 'tableName')->textInput(['table_prefix' => $generator->getTablePrefix()]);
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'moduleName')->dropDownList(Dz::getModules(), ['onchange' => '$.moduleChanged()']);

// echo $form->field($generator, 'standardizeCapitals')->checkbox();
echo $form->field($generator, 'ns');
echo '<ul>';
echo '<li>Namespace for app modules: <code>my_module\models</code></li>';
echo '<li>Namespace for core modules: <code>dezero\modules\my_module\models</code></li>';
echo '</ul>';

echo $form->field($generator, 'baseClass');
echo $form->field($generator, 'db');
// echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generateRelations')->dropDownList([
    Generator::RELATIONS_NONE => 'No relations',
    Generator::RELATIONS_ALL => 'All relations',
    Generator::RELATIONS_ALL_INVERSE => 'All relations with inverse',
]);
// echo $form->field($generator, 'generateRelationsFromCurrentSchema')->checkbox();
// echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs');
echo '<ul>';
echo '<li>Namespace for app modules: <code>my_module\models\queries</code></li>';
echo '<li>Namespace for core modules: <code>dezero\modules\my_module\models\queries</code></li>';
echo '</ul>';

echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass');
// echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $form->field($generator, 'useSchemaName')->checkbox();

// Event when MODULE dropdown has changed
$script = <<< JS
    $.moduleChanged = function() {
        var module_id = $('#generator-modulename').val();
        $('.field-generator-ns').find('.sticky-value').html(module_id +"\\\models");
        $('#generator-ns').val(module_id +"\\\models");

        $('.field-generator-queryns').find('.sticky-value').html(module_id +"\\\models\\\queries");
        $('#generator-queryns').val(module_id +"\\\models\\\queries");

        $('.field-generator-messagecategory').find('.sticky-value').html(module_id);
        $('#generator-messagecategory').val(module_id);
    }
JS;
$this->registerJs($script, yii\web\View::POS_END);

// Trigger "moduleChanged()" custom event
$script = <<< JS
    $.moduleChanged();
JS;
$this->registerJs($script, yii\web\View::POS_READY);


