<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\School;
use PHPUnit\Framework\TestCase;

class SchoolTest extends TestCase
{
    /**
     * @dataProvider websiteUrlProvider
     */
    public function testWebsiteUrlIsNormalized(?string $website, ?string $expected): void
    {
        $school = new School();
        $school->setWebsite($website);

        self::assertSame($expected, $school->getWebsiteUrl());
    }

    public function websiteUrlProvider(): iterable
    {
        yield 'null website' => [null, null];
        yield 'empty website' => ['  ', null];
        yield 'website without scheme' => ['school.example.be', 'https://school.example.be'];
        yield 'website with path and without scheme' => ['school.example.be/info', 'https://school.example.be/info'];
        yield 'http website' => ['http://school.example.be', 'http://school.example.be'];
        yield 'https website with spaces' => [' https://school.example.be ', 'https://school.example.be'];
        yield 'protocol relative website' => ['//school.example.be', 'https://school.example.be'];
    }
}
