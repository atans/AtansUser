<?php
namespace AtansUser\Form;

use AtansUser\Entity\User;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\InputFilter\InputFilterProviderInterface;
use ZfcBase\Form\ProvidesEventsForm;

class UserForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('user-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
        $this->setHydrator(new DoctrineObject($serviceManager))
             ->setObject(new User());

        $this->add(array(
            'name' => 'id',
        ));

        $this->add(array(
            'name' => 'username',
        ));

        $this->add(array(
            'name' => 'password',
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
            'name' => 'roles',
            'options' => array(
                'label_attributes' => array(
                    'class' => 'checkbox'
                ),
                'object_manager' => $entityManager,
                'target_class'   => 'AtansUser\Entity\Roles',
                'property'       => 'name',
                'is_method'      => true,
                'find_method'    => array(
                    'name' => 'findAll',
                ),
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'status',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value'      => User::STATUS_ACTIVE,
                'unchecked_value'    => User::STATUS_DISABLED,
            ),
        ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'id' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ),
            'password' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
        );
    }
}
