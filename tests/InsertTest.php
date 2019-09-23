<?php


namespace Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use Zen\Database\Query;

class InsertTest extends TestCase
{

    /**
     * @var PDO
     */
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('mysql:host=localhost;dbname=blog', 'root', 'root', [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }


    public function testInsertQuery()
    {
        $query = (new Query($this->pdo))->table('posts')
            ->insert([
                'title' => 'titre de test',
                'slug' => 'slug-test',
                'category_id' => 1,
            ]);
        $this->assertEquals(
            "INSERT INTO posts (title, slug, category_id) VALUES (?, ?, ?)",
            (string)$query
        );
    }

    public function testInsertQueryWithExecute()
    {
        $query = (new Query($this->pdo))->table('posts')
            ->insert([
                'title' => 'titre de test',
                'slug' => 'slug-test',
                'category_id' => 1,
            ])
        ->execute();
        $this->assertEquals($this->pdo->lastInsertId(), $query);
    }
}
