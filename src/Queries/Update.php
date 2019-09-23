<?php


namespace Zen\Database\Queries;

use Zen\Database\Query;

class Update
{

    /**
     * @var Query
     */
    private $query;

    private $table;
    /**
     * @var array
     */
    private $inputs;
    /**
     * @var array
     */
    private $where = [];
    /**
     * @var array
     */
    private $parts = [];

    public function __construct(Query $query, $table, array $inputs)
    {
        $this->query = $query;
        $this->table = $table;
        $this->inputs = $inputs;
    }

    /**
     * Définit la condition de récupération
     * @param array $conditions
     * @return Update
     */
    public function where(array $condition): self
    {
        foreach ($condition as $key => $value) {
            $this->where[] = str_replace('?', $this->query->pdo->quote($value), $key);
        }
        return $this;
    }

    /***
     * Permet d'executer la requete sql
     * @return int
     * @throws \Exception
     */
    public function execute()
    {
        $query = $this->__toString();
        if (!in_array('WHERE', $this->parts)) {
            throw new \Exception('Update queries must contain a WHERE clause to prevent unwanted data loss');
        }
        $statement = $this->query->pdo->prepare($query);
        $statement->execute(array_values($this->inputs));
        return $statement->rowCount();
    }

    /***
     * Gènére la requete sql
     * @return string
     */
    public function __toString()
    {
        $this->parts = ['UPDATE'];
        if ($this->table) {
            $this->parts[] = $this->table;
        }
        $this->parts[] = 'SET';
        if ($this->inputs) {
            $this->parts[] = $this->getInputsKeys();
        }
        if (!empty($this->where)) {
            $this->parts[] = "WHERE";
            $this->parts[] = "(" . join(') AND (', $this->where) . ')';
        }
        return join(' ', $this->parts);
    }

    private function getInputsKeys()
    {
        return implode(' = ?, ', array_keys($this->inputs)) . ' = ?';
    }
}
