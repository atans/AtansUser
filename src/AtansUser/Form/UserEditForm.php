<?php
namespace AtansUser\Form;

use AtansUser\Entity\User;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class UserEditForm extends ProvidesEventsForm
{
    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('user-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
        $this->setHydrator(new DoctrineObject($entityManager))
            ->setObject(new User());

        $this->add(array(
            'name' => 'id',
        ));

        $this->add(array(
            'name' => 'username',
        ));

        $this->add(array(
            'name' => 'email',
        ));

        $this->add(array(
            'name' => 'newPassword',
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
            'name' => 'userRoles',
            'options' => array(
                'label_attributes' => array(
                    'class' => 'checkbox'
                ),
                'object_manager' => $entityManager,
                'target_class'   => 'AtansUser\Entity\Role',
                'property'       => 'name',
                'is_method'      => true,
                'find_method'    => array(
                    'name' => 'findAll',
                ),
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'status',
            'options' => array(
                'label_attributes' => array(
                    'class' => 'radio inline',
                ),
                'value_options' => array(
                    User::STATUS_ACTIVE   => '有效',
                    User::STATUS_INACTIVE => '失效',
                    User::STATUS_DELETED  => '已刪除',
                ),
            ),
        ));
    }
}
