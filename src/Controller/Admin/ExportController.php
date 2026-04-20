<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Export\Kiesjeschool;
use App\Model\Role;
use App\Repository\SchoolRepository;
use App\Repository\SchoolYearRepository;
use App\Service\Exports\KiesjeschoolCsvGenerator;
use App\Service\Exports\KiesjeschoolDataTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    public const YEAR_ID = 'yearId';

    /**
     * @return array<string, string>
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            SchoolRepository::class => '?' . SchoolRepository::class,
            SchoolYearRepository::class => '?' . SchoolYearRepository::class,
            KiesjeschoolDataTransformer::class => '?' . KiesjeschoolDataTransformer::class,
            KiesjeschoolCsvGenerator::class => '?' . KiesjeschoolCsvGenerator::class,
        ]);
    }

    /**
     * @Route("/admin/exports", name="export_index")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Role::ROLE_SUPER_ADMIN);

        $schoolYears = $this->get(SchoolYearRepository::class)->findActiveBackendSchoolYears();

        return $this->render('Export/index.html.twig', [
            'schoolYears' => $schoolYears,
        ]);
    }

    /**
     * @Route("/admin/exports/kiesjeschool", name="export_kiesjeschool")
     */
    public function kiesjeschool(Request $request): Response
    {
        $this->denyAccessUnlessGranted(Role::ROLE_SUPER_ADMIN);

        $schools = $this->get(SchoolRepository::class)->findAll();
        $year = $this->get(SchoolYearRepository::class)->find($request->get(self::YEAR_ID));
        $transformer = $this->get(KiesjeschoolDataTransformer::class);

        $data = [];
        foreach ($schools as $school) {
            $schoolData = $transformer->transform($school, $year);
            if (!$schoolData instanceof Kiesjeschool) {
                continue;
            }
            $data[] = $transformer->transform($school, $year);
        }

        $response = new Response($this->get(KiesjeschoolCsvGenerator::class)->generate($data));

        $disposition = HeaderUtils::makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf(
                'NSIV %s-%s %s.csv',
                $year->getStartYear(),
                $year->getEndYear(),
                (new \DateTime())->format('d-m-Y H:i:s')
            ),
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }
}
