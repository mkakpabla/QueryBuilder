<?php


namespace Tests;

use PHPUnit\Framework\TestCase;
use Zen\Database\Query;

class TruncateTest extends TestCase
{


    public function testTruncateQuery()
    {
        $query = (new Query())->table('posts')->truncate();
        $this->assertEquals("TRUNCATE TABLE posts", (string)$query);
    }
}
