<?php

namespace Zen\Database\Queries;

use Exception;
use PDO;
use PDOStatement;
use Zen\Database\NoRecordException;
use Zen\Database\Query;

class Select
{

    private $select;

    private $where = [];

    private $entity;

    private $order = [];

    private $limit;

    private $joins;

    private $params = [];
    /**
     * @var Query
     */
    private $query;
    private $table;

    public function __construct(Query $query, $table, $fields)
    {
        $this->query = $query;
        $this->table = $table;
        $this->select = $fields;
    }

    /**
     * Spécifie la limite
     * @param int $length
     * @param int $offset
     * @return Select
     */
    public function limit(int $length, int $offset = 0): self
    {
        $this->limit = "$offset, $length";
        return $this;
    }

    /**
     * Spécifie l'ordre de récupération
     * @param string $order
     * @return Select
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
     * @return Select
     */
    public function join(string $table, string $condition, string $type = "left"): self
    {
        $this->joins[$type][] = [$table, $condition];
        return $this;
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

    /**
     * Execute un COUNT() et renvoie la colonne
     * @return int
     */
    public function count(): int
    {
        $table = current($this->table);
        return $this->query->select("COUNT($table.id)")->execute()->fetchColumn();
    }

    /**
     * Définit les paramètre pour la requête
     * @param array $params
     * @return Select
     */
    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Spécifie l'entité à utiliser
     * @param string $entity
     * @return Select
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
     * @return array
     * @throws Exception
     */
    public function fetchAll(): array
    {
        if ($this->entity) {
            $exc = $this->execute();
            $exc->setFetchMode(PDO::FETCH_CLASS, $this->entity);
            $exc->fetchAll();
        }
        return $this->execute()->fetchAll();
    }

    /**
     * Génère la requête SQL
     * @return string
     */
    public function __toString()
    {
        $parts = ['SELECT'];
        if ($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = '*';
        }
        $parts[] = 'FROM';
        $parts[] = $this->query->buildFrom();
        if (!empty($this->joins)) {
            foreach ($this->joins as $type => $joins) {
                foreach ($joins as [$table, $condition]) {
                    $parts[] = strtoupper($type) . " JOIN $table ON $condition";
                }
            }
        }
        if (!empty($this->query->where)) {
            $parts[] = "WHERE";
            $parts[] = "(" . join(') AND (', $this->query->where) . ')';
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

    /**
     * Exécute la requête
     * @return PDOStatement
     */
    private function execute()
    {
        $query = $this->__toString();
        if (!empty($this->params)) {
            $statement = $this->query->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        }
        return $this->query->pdo->query($query);
    }
}
