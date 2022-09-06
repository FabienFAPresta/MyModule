<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class MyModule extends Module
{
    public const AVAILABLE_HOOKS = [
        'header',
        'footer',
        'displayLeftColumn'
    ];

    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Fabien Fernandes Alves';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.0.0.0',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('My Module');
        $this->description = $this->l('Description of My Module');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    /**
     * @inheritDoc
     */
    public function install()
    {
        return (
            parent::install() 
            && $this->registerHook(self::AVAILABLE_HOOKS) 
            && Configuration::updateValue('MYMODULE_TEST', 'Test')
        );
    }

    /**
     * @inheritDoc
     */
    public function uninstall()
    {
        return (
            parent::uninstall() 
            && Configuration::deleteByName('MYMODULE_TEST')
        );
    }

    // public function hookHeader($params)
    // {
    //     // Added CSS file for the front
    //     $this->context->controller->addCSS($this->_path.'views/css/front.css');
    // }

    // public function hookFooter($params)
    // {
    //     // Added JS file for the front
    //     $this->context->controller->addJS($this->_path.'views/js/front.js');
    // }

    // public function hooknomDuCustomHook($params)
    // {
    //     // Added JS file for the front
    //     $this->context->controller->addJS($this->_path.'views/js/front.js');
    // }
}
