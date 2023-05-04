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

// Module list
echo $form->field($generator, 'moduleName')->dropDownList($generator->getModulesList(), ['onchange' => 'jQuery.moduleChanged()']);

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

// ActiveQuery
echo '<br><hr><h3>ActiveQuery</h3><br>';
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs');
echo '<ul>';
echo '<li>Namespace for app modules: <code>my_module\models\query</code></li>';
echo '<li>Namespace for core modules: <code>dezero\modules\my_module\models\query</code></li>';
echo '</ul>';
echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass');

// Search subclass
echo '<br><hr><h3>Search subclass</h3><br>';
echo $form->field($generator, 'generateSearch')->checkbox();
echo $form->field($generator, 'searchNs');
echo '<ul>';
echo '<li>Namespace for app modules: <code>my_module\models\search</code></li>';
echo '<li>Namespace for core modules: <code>dezero\modules\my_module\models\search</code></li>';
echo '</ul>';
echo $form->field($generator, 'searchClass');


// ActiveQuery
echo '<br><hr><h3>More Options</h3><br>';
// echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $form->field($generator, 'useSchemaName')->checkbox();

// Event when MODULE dropdown has changed
$script = <<< JS
    jQuery.moduleChanged = function() {
        var module_id = $('#generator-modulename').val();
        if ( module_id.startsWith('core_') ) {
            module_id = module_id.replace('core_', 'dezero\\\modules\\\');
        }

        $('.field-generator-ns').find('.sticky-value').html(module_id +"\\\models");
        $('#generator-ns').val(module_id +"\\\models");

        $('.field-generator-queryns').find('.sticky-value').html(module_id +"\\\models\\\query");
        $('#generator-queryns').val(module_id +"\\\models\\\query");

        $('.field-generator-searchns').find('.sticky-value').html(module_id +"\\\models\\\search");
        $('#generator-searchns').val(module_id +"\\\models\\\search");

        var model_class = $('#generator-modelclass').val().toLowerCase();
        $('.field-generator-messagecategory').find('.sticky-value').html(model_class);
        $('#generator-messagecategory').val(model_class);
    }
JS;
$this->registerJs($script, yii\web\View::POS_END);

// Trigger "moduleChanged()" custom event
$script = <<< JS
    jQuery.moduleChanged();
JS;
$this->registerJs($script, yii\web\View::POS_READY);


