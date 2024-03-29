<?php


namespace Zen\Database\Queries;

use Exception;
use Zen\Database\Query;

class Delete
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


    /**
     * Définit la condition de récupération
     * @param array $condition
     * @param string $separator
     * @return Update
     */
    public function where(array $condition, $separator = 'AND'): self
    {
        $this->query->where($condition, $separator);
        return $this;
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
        $this->parts = ['DELETE FROM'];
        if ($this->table) {
            $this->parts[] = $this->query->buildFrom();
        }
        if (!empty($this->query->where)) {
            $this->parts[] = "WHERE";
            $this->parts[] = "(" . join(') AND (', $this->query->where) . ')';
        }

        return join(' ', $this->parts);
    }
}
