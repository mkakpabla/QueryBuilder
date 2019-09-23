<?php

namespace Zen\Database;

use Zen\Database\Queries\Insert;
use Zen\Database\Queries\Select;
use Zen\Database\Queries\Update;
use PDO;

class Query
{

    /**
     * @var string
     */
    private $table;

    /**
     * @var PDO
     */
    public $pdo;


    public function __construct(PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }


    /**
     * Definit la table
     * @param string $table
     * @param null|string $alias
     * @return Query
     */
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Permet de faire des requetes de types SELECT
     * @param string ...$columns
     * @return Select
     */
    public function select(string ...$columns)
    {
        return new Select($this, $this->table, $columns);
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
}
