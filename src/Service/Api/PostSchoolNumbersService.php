<?php

declare(strict_types=1);

namespace App\Service\Api;

use App\Entity\SchoolYear;
use App\Model\Api\Request\PostSchoolNumbersRequest;
use App\Model\Form\SchoolEducationsData;
use App\Repository\SchoolRepository;
use App\Repository\SchoolYearRepository;
use App\Service\Api\Transformer\PostSchoolNumbers\DataService;
use App\Service\Educations\EducationsFormDataTransformer;
use App\Service\Educations\EducationsService;
use Symfony\Component\Form\FormFactoryInterface;

class PostSchoolNumbersService
{
    private SchoolRepository $schoolRepository;
    private SchoolYearRepository $schoolYearRepository;
    private EducationsFormDataTransformer $educationsFormDataTransformer;
    private FormFactoryInterface $formFactory;
    private DataService $dataService;
    private EducationsService $educationsService;

    public function __construct(
        SchoolRepository $schoolRepository,
        SchoolYearRepository $schoolYearRepository,
        EducationsFormDataTransformer $educationsFormDataTransformer,
        FormFactoryInterface $formFactory,
        DataService $dataService,
        EducationsService $educationsService
    ) {
        $this->schoolRepository = $schoolRepository;
        $this->schoolYearRepository = $schoolYearRepository;
        $this->educationsFormDataTransformer = $educationsFormDataTransformer;
        $this->formFactory = $formFactory;
        $this->dataService = $dataService;
        $this->educationsService = $educationsService;
    }

    public function handleRequest(PostSchoolNumbersRequest $request): void
    {
        $schools = $this->schoolRepository->findByEstablishmentNumber((string) $request->establishmentNumber);
        $year = $this->schoolYearRepository->findYearByStartAndEndYear($request->startYear, $request->endYear);

        if (!$year instanceof SchoolYear) {
            throw new \InvalidArgumentException('Year not found.');
        }

        if (0 === \count($schools)) {
            throw new \InvalidArgumentException('School not found.');
        }

        foreach ($schools as $school) {
            /** @var SchoolEducationsData $educationsData */
            $educationsData = $this->educationsFormDataTransformer->transform($school, $year);
            $newData = $this->dataService->enrichData($educationsData, $request);
            $this->educationsService->saveEducations($school, $year, $newData);
        }
    }
}
