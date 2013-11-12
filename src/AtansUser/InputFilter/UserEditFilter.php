<?php
namespace AtansUser\InputFilter;

use Zend\ServiceManager\ServiceManager;
use ZfcBase\InputFilter\ProvidesEventsInputFilter;

class UserEditFilter extends ProvidesEventsInputFilter
{
    public function __construct(ServiceManager $serviceManager)
    {
        $this->add(array(
            'name' => 'id',
            'required' => true,
            'filters' => array(
                array('name' => 'int'),
            ),
        ));

        // TODO username 長度限制
        $this->add(array(
            'name' => 'username',
            'requred' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'required' => true,
            'validators' => array(
                array('name' => 'EmailAddress'),
            ),
        ));

        // TODO password regex
        $this->add(array(
            'name' => 'newPassword',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));

        $this->add(array(
            'name' => 'userRoles',
            'required' => false,
        ));

        $this->add(array(
            'name' => 'status',
            'required' => true,
        ));
    }

    public function callback($value)
    {
        if (is_null($value)) {
            return array();
        }
        return $value;
    }
}
