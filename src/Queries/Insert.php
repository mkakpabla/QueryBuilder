<?php


namespace Zen\Database\Queries;

use Zen\Database\Query;

class Insert
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

    public function __construct(Query $query, string $table, array $inputs)
    {
        $this->query = $query;
        $this->table = $table;
        $this->inputs = $inputs;
    }

    public function execute()
    {
        $query = $this->__toString();
        $statement = $this->query->pdo->prepare($query);
        $statement->execute(array_values($this->inputs));
        return $this->query->pdo->lastInsertId();
    }

    public function __toString()
    {
        $parts = ['INSERT INTO'];
        if ($this->table) {
            $parts[] = $this->table;
        }
        if ($this->inputs) {
            $parts[] = $this->buildColumnPart();
            $parts[] = $this->buildValuesPart();
        }
        return join(' ', $parts);
    }


    private function buildColumnPart()
    {
        return '('. implode(', ', array_keys($this->inputs)) . ')';
    }

    private function buildValuesPart()
    {
        $values = 'VALUES (';
        $keys = array_keys($this->inputs);
        foreach ($keys as $key => $value) {
            if (count($keys) == (int)$key + 1) {
                $values .= '?)';
            } else {
                $values .= '?, ';
            }
        }
        return $values;
    }
}
