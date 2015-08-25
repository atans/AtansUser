<?php
namespace AtansUser\Form;

use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use ZfcBase\Form\ProvidesEventsForm;

class PermissionSearchForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    /**
     * Initialization
     */
    public function __construct()
    {
        parent::__construct('user-search-form');
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-inline');

        $page = new Element\Text('page');
        $this->add($page);

        $count = new Element\Text('count');
        $count->setAttribute('class', 'form-control');
        $count->setAttribute('style', 'width: 80px;');
        $this->add($count);

        $query = new Element\Text('query');
        $query->setAttribute('class', 'form-control');
        $this->add($query);

        $this->getEventManager()->trigger('init', $this);
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
            'page' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ),
            'count' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ),
            'query' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
        );
    }
}