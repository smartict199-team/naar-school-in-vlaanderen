<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\FilterFormType;
use App\Model\Form\FilterForm;
use App\Service\Search\SearchService;
use App\Service\Transformer\FilterFormModelTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    private FilterFormModelTransformer $filterModelTransformer;
    private SearchService $searchService;

    public function __construct(
        FilterFormModelTransformer $filterModelTransformer,
        SearchService $searchService
    ) {
        $this->filterModelTransformer = $filterModelTransformer;
        $this->searchService = $searchService;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request): Response
    {
        $requestData = $request->get('filter_form', false);
        $results = [];
        $submitted = false;

        if ($requestData) {
            $data = $this->filterModelTransformer->transformArrayDataToModel($requestData);
        } else {
            $data = new FilterForm();
        }

        $form = $this->createForm(FilterFormType::class, $data, ['method' => 'GET']);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $results = $this->searchService->search($data);
            $submitted = true;
        }

        shuffle($results);

        return $this->render('index.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
            'submitted' => $submitted,
        ]);
    }
}
