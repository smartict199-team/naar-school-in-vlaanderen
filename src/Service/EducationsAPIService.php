<?php

namespace App\Service;

use App\Entity\School;
use App\Entity\SchoolEducation;
use App\Entity\SchoolFinality;
use App\Entity\SchoolFormType;
use App\Entity\SchoolYear;
use App\Model\Form\SchoolEducationsData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EducationsAPIService extends AbstractController
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
    public function getEducationsFromAPI(?School $school): ?SchoolEducationsData
    {

        if (!$school) {
            return null;
        }

        $institutionNumber = $school->getInstitutionNumber();
        $establishmentNumbers = $school->getEstablishmentNumbers();
        $establishmentNumber = !empty($establishmentNumbers) ? $establishmentNumbers[0] : null;

        if($institutionNumber == $establishmentNumber) {
            $institutionNumber = 1;
        }

        $i = 0;
        /**
         * @var array<int, array<array-key, SchoolEducation>>
         */
        $educationsCollections = [];

        for ($j = 1; $j <= 9; $j++) {
            $educationsCollections[$j] = [];
        }

        foreach ($establishmentNumbers as $establishmentNumber)
        {
            $totalPages = 1;
            $pageCount = 0;
            $invalidEducations = [];
            while ($pageCount < $totalPages){
                $pageCount++;
                $apiUrl = "{$this->url}instellingsgegevens/onderwijsaanbod_so/v2/administratievegroep?apikey={$this->apiKey}&page={$pageCount}&size=100&filter_administratievegroep_ingericht=true&filter_instelling_nummer={$establishmentNumber}&filter_instellingslocatie_vestigingsnummer={$institutionNumber}";
                $client = new \GuzzleHttp\Client();

                try {
                    $response = $client->request('GET', $apiUrl);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'app.admin.schools.educations.error_getting_educations');
                    return null;
                }

                $data = json_decode($response->getBody(), true);
                if($pageCount == 1) {
                    $totalPages = $data['meta']['total_pages'];
                }

                if (!isset($data['content']) || !is_array($data['content'])) {
                    return null;
                }

                foreach ($data['content'] as $educationData) {
                    if (!isset($educationData['administratievegroep_leerjaar']['code'])) {
                        if($educationData['administratievegroep_code'] == 37749){
                            $index = 9;
                        }
                        else{
                        $invalidEducations[] = $educationData['administratievegroep_omschrijving'];
                        continue;
                        }
                    }
                    else{
                        $index = $educationData['administratievegroep_leerjaar']['code'];
                    }
                    if ($index == 7){
                        $index = 8;
                    }
                    elseif ($index == 8){
                        $index = 7;
                    }
                    $education = new SchoolEducation();
                    $education->setSchool($school);
                    $education->setName($this->formatEducationName($educationData['administratievegroep_omschrijving']));
                    $education->setAdministrativeGroups($educationData['administratievegroep_code']);
                    // if the finality field exists, set the finality
                    if (isset($educationData['administratievegroep_finaliteit'])) {
                        $finality = $educationData['administratievegroep_finaliteit']['code'];
                        $eduFinality = new SchoolFinality();
                        switch ($finality) {
                            case 'A':
                                $eduFinality = $this->em->getRepository(SchoolFinality::class)->findOneBy(['id' => 2]);
                                break;
                            case 'DU':
                                $eduFinality = $this->em->getRepository(SchoolFinality::class)->findOneBy(['id' => 3]);
                                break;
                            case 'DO':
                                $eduFinality = $this->em->getRepository(SchoolFinality::class)->findOneBy(['id' => 4]);
                                break;
                            default:
                                $eduFinality = $this->em->getRepository(SchoolFinality::class)->findOneBy(['id' => 1]);
                                break;
                        }
                        $education->setFinality($eduFinality);
                    }

                    $education->setPosition($i);
                    $formType = $this->em->getRepository(SchoolFormType::class)->findOneBy(['name' => $educationData['administratievegroep_onderwijsvorm']['code']]);
                    $education->setFormType($formType);
                    $educationsCollections[$index][] = $education;

                    $i++;
                }
            }
        }

        $schoolEducationsData = new SchoolEducationsData();
        $schoolEducationsData->setEducationsCollections($educationsCollections);
        $schoolEducationsData->setFormTypeVisibleOnFrontend(true);
        $schoolEducationsData->setFinalityVisibleOnFrontend(true);

        return $schoolEducationsData;
        }

    private function formatEducationName(string $name): string
    {
        // Eerst: 1ste/2e leerjaar A/B -> 1A/2B
        $name = preg_replace_callback(
            '/(\d+)(?:e|ste) leerjaar\s+([A-Z])/',
            function ($matches) {
                return $matches[1] . $matches[2];
            },
            $name
        );

        // Dan: leerjaar in de graad -> 3, 4, 5, 6
        $name = preg_replace_callback(
            '/(\d+)e leerjaar in de (\d+)e graad/',
            function ($matches) {
                // Map leerjaar + graad naar juiste cijfer
                $leerjaar = (int)$matches[1];
                $graad = (int)$matches[2];
                $jaar = (($graad - 1) * 2) + ($leerjaar);
                return $jaar;
            },
            $name
        );

        return $name;
    }
}
