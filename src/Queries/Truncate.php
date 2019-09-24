<?php


namespace Zen\Database\Queries;


use Exception;
use Zen\Database\Query;

class Truncate
{
    /**
     * @var Query
     */
    private $query;

    private $table;
    /**
     * @var array
     */
    private $parts = [];

    public function __construct(Query $query, $table)
    {
        $this->query = $query;
        $this->table = $table;
    }

    /***
     * Permet d'executer la requete sql
     * @return int
     * @throws Exception
     */
    public function execute()
    {
        $query = $this->__toString();
        $statement = $this->query->pdo->prepare($query);
        return $statement->execute();
    }

    /***
     * Gènére la requete sql
     * @return string
     */
    public function __toString()
    {
        $this->parts = ['TRUNCATE TABLE'];
        if ($this->table) {
            $this->parts[] = $this->query->buildFrom();
        }
        return join(' ', $this->parts);
    }
}