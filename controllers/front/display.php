<?php

class mymoduledisplayModuleFrontController extends ModuleFrontController {
    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/display.tpl');
    }
}