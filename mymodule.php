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
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Component\HttpFoundation\Response;

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

require_once __DIR__ . '/src/TestService.php';

class MyModule extends Module implements WidgetInterface
{
    public const AVAILABLE_HOOKS = [
        'actionFrontControllerSetMedia',
        'displayBanner',
        'displayFooter',
        'displayDashboardToolbarIcons',
        'moduleRoutes'
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

    private function installSql(): bool
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "testcomment` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` varchar(255) DEFAULT NULL,
            `comment` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        return Db::getInstance()->execute($sql);
    }

    public function installTab(): bool
    {
        $tab = new Tab();
        $tab->class_name = 'AdminTestController';
        $tab->module = $this->name;
        $tabRepository = $this->get('prestashop.core.admin.tab.repository');
        $tab->id_parent = $tabRepository->findOneIdByClassName('DEFAULT');
        $tab->icon = 'settings_applications';
        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            $tab->name[$language['id_lang']] = $this->trans('Test admin controller');
        }

        try {
            $tab->save();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }

        return true;
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
            && $this->installSql()
            && $this->installTab()
        );
    }

    private function uninstallSql(): bool
    {
        $sql = "DROP TABLE IF EXISTS " . _DB_PREFIX_ . "testcomment";
        return Db::getInstance()->execute($sql);
    }

    public function uninstallTab(): bool
    {
        $tabRepository = $this->get('prestashop.core.admin.tab.repository');
        $tabId = $tabRepository->findOneIdByClassName('AdminTest');

        if ($tabId) {
            $tab = new Tab($tabId);
            try {
                $tab->delete();
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }

        return true;
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
            && $this->uninstallSql()
            && $this->uninstallTab()
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
        /* 
         * First method using the helper
         */

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

        /* 
         * Second method using the tpl
         */
        // $message = null;

        // if (Tools::getValue('courserating')) {
        //     Configuration::updateValue('COURSE_RATING', Tools::getValue('courserating'));
        //     $message = $this->trans("Form saved correctly");
        // }

        // $courserating = Configuration::get('COURSE_RATING');
        // $this->context->smarty->assign([
        //     'courserating' => $courserating,
        //     'message' => $message
        // ]);

        // return $this->fetch('module:' . $this->name . '/views/templates/admin/configuration.tpl');
    }

    public function hookDisplayBanner(array $params)
    {
        $this->context->smarty->assign([
            'my_module_name' => Configuration::get('MYMODULE_NAME'),
            'my_module_link' => $this->context->link->getModuleLink($this->name, 'display'),
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
     * Serialize products to xml and return the filename
     *
     * @param   array   $products           Products to serialize
     * @return string                       Filename
     */
    private function serializeProducts(array $products): string
    {
        $productsXml = $this->get('serializer')->serialize(
            $products,
            'xml',
            [
                'xml_root_node_name' => 'products',
                'xml_format_output' => true,
            ]
        );

        $filename = _PS_UPLOAD_DIR_ . 'products.xml';

        $this->get('filesystem')->dumpFile($filename, $productsXml);

        return $filename;
    }

    /**
     * Add an "XML export" to the product list
     *
     * @param  array        $hookParams         Hook parameters
     * @return string
     */
    public function hookDisplayDashboardToolbarIcons(array $hookParams): string
    {
        if ($this->isSymfonyContext() && $hookParams['route'] === 'admin_product_catalog') {
            $products = $this->get('mymodule.product_repository')->findAllByLangId(1);
            $filepath = $this->serializeProducts($products);

            return $this->get('twig')->render('@Modules/' . $this->name . '/views/templates/hook/download_link.twig', [
                'filepath' => _PS_BASE_URL_ . '/products.xml',
            ]);
        }

        return '';
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

    public function hookModuleRoutes(array $params): array
    {
        return [
            'display' => [
                'controller' => 'display',
                'rule' => 'mymodule-display',
                'keywords' => [],
                'params' => [
                    'module' => $this->name,
                    'fc' => 'module',
                    'controller' => 'display'
                ]
            ]
        ];
    }
}
