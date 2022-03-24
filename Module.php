<?php
namespace AtansUser;

use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{
    /**
     * Translator name space
     */
    const TRANSLATOR_TEXT_DOMAIN = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        $language = new \Zend\Session\Container('language');

        $mvctranslator = $e->getApplication()->getServiceManager()->get('MvcTranslator');
        $mvctranslator
            ->setLocale($language->current)
            ->setFallbackLocale('en_US');
        $mvctranslator->addTranslationFile(
            'phpArray',
            './vendor/zendframework/zend-i18n-resources/languages/de/Zend_Validate.php',
            'default',
            'en_US'
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return (require __DIR__ . '/config/service_manager.config.php');
    }
}
