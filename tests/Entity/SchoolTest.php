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
    public function testWebsiteUrlIsNormalized(?string $website, ?string $expectedWebsiteUrl): void
    {
        $school = new School();
        $school->setWebsite($website);

        self::assertSame($expectedWebsiteUrl, $school->getWebsiteUrl());
    }

    public function websiteUrlProvider(): \Generator
    {
        yield 'no website' => [null, null];
        yield 'empty website' => ['', null];
        yield 'whitespace website' => ['   ', null];
        yield 'https website' => ['https://school.example', 'https://school.example'];
        yield 'http website' => ['http://school.example', 'http://school.example'];
        yield 'website without scheme' => ['school.example', 'https://school.example'];
        yield 'www website without scheme' => ['www.school.example', 'https://www.school.example'];
        yield 'website with spaces around value' => ['  school.example/path ', 'https://school.example/path'];
        yield 'website with leading slashes' => ['//school.example', 'https://school.example'];
    }
}
