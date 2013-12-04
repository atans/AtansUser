<?php
namespace AtansUser\Form;

use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class UserSearchForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    /**
     * Translator text domain
     */
    const TRANSLATOR_TEXT_DOMAIN = 'AtansUser';

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('user-search-form');
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-inline');

        $this->setServiceManager($serviceManager);
        $translator = $serviceManager->get('Translator');

        $page = new Element\Text('page');
        $this->add($page);

        $size = new Element\Text('size');
        $size->setAttribute('class', 'form-control');
        $size->setAttribute('style', 'width: 60px;');
        $this->add($size);

        $query = new Element\Text('query');
        $query->setAttribute('class', 'form-control');
        $this->add($query);

        $status = new Element\Select('status');
        $status->setAttribute('class', 'form-control')
               ->setOptions(array(
                   'empty_option' => $translator->translate('Status', static::TRANSLATOR_TEXT_DOMAIN),
               ))
               ->setValueOptions($serviceManager->get('atansuser_user_statuses'));
        $this->add($status);

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
            'size' => array(
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
            'status' => array(
                'required' => false,
            ),
        );
    }

    /**
     * Get serviceManager
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set serviceManager
     *
     * @param ServiceManager $serviceManager
     * @return UserSearchForm
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}