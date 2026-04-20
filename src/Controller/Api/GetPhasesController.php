<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Api\Details;
use App\Model\Api\Phases\Phases;
use App\Repository\SchoolPhaseRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetPhasesController extends AbstractController
{
    private SchoolPhaseRepository $phaseRepository;

    public function __construct(
        SchoolPhaseRepository $phaseRepository
    ) {
        $this->phaseRepository = $phaseRepository;
    }

    /**
     * @Route("/api/phases", name="api_get_phases", methods={"GET"})
     *
     * @OA\Tag(name="Details")
     *
     * @OA\Get(description="Get all phases")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all phases",
     *     @Model(type=Phases::class)
     * )
     */
    public function __invoke(): Response
    {
        $phases = $this->phaseRepository->findAll();

        $data = [];
        foreach ($phases as $phase) {
            $details = new Details();
            $details->id = $phase->getId();
            $details->label = $phase->getName();

            $data[] = $details;
        }

        $phasesResponse = new Phases();
        $phasesResponse->phases = $data;

        return new JsonResponse($phasesResponse);
    }
}
