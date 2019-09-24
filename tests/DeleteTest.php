<?php


namespace Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use Zen\Database\Query;

class DeleteTest extends TestCase
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

    public function testDeleteQuery()
    {
        $query = (new Query($this->pdo))->table('posts')
            ->delete();
        $this->assertEquals("DELETE FROM posts", (string)$query);
    }

    public function testDeleteQueryWithWhere()
    {
        $query = (new Query($this->pdo))->table('posts')
            ->where(['id = ?'=> 2])
            ->delete();
        $this->assertEquals("DELETE FROM posts WHERE (id = '2')", (string)$query);
    }
    /*
    public function testDeleteQueryWithWhereAndExecute()
    {
        $query = (new Query($this->pdo))->table('posts')
            ->where(['id = ?'=> 2])
            ->delete()->execute();
        $this->assertEquals("DELETE FROM posts WHERE (id = '2')", (string)$query);
    }*/
}
