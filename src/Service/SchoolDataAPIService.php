<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;

class SchoolDataAPIService
{
    private string $url;
    private string $apiKey;
    private EntityManagerInterface $em;

    public function __construct(string $url, string $apiKey, EntityManagerInterface $em)
    {
        $this->url = $url;
        $this->apiKey = $apiKey;
        $this->em = $em;
    }

    public function fetchSchoolData($institutionNumber, $establishmentNumber): array
    {
        if (!$institutionNumber || !$establishmentNumber) {
            return [];
        }


        $client = new Client();

        // Fetch instellingslocatie gegevens
        $locationUrl = sprintf(
            '%sinstellingsgegevens/instellingslocatie/v1/instellingslocatie/%s/%s?apikey=%s',
            $this->url,
            $institutionNumber,
            $establishmentNumber,
            $this->apiKey
        );

        try {
            $locationResponse = $client->request('GET', $locationUrl, [
                'headers' => ['Accept' => 'application/json']
            ]);
            $locationData = json_decode($locationResponse->getBody(), true);
        } catch (\Exception $e) {
            return [];
        }

        // Fetch instelling gegevens
        $institutionUrl = sprintf(
            '%sinstellingsgegevens/instelling/v2/instelling/%s?apikey=%s',
            $this->url,
            $institutionNumber,
            $this->apiKey
        );

        try {
            $institutionResponse = $client->request('GET', $institutionUrl, [
                'headers' => ['Accept' => 'application/json']
            ]);
            $institutionData = json_decode($institutionResponse->getBody(), true);
        } catch (\Exception $e) {
            $institutionData = [];
        }

        // Combine data
        return [
            'name' => $locationData['instelling_naam'] ?? null,
            'address' => trim(($locationData['instellingslocatie_straatnaam'] ?? '') . ' ' . ($locationData['instellingslocatie_huisnummer'] ?? '')),
            'postal_code' => $locationData['instellingslocatie_postcode'] ?? null,
            'region' => $locationData['instellingslocatie_gemeente'] ?? null,
            'city' => $locationData['instellingslocatie_gemeente'] ?? null,
            'phone' => $locationData['instellingslocatie_telefoonnummers'][0] ?? null,
            'level' => $this->determineLevel($locationData['instellingslocatie_hoofdstructuur'] ?? []),
            'education_type' => $this->determineEducationType($locationData['instellingslocatie_hoofdstructuur'] ?? []),
            'website' => $institutionData['instelling_website'] ?? null,
            'email' => $institutionData['instelling_email'] ?? null,
        ];
    }

    private function determineLevel(array $hoofdstructuur): ?string
    {
        $codes = array_map(fn($obj) => $obj['code'], $hoofdstructuur);
        $eersteCijfers = array_map(fn($code) => $code[0], $codes);

        if (in_array('1', $eersteCijfers) && in_array('2', $eersteCijfers)) {
            return 'primary_education';
        } elseif (in_array('1', $eersteCijfers)) {
            return 'pre_primary_education';
        } elseif (in_array('2', $eersteCijfers)) {
            return 'only_primary_education';
        } elseif (in_array('3', $eersteCijfers)) {
            return 'secondary_education';
        }

        return null;
    }

    private function determineEducationType(array $hoofdstructuur): ?string
    {
        $codes = array_map(fn($obj) => $obj['code'], $hoofdstructuur);
        $isBuitengewoon = array_filter($codes, fn($code) => strlen($code) >= 2 && $code[1] === '2');

        if (!empty($codes) && empty($isBuitengewoon)) {
            return 'regular_education';
        } elseif (!empty($isBuitengewoon)) {
            return 'special_needs_education';
        }

        return null;
    }

    public function bulkImportSchoolData($postcode, $onderwijsvormen)
    {
        $client = new Client();
        $totalPages = 1;
        $pageCount = 0;
        $schoolDataCollection = [];
        while ($pageCount < $totalPages) {
            $pageCount++;
            $url = sprintf(
                '%sinstellingsgegevens/instellingslocatie/v1/instellingslocatie?apikey=%s&size=20&page=%s&filter_instellingslocatie_postcode=%s',
                $this->url,
                $this->apiKey,
                $pageCount,
                $postcode
            );
            try {
                $response = $client->request('GET', $url, [
                    'headers' => ['Accept' => 'application/json']
                ]);
                $data = json_decode($response->getBody(), true);
                $totalPages = $data['meta']['total_pages'];
                if (!isset($data['content']) || !is_array($data['content'])) {
                    return [];
                }
                $schoolDataCollection = array_merge($schoolDataCollection, $data['content']);

            } catch (\Exception $e) {
                return [];
            }
        }
        foreach ($schoolDataCollection as $key => $schoolData) {
            $level = $this->determineLevel($schoolData['instellingslocatie_hoofdstructuur'] ?? []);
            if(!in_array($level, $onderwijsvormen)) {
                unset($schoolDataCollection[$key]);
            }
        }

        return $schoolDataCollection;
    }
}
