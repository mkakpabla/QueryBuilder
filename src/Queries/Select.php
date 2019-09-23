<?php

namespace Framework\Database\Queries;

use Zen\Database\Query;
use PDO;

class Select
{



    private $where = [];

    private $entity;

    private $order = [];

    private $limit;

    private $joins;


    private $params = [];
    /**
     * @var string
     */
    private $table;
    /**
     * @var Query
     */
    private $query;
    /**
     * @var array
     */
    private $columns;


    public function __construct(Query $query, string $table, array $columns = ['*'])
    {
        $this->query = $query;
        $this->table = $table;
        $this->columns = $columns;
    }

    /**
     * Spécifie la limite
     * @param int $length
     * @param int $offset
     * @return Query
     */
    public function limit(int $length, int $offset = 0): self
    {
        $this->limit = "$offset, $length";
        return $this;
    }

    /**
     * Spécifie l'ordre de récupération
     * @param string $order
     * @return Query
     */
    public function order(string $order): self
    {
        $this->order[] = $order;
        return $this;
    }

    /**
     * Ajoute une liaison
     * @param string $table
     * @param string $condition
     * @param string $type
     * @return Query
     */
    public function join(string $table, string $condition, string $type = "left"): self
    {
        $this->joins[$type][] = [$table, $condition];
        return $this;
    }

    /**
     * Définit la condition de récupération
     * @param array $conditions
     * @return Query
     */
    public function where(string ...$conditions): self
    {
        $this->where = array_merge($this->where, $conditions);
        return $this;
    }

    /**
     * Execute un COUNT() et renvoie la colonne
     * @return int
     * @throws \Exception
     */
    public function count(): int
    {
        $query = clone $this;
        return $query->select("COUNT($this->table.id)")->execute()->fetchColumn();
    }

    /**
     * Définit les paramètre pour la requête
     * @param array $params
     * @return Query
     */
    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Spécifie l'entité à utiliser
     * @param string $entity
     * @return Query
     */
    public function into(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Récupère un résultat
     */
    public function fetch()
    {
        $record = $this->execute()->fetch();
        if ($record === false) {
            return false;
        }
        if ($this->entity) {
            return $this->execute()->fetchObject($this->entity);
        }
        return $record;
    }

    /**
     * Retournera un résultat ou renvoie une exception
     * @return bool|mixed
     * @throws NoRecordException
     */
    public function fetchOrFail()
    {
        $record = $this->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

    /**
     * Lance la requête
     * @return Collection
     * @throws \Exception
     */
    public function fetchAll(): Collection
    {
        if ($this->entity) {
            $exc = $this->execute();
            $exc->setFetchMode(PDO::FETCH_CLASS, $this->entity);
            return new Collection(
                $exc->fetchAll()
            );
        }
        return new Collection(
            $this->execute()->fetchAll()
        );
    }


    /**
     * Exécute la requête
     * @return string
     * @throws \Exception
     */
    private function execute()
    {
        $query = $this->__toString();
        if (!empty($this->params)) {
            $statement = $this->query->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        }
        $statement = $this->query->pdo->prepare($query);
        $statement->execute();
        return $statement;
    }


    /**
     * Génère la requête SQL
     * @return string
     * @throws \Exception
     */
    public function __toString()
    {
        $parts = ['SELECT'];
        if ($this->columns) {
            $parts[] = join(', ', $this->columns);
        } else {
            $parts[] = '*';
        }
        $parts[] = 'FROM';
        $parts[] = $this->table;
        if (!empty($this->joins)) {
            foreach ($this->joins as $type => $joins) {
                foreach ($joins as [$table, $condition]) {
                    $parts[] = strtoupper($type) . " JOIN $table ON $condition";
                }
            }
        }
        if (!empty($this->where)) {
            $parts[] = "WHERE";
            $parts[] = "(" . join(') AND (', $this->where) . ')';
        }
        if (!empty($this->order)) {
            $parts[] = 'ORDER BY';
            $parts[] = join(', ', $this->order);
        }
        if ($this->limit) {
            $parts[] = 'LIMIT ' . $this->limit;
        }
        return join(' ', $parts);
    }
}
