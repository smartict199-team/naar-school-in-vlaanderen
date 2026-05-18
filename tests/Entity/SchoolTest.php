<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\School;
use PHPUnit\Framework\TestCase;

final class SchoolTest extends TestCase
{
    public function testGetWebsiteUrlReturnsNullWhenWebsiteIsMissingOrEmpty(): void
    {
        $school = new School();

        self::assertNull($school->getWebsiteUrl());

        $school->setWebsite('   ');
        self::assertNull($school->getWebsiteUrl());
    }

    public function testGetWebsiteUrlKeepsAbsoluteHttpOrHttpsUrl(): void
    {
        $school = new School();
        $school->setWebsite('http://example.org');

        self::assertSame('http://example.org', $school->getWebsiteUrl());

        $school->setWebsite('https://example.org');
        self::assertSame('https://example.org', $school->getWebsiteUrl());
    }

    public function testGetWebsiteUrlAddsHttpsWhenMissingScheme(): void
    {
        $school = new School();
        $school->setWebsite('www.example.org');

        self::assertSame('https://www.example.org', $school->getWebsiteUrl());
    }
}
