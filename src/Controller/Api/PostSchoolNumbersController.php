<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Api\Request\PostSchoolNumbersRequest;
use App\Service\Api\PostSchoolNumbersService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostSchoolNumbersController extends AbstractController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private PostSchoolNumbersService $postSchoolNumbersService;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PostSchoolNumbersService $postSchoolNumbersService
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->postSchoolNumbersService = $postSchoolNumbersService;
    }

    /**
     * @Route("/api/numbers/", name="api_post_school_numbers", methods={"POST"})
     *
     * @OA\Tag(name="School numbers")
     *
     * @OA\Post(description="Update education numbers for a school")
     *
     * @OA\RequestBody(@Model(type=PostSchoolNumbersRequest::class))
     */
    public function __invoke(Request $request): Response
    {
        $input = $this->serializer->deserialize($request->getContent(), PostSchoolNumbersRequest::class, 'json');
        $errors = $this->validator->validate($input);
        if ($errors->count()) {
            $result = [];
            foreach ($errors as $error) {
                $result[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse($result);
        }

        try {
            $this->postSchoolNumbersService->handleRequest($input);
        } catch (\Exception $exception) {
            if ($exception instanceof \InvalidArgumentException) {
                return new JsonResponse($exception->getMessage(), 404);
            }

            return new JsonResponse('Something went wrong.', 404);
        }

        return new JsonResponse('Success');
    }
}
