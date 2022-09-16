<?php

declare(strict_types=1);

//namespace PrestaShop\Module\MyModule\Controller\Front;

// use ModuleFrontController;
// use Tools;

class MyModuleDisplayModuleFrontController extends ModuleFrontController
{
    /**
     * Init the controller content
     *
     * @return void
     */
    public function initContent(): void
    {
        parent::initContent();
        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/display.tpl');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('form')) {
            return Tools::redirect('/');
        }
    }
}
