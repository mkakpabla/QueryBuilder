<?php

namespace Zen\Database;

use Zen\Database\Queries\Delete;
use Zen\Database\Queries\Insert;
use Zen\Database\Queries\Select;
use Zen\Database\Queries\Truncate;
use Zen\Database\Queries\Update;
use PDO;

class Query
{

    /**
     * @var array
     */
    private $table = [];

    /**
     * @var PDO
     */
    public $pdo;
    /**
     * @var string
     */
    public $separator;
    /**
     * @var array
     */
    public $where;


    public function __construct(PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }


    /**
     * Definit le FROM
     * @param string $table
     * @param null|string $alias
     * @return Query
     */
    public function table(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->table[$table] = $alias;
        } else {
            $this->table[] = $table;
        }
        return $this;
    }


    /**
     * Spécifie les champs à récupérer
     * @param string[] ...$fields
     * @return Select
     */
    public function select(string ...$fields): Select
    {
        return new Select($this, $this->table, $fields);
    }



    /**
     * Permet de faire la requete de type INSERT
     * @param array $inputs
     * @return Insert
     */
    public function insert(array $inputs)
    {
        return new Insert($this, $this->table, $inputs);
    }

    /**
     * Permet de faire la requete de type UPDATE
     * @param array $inputs
     * @return Update
     */
    public function update(array $inputs)
    {
        return new Update($this, $this->table, $inputs);
    }

    /**
     * Permet de faire la requete de type UPDATE
     * @param array $inputs
     * @return Delete
     */
    public function delete()
    {
        return new Delete($this, $this->table);
    }

    /**
     * Permet de faire la requete de type UPDATE
     * @param array $inputs
     * @return Truncate
     */
    public function truncate()
    {
        return new Truncate($this, $this->table);
    }

    /**
     * Définit la condition de récupération
     * @param array $condition
     * @param string $separator
     * @return Update
     */
    public function where(array $condition, $separator = 'AND'): self
    {
        $conditions = [];
        foreach ($condition as $key => $value) {
            $conditions[] = str_replace('?', $this->pdo->quote($value), $key);
        }
        $this->where[]  = join(' '. $separator. ' ', $conditions);
        return $this;
    }

    /**
     * Construit le FROM a as b ....
     * @return string
     */
    public function buildFrom(): string
    {
        $from = [];
        foreach ($this->table as $key => $value) {
            if (is_string($key)) {
                $from[] = "$key as $value";
            } else {
                $from[] = $value;
            }
        }
        return join(', ', $from);
    }
}
