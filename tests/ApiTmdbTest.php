<?php

namespace Louisperre\tests;

use Louisperre\ComposerTd3\ApiTmdb;
use PHPUnit\Framework\TestCase;

class ApiTmdbTest extends TestCase
{
    public function testSlugIsArray()
    {
        $api = new ApiTmdb();
        $this->assertIsArray($api->getSlugCategories());
    }

    public function testNameCategoriesIsArray()
    {
        $api = new ApiTmdb();
        $this->assertIsArray($api->getNameCategories());
    }

    public function testMovieWithCategoriesIsArray()
    {
        $api = new ApiTmdb();
        $this->assertIsArray($api->GetMoviesWithCategory('28-action'));
    }

    public function testMovieIsArray()
    {
        $api = new ApiTmdb();
        $this->assertIsArray($api->GetMovieInformation('https://www.themoviedb.org/movie/396263'));
    }

}