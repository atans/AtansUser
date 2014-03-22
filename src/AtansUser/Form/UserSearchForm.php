<?php
namespace AtansUser\Form;

use AtansUser\Module;
use Zend\Form\Element;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class UserSearchForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Initialization
     *
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->setServiceManager($serviceManager);

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

        $status = new Element\Select('status');
        $status->setAttribute('class', 'form-control')
               ->setEmptyOption($this->getTranslator()->translate('Status', Module::TRANSLATOR_TEXT_DOMAIN))
               ->setValueOptions($serviceManager->get('atansuser_user_status_value_options'));
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

    /**
     * Get translator
     *
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        if (! $this->translator instanceof TranslatorInterface) {
            $this->setTranslator($this->getServiceManager()->get('Translator'));
        }
        return $this->translator;
    }

    /**
     * Set translator
     *
     * @param  TranslatorInterface $translator
     * @return UserSearchForm
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        return $this;
    }
}
