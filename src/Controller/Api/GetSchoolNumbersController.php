<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\SchoolYear;
use App\Model\Api\SchoolNumberResponse;
use App\Model\Form\SchoolNumbersData;
use App\Repository\SchoolRepository;
use App\Repository\SchoolYearRepository;
use App\Service\Api\Transformer\ApiViewTransformer;
use App\Service\Educations\EducationsFormDataTransformer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetSchoolNumbersController extends AbstractController
{
    private SchoolRepository $schoolRepository;
    private SchoolYearRepository $schoolYearRepository;
    private EducationsFormDataTransformer $dataTransformer;
    private ApiViewTransformer $apiViewTransformer;

    public function __construct(
        SchoolRepository $schoolRepository,
        SchoolYearRepository $schoolYearRepository,
        EducationsFormDataTransformer $dataTransformer,
        ApiViewTransformer $apiViewTransformer
    ) {
        $this->schoolRepository = $schoolRepository;
        $this->schoolYearRepository = $schoolYearRepository;
        $this->dataTransformer = $dataTransformer;
        $this->apiViewTransformer = $apiViewTransformer;
    }

    /**
     * @Route("/api/numbers/{establishmentNumber}/{startYear}/{endYear}", name="api_get_school_numbers", methods={"GET"})
     *
     * @OA\Tag(name="School numbers")
     *
     * @OA\Get(description="Get the numbers for a specific school")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns the education numbers for a school",
     *     @Model(type=SchoolNumberResponse::class)
     * )
     */
    public function __invoke(string $establishmentNumber, int $startYear, int $endYear): Response
    {
        $schools = $this->schoolRepository->findByEstablishmentNumber($establishmentNumber);
        $year = $this->schoolYearRepository->findYearByStartAndEndYear($startYear, $endYear);

        if (!$year instanceof SchoolYear) {
            return new JsonResponse('Year not found.', 404);
        }

        if (0 === \count($schools)) {
            return new JsonResponse('School not found.', 404);
        }

        $data = [];
        foreach ($schools as $school) {
            $schoolNumbersData = $this->dataTransformer->transform($school, $year, SchoolNumbersData::class);
            $data[] = $this->apiViewTransformer->getView($school, $year, $schoolNumbersData);
        }

        return new JsonResponse($data);
    }
}
