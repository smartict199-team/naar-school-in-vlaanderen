<?php

declare(strict_types=1);

namespace App\Model\Form;

class SchoolCityChoice
{
    private string $postalCode;
    private string $city;
    private string $region;

    public function __construct(string $postalCode, string $city, string $region)
    {
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->region = $region;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }
}
