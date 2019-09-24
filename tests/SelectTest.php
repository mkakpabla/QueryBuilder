<?php


namespace Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use Zen\Database\Query;

class SelectTest extends TestCase
{

    /**
     * @var PDO
     */
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('mysql:host=localhost;dbname=blog', 'root', '', [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function testSimpleQuery()
    {
        $query = (new Query($this->pdo))->table('posts')->select('name');
        $this->assertEquals('SELECT name FROM posts', (string)$query);
    }


    public function testWithWhere()
    {
        $query = (new Query($this->pdo))
            ->table('posts', 'p')
            ->where(['id = ?' => 1])->select();
        $this->assertEquals('SELECT * FROM posts as p WHERE (id = \'1\')', (string)$query);
    }

    public function testLimitOrder()
    {
        $query = (new Query())
            ->table('posts', 'p')
            ->select('name')
            ->order('id DESC')
            ->order('name ASC')
            ->limit(10, 5);
        $this->assertEquals('SELECT name FROM posts as p ORDER BY id DESC, name ASC LIMIT 5, 10', (string)$query);
    }

    public function testJoinQuery()
    {
        $query = (new Query())
            ->table('posts', 'p')
            ->select('name')
            ->join('categories as c', 'c.id = p.category_id')
            ->join('categories as c2', 'c2.id = p.category_id', 'inner');
        $this->assertEquals('SELECT name ' .
            'FROM posts as p ' .
            'LEFT JOIN categories as c ON c.id = p.category_id ' .
            'INNER JOIN categories as c2 ON c2.id = p.category_id', (string)$query);
    }
}
