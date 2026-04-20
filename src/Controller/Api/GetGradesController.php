<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Api\Details;
use App\Model\Api\Grades\Grades;
use App\Repository\SchoolGradeRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetGradesController extends AbstractController
{
    private SchoolGradeRepository $schoolGradeRepository;

    public function __construct(
        SchoolGradeRepository $schoolGradeRepository
    ) {
        $this->schoolGradeRepository = $schoolGradeRepository;
    }

    /**
     * @Route("/api/grades", name="api_get_grades", methods={"GET"})
     *
     * @OA\Tag(name="Details")
     *
     * @OA\Get(description="Get all grades")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all grades",
     *     @Model(type=Grades::class)
     * )
     */
    public function __invoke(): Response
    {
        $grades = $this->schoolGradeRepository->findAll();

        $data = [];
        foreach ($grades as $grade) {
            $details = new Details();
            $details->id = $grade->getId();
            $details->label = $grade->getName();

            $data[] = $details;
        }

        $gradesResponse = new Grades();
        $gradesResponse->grades = $data;

        return new JsonResponse($gradesResponse);
    }
}
