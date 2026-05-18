<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\School;
use PHPUnit\Framework\TestCase;

class SchoolTest extends TestCase
{
    public function testSetWebsiteAddsHttpsPrefixWhenMissingProtocol(): void
    {
        $school = new School();

        $school->setWebsite('www.example.org');

        self::assertSame('https://www.example.org', $school->getWebsite());
    }

    public function testSetWebsiteKeepsExistingProtocol(): void
    {
        $school = new School();

        $school->setWebsite('http://www.example.org');

        self::assertSame('http://www.example.org', $school->getWebsite());
    }

    public function testSetWebsiteConvertsWhitespaceOnlyValueToNull(): void
    {
        $school = new School();

        $school->setWebsite('   ');

        self::assertNull($school->getWebsite());
    }
}
