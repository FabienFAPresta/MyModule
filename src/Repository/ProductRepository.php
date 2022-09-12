<?php

namespace MyModule\Repository;

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
