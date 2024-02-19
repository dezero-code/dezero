<?php
/*
|-----------------------------------------------------------------
| Controller class for testing DataObjects
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\data\ArrayDataObject;
use dezero\data\StringDataObject;
use dezero\helpers\ArrayHelper;
use dezero\helpers\StringHelper;
use dezero\web\Controller;
use Yii;

class DataObjectController extends Controller
{
     /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Permissions
        $this->requireSuperadmin();

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    /**
     * Main action
     */
    public function actionIndex()
    {
        // Testing StringDataObject
        // $this->testStringDataObject();

        // Testing ArrayDataObject
        $this->testArrayDataObject();

        return $this->render('//test/test/index');
    }


    /**
     * Testing STRING data objects
     */
    private function testStringDataObject()
    {
        d("----------- STRING DATA OBJECT TESTS -----------");

        $string_object = StringDataObject::from(" hola qué hases?");

        d($string_object->trim());
        d($string_object->value());

        d($string_object->readableName());
        d($string_object->value());

        d($string_object->strtoupper());
        d($string_object->value());

        d($string_object->strtolower());
        d($string_object->value());

        d($string_object->ucfirst());
        d($string_object->value());

        $string_object = StringHelper::toObject("<p>Soy un HTML y un <strong>bold</strong> molón</p>");

        d($string_object->cleanHtml(['strong']));
        d($string_object->value());

        d($string_object->original());
        d($string_object->strlen());

        d($string_object->encrypt('sha1'));
        d($string_object->value());



        dd("----------- FINISHED TESTS -----------");

    }


    /**
     * Testing ArrayDataObject class
     */
    private function testArrayDataObject()
    {
        $array_object = ArrayHelper::toObject([
            33 => [
                'firstname' => 'Enjuto',
                'lastname'  => 'Mojamuto',
                'age'       => 40
            ],
            66 => [
                'firstname' => 'Hincli',
                'lastname'  => 'Mincli',
                'age'       => 50
            ],
        ]);

        // Get all the items
        d($array_object->all());

        // Return item by key "33"
        d($array_object->get(33));

        // Add a new item
        $array_object->add([99 => [
            'firstname' => 'Bocachoti',
            'age'       => 30
        ]]);
        d($array_object->all());

        // Change "firstname" of 66 from "Hincli" to "Jincli"
        $array_object->set([66, 'firstname'], 'Jincli1');
        d($array_object->all());
        $array_object->set('66.firstname', 'Jincli2');
        d($array_object->all());

        d("----------- ACCESS METHODS TESTS -----------");

        // Return value "firstname" of key 66
        d($array_object->get('66.firstname'));
        d($array_object->get([66, 'firstname']));

        // Return the element in the second position
        d($array_object->at(2));

        // Returns the numerical index of the given key
        d($array_object->index(66));

        // Return an array with all the lastnames
        d($array_object->column('lastname'));

        // First
        d($array_object->first());
        d($array_object->firstKey());

        // Last
        d($array_object->last());
        d($array_object->lastKey());

        // Original
        d($array_object->original());


        d("----------- COUNT METHODS TESTS -----------");

        // Count
        d(count($array_object));
        d($array_object->count());


        d("----------- ITERATION TESTS -----------");

        // Iterations as an array
        $num_iteration = 0;
        foreach ( $array_object as $key => $item )
        {
            $num_iteration++;
            d("ITERATION #{$num_iteration} - KEY = {$key}");
            d($item);
        }


        d("----------- ARRAY ACCESS METHODS TESTS -----------");
        d(isset($array_object[33]));
        d(isset($array_object[34]));
        d($array_object[33]);
        d($array_object[33]['firstname']);

        // Update existing element
        $array_object[33] = [
            'firstname' => 'Enjuto super',
            'lastname'  => 'Mojamuto super',
            'age'       => 41
        ];
        d($array_object->all());
        d($array_object->get(33));

        // Add a new element
        $array_object[34] = [
            'firstname' => 'Bounty',
            'lastname'  => 'Onthebounty'
        ];
        d($array_object[34]);
        d($array_object->all());

        d($array_object->original());


        d("----------- UTILS METHODS TESTS -----------");

        // JSON
        d($array_object->toJson());

        $array_object = ArrayDataObject::from([
            ' valor con espacios.    ',
            "Nuevo elémento con \n y \t",
            ' M á s - E s p a c i o s '
        ]);
        d($array_object->all());
        $array_object->trim();
        d($array_object->all());



        dd("----------- FINISHED TESTS -----------");
    }
}
