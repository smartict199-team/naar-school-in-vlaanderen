<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Api\Details;
use App\Model\Api\Levels\Levels;
use App\Repository\SchoolLevelRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetLevelsController extends AbstractController
{
    private SchoolLevelRepository $schoolLevelRepository;

    public function __construct(
        SchoolLevelRepository $schoolLevelRepository
    ) {
        $this->schoolLevelRepository = $schoolLevelRepository;
    }

    /**
     * @Route("/api/levels", name="api_get_levels", methods={"GET"})
     *
     * @OA\Tag(name="Details")
     *
     * @OA\Get(description="Get all levels")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all levels",
     *     @Model(type=Levels::class)
     * )
     */
    public function __invoke(): Response
    {
        $levels = $this->schoolLevelRepository->findAll();

        $data = [];
        foreach ($levels as $level) {
            $details = new Details();
            $details->id = $level->getId();
            $details->label = $level->getName();

            $data[] = $details;
        }

        $gradesResponse = new Levels();
        $gradesResponse->levels = $data;

        return new JsonResponse($gradesResponse);
    }
}
