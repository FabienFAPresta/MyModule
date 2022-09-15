<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/src/TestService.php';
//require_once __DIR__ . '/src/Repository/ProductRepository.php';

class MyModule extends Module implements WidgetInterface
{
    public const AVAILABLE_HOOKS = [
        'actionFrontControllerSetMedia',
        'displayBanner',
        'displayFooter',
        'displayDashboardToolbarIcons'
    ];

    private $container;

    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'Fabien Fernandes Alves';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('My Module');
        $this->description = $this->trans('Description of My Module');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?');
    }

    /**
     * Install and activate the module
     *
     * @return bool
     */
    public function install(): bool
    {
        return (parent::install()
            && $this->registerHook(self::AVAILABLE_HOOKS)
            && Configuration::updateValue('MYMODULE_NAME', 'my friend')
        );
    }

    /**
     * Uninstall the module
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        return (parent::uninstall()
            && Configuration::deleteByName('MYMODULE_NAME')
        );
    }

    /**
     * Generate the configuration form
     *
     * @return string            HTML code to display
     */
    private function displayConfigurationForm(): string
    {
        // Init Fields form array
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Test my configuration string'),
                        'name' => 'MYMODULE_TEST',
                        'required' => true
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value['MYMODULE_TEST'] = Tools::getValue('MYMODULE_TEST', Configuration::get('MYMODULE_TEST'));

        return $helper->generateForm([$form]);
    }

    /**
     * Get the content of the configuration page
     *
     * @return string           HTML code to display
     */
    public function getContent(): string
    {
        $output = '';

        // this part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->name)) {
            // retrieve the value set by the user
            $configValue = (string) Tools::getValue('MYMODULE_TEST');

            // check that the value is valid
            if (empty($configValue) || !Validate::isGenericName($configValue)) {
                // invalid value, show an error
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue('MYMODULE_TEST', $configValue);
                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->displayConfigurationForm();
    }

    public function hookDisplayBanner(array $params)
    {
        $this->context->smarty->assign([
            'my_module_name' => Configuration::get('MYMODULE_NAME'),
            'my_module_link' => $this->context->link->getModuleLink('mymodule', 'display'),
            'my_module_message' => $this->l('This is a simple text message')
        ]);

        return $this->display(__FILE__, 'mymodule.tpl');
    }

    public function hookActionFrontControllerSetMedia()
    {
        $res = $this->context->controller->registerStylesheet(
            'mymodule-style',
            'modules/' . $this->name . '/views/css/mymodule.css',
            [
                'media' => 'all',
                'priority' => 1000,
            ]
        );

        $this->context->controller->registerJavascript(
            'mymodule-javascript',
            'modules/' . $this->name . '/views/js/mymodule.js',
            [
                'position' => 'bottom',
                'priority' => 1000,
            ]
        );
    }

    /**
     * Add an "XML export" to the product list
     *
     * @param  array        $hookParams         Hook parameters
     * @return bool
     */
    public function hookDisplayDashboardToolbarIcons(array $hookParams): bool
    {
        if ($this->isSymfonyContext() && $hookParams['route'] === 'admin_product_catalog') {
            //$this->get('mymodule.testservice')->toto();
            dump($this->container);
            $container = SymfonyContainer::getInstance();
            $container->get('mymodule.testservice');
            dump($container);
            //$products = $this->get('mymodule.product_repository')->findAllByLangId(1);
            //dump($products);
            dump($this->container);
        }

        return true;
    }

    public function hookDisplayFooter($params)
    {
        $this->context->smarty->assign([
            'footer_sentence' => 'This is the footer !',
            'cart_id' => $this->context->cart->id
        ]);

        return $this->display(__FILE__, 'footer.tpl');
    }

    public function getWidgetVariables($hookName, array $configuration = []): array
    {
        return [
            'myparamtest' => 'Prestashop developer'
        ];
    }

    public function renderWidget($hookName = '', array $configuration = []): string
    {
        $this->context->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->display(__FILE__, 'basic.tpl');
    }
}
