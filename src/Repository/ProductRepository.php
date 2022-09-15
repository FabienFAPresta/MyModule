<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\MyModule\Repository;

use Doctrine\DBAL\Connection;

class ProductRepository
{
    /** @var Connection     Database connection */
    private Connection $connection;

    /** @var string         Database prefix */
    private string $databasePrefix;

    /**
     * Constructor
     *
     * @param Connection    $connection         Database connection
     * @param string        $databasePrefix     Database prefix
     */
    public function __construct(Connection $connection, string $databasePrefix)
    {
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;
    }

    /**
     * Find all products based on it's language id
     *
     * @param   integer     $langId         Language id
     * @return  array                       List of products
     */
    public function findAllByLangId(int $langId): array
    {
        $prefix = $this->databasePrefix;
        $table = "${prefix}product";
        $langTable = "${prefix}product_lang";

        $query = "SELECT p.* FROM ${table} AS p LEFT JOIN ${langTable} pl ON (p.`id_product` = pl.`id_product`)";
        $query .= " WHERE pl.`id_product` = :langId";
        $statement = $this->connection->prepare($query);
        $statement->bindValue('langId', $langId);
        $res = $statement->executeQuery();

        return $res->fetchAllAssociative();
    }
}
