<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\School;
use PHPUnit\Framework\TestCase;

class SchoolTest extends TestCase
{
    public function testWebsiteUrlAddsHttpsSchemeWhenMissing(): void
    {
        $school = new School();
        $school->setWebsite('www.school.be');

        self::assertSame('https://www.school.be', $school->getWebsiteUrl());
    }

    public function testWebsiteUrlKeepsExistingHttpOrHttpsScheme(): void
    {
        $school = new School();
        $school->setWebsite('http://school.be');
        self::assertSame('http://school.be', $school->getWebsiteUrl());

        $school->setWebsite('https://school.be');
        self::assertSame('https://school.be', $school->getWebsiteUrl());
    }

    public function testWebsiteUrlReturnsNullForEmptyWebsite(): void
    {
        $school = new School();
        $school->setWebsite('   ');

        self::assertNull($school->getWebsiteUrl());
    }
}
