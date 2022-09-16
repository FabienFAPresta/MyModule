<?php

declare(strict_types=1);

namespace PrestaShop\Module\MyModule\Controller\Admin;

use Doctrine\Common\Cache\CacheProvider;
//use PrestaShop\Module\MyModule\CommentMyModule;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

//require_once(_PS_MODULE_DIR_ . 'mymodule/classes/comment.class.php');

class AdminTestController extends FrameworkBundleAdminController
{
    private $cache;

    public function __construct(CacheProvider $cache)
    {
        $this->cache = $cache;
    }

    public function indexAction(): Response
    {
        return $this->render('@Modules/mymodule/views/templates/admin/admintest.html.twig');
    }
}
