<?php


namespace Tests;

use Framework\Database\Query;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{

    /**
     * @var Query
     */
    private $query;

    protected function setUp(): void
    {
        $this->query = new Query();
    }

    public function testSelectQuery()
    {
        $query = $this->query->table('posts')
            ->select();
        $this->assertEquals("SELECT * FROM posts", (string)$query);
    }
}
