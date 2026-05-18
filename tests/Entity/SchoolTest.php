<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\School;
use PHPUnit\Framework\TestCase;

class SchoolTest extends TestCase
{
    /**
     * @dataProvider websiteProvider
     */
    public function testGetWebsiteUrl(?string $website, ?string $expected): void
    {
        $school = new School();
        $school->setWebsite($website);

        self::assertSame($expected, $school->getWebsiteUrl());
    }

    /**
     * @return iterable<string, array{0: ?string, 1: ?string}>
     */
    public function websiteProvider(): iterable
    {
        yield 'null website' => [null, null];
        yield 'empty website' => ['  ', null];
        yield 'https website' => ['https://school.example', 'https://school.example'];
        yield 'http website' => ['http://school.example', 'http://school.example'];
        yield 'protocol relative website' => ['//school.example', 'https://school.example'];
        yield 'missing protocol website' => ['school.example', 'https://school.example'];
        yield 'missing protocol with www and spaces' => [' www.school.example ', 'https://www.school.example'];
    }
}
