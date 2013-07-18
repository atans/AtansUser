<?php
namespace AtansUser\InputFilter;

use Zend\InputFilter\Factory;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\InputFilter\ProvidesEventsInputFilter;

class PermissionEditFilter extends ProvidesEventsInputFilter
{
    public function __construct(ServiceManager $serviceManager)
    {
        $factory =  new Factory();

        $this->add($factory->createInput(array(
            'name' => 'id',
            'required' => true,
            'filters' => array(
                array('name' => 'int'),
            ),
        )));

        $this->add($factory->createInput(array(
            'name' => 'name',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-z][a-z0-9_\.]+$/',
                    ),
                ),
            ),
        )));

        $this->add($factory->createInput(array(
            'name' => 'description',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        )));
    }
}
