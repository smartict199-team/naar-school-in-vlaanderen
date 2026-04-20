<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\School;
use App\Entity\SchoolEducation;
use App\Entity\SchoolYear;
use App\Form\School\SchoolBulkImportType;
use App\Form\School\SchoolEducationsCloneType;
use App\Form\School\SchoolEducationsType;
use App\Form\School\SchoolNumbersOverviewType;
use App\Model\Form\SchoolEducationsData;
use App\Model\Form\SchoolNumbersData;
use App\Model\Role;
use App\Model\User;
use App\Repository\SchoolEducationRepository;
use App\Repository\SchoolLevelRepository;
use App\Repository\SchoolOfficialEducationRepository;
use App\Repository\SchoolRepository;
use App\Repository\SchoolYearRepository;
use App\Service\Educations\EducationCloner;
use App\Service\Educations\EducationNumbersCsvGenerator;
use App\Service\Educations\EducationsFormDataTransformer;
use App\Service\Educations\EducationsService;
use App\Service\EducationsAPIService;
use App\Service\SchoolDataAPIService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MenuItemDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SchoolCrudController extends AbstractCrudController
{
    public const YEAR_ID = 'yearId';
    private EducationsAPIService $educationsAPIService;
    private EntityManagerInterface $em;
    private SchoolDataAPIService $schoolDataAPIService;

    public function __construct(EducationsAPIService $educationsAPIService, EntityManagerInterface $em, SchoolDataAPIService $schoolDataAPIService)
    {
        $this->em = $em;
        $this->educationsAPIService = $educationsAPIService;
        $this->schoolDataAPIService = $schoolDataAPIService;
    }
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class => '?' . TranslatorInterface::class,
            EducationsFormDataTransformer::class => '?' . EducationsFormDataTransformer::class,
            EducationsService::class => '?' . EducationsService::class,
            EducationCloner::class => '?' . EducationCloner::class,
            SchoolYearRepository::class => '?' . SchoolYearRepository::class,
            SchoolLevelRepository::class => '?' . SchoolLevelRepository::class,
            SchoolRepository::class => '?' . SchoolRepository::class,
            SchoolOfficialEducationRepository::class => '?' . SchoolOfficialEducationRepository::class,
            SchoolEducationRepository::class => '?' . SchoolEducationRepository::class,
            EducationNumbersCsvGenerator::class => '?' . EducationNumbersCsvGenerator::class,
        ]);
    }

    public static function getEntityFqcn(): string
    {
        return School::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setSearchFields(['name', 'address', 'city', 'region', 'postalCode'])->setPageTitle(Crud::PAGE_INDEX, 'app.admin.schools.index.title')->showEntityActionsAsDropdown(true);
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder(
            $searchDto,
            $entityDto,
            $fields,
            $filters
        );

        $user = $this->getUser();
        if ($user instanceof User && !$this->isGranted(Role::ROLE_SUPER_ADMIN)) {
            $qb->andWhere('entity.id IN (:ids)')
                ->setParameter('ids', $user->getSchools());
        }

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        yield CollectionField::new('establishment_numbers', 'app.admin.schools.general.establishment_number')
            ->hideOnIndex()
            ->setRequired(true);

        yield TextField::new('institution_number', 'app.admin.schools.general.institution_number')
            ->hideOnIndex()
            ->setRequired(true);

        yield TextField::new('name', 'app.admin.schools.general.name')
            ->setRequired(true);

        yield TextField::new('address', 'app.admin.schools.general.address')
            ->setRequired(true);

        yield TextField::new('postal_code', 'app.admin.schools.general.postal_code')
            ->hideOnIndex()
            ->setRequired(true);

        yield TextField::new('region', 'app.admin.schools.general.region')
            ->setRequired(true);

        yield TextField::new('city', 'app.admin.schools.general.city')
            ->hideOnIndex()
            ->setRequired(true);

        yield EmailField::new('email', 'app.admin.schools.general.email')
            ->hideOnIndex();

        yield TextField::new('phone_number', 'app.admin.schools.general.phone_number')
            ->hideOnIndex();

        yield TextField::new('website', 'app.admin.schools.general.website')
            ->hideOnIndex();



        $typeField = ChoiceField::new('type', 'app.admin.schools.general.type')
            ->setChoices(fn () => [
                'app.admin.schools.general.types.' . School::TYPE_PRE_PRIMARY_EDUCATION => School::TYPE_PRE_PRIMARY_EDUCATION,
                'app.admin.schools.general.types.' . School::TYPE_ONLY_PRIMARY_EDUCATION => School::TYPE_ONLY_PRIMARY_EDUCATION,
                'app.admin.schools.general.types.' . School::TYPE_PRIMARY_EDUCATION => School::TYPE_PRIMARY_EDUCATION,
                'app.admin.schools.general.types.' . School::TYPE_SECONDARY_EDUCATION => School::TYPE_SECONDARY_EDUCATION,
            ])
            ->setCustomOption(ChoiceField::OPTION_RENDER_EXPANDED, true)
            ->setRequired(true)
            ->setPermission(role::ROLE_GROUP_ADMIN);

        yield $typeField;

        $levelField = ChoiceField::new('level', 'app.admin.schools.general.level')->setChoices(fn () => [
                'app.admin.schools.general.levels.' . School::LEVEL_REGULAR_EDUCATION => School::LEVEL_REGULAR_EDUCATION,
                'app.admin.schools.general.levels.' . School::LEVEL_SPECIAL_NEEDS_EDUCATION => School::LEVEL_SPECIAL_NEEDS_EDUCATION,
            ])
            ->setCustomOption(ChoiceField::OPTION_RENDER_EXPANDED, true)
            ->setRequired(true);

        $levelField->getAsDto()->getDisplayedOn()->delete(Crud::PAGE_EDIT);

        yield $levelField;
    }

    public function editEducations(AdminContext $context): Response
    {
        $school = $context->getEntity()->getInstance();
        $year = $this->get(SchoolYearRepository::class)->find($context->getRequest()->get(self::YEAR_ID));
        $establishmentNumbers = $school->getEstablishmentNumbers();
        $institutionNumber = $school->getInstitutionNumber();

        if (!$year instanceof SchoolYear || !$school instanceof School || !$this->isGranted(Role::ROLE_EDIT_SCHOOL_EDUCATIONS)) {
            throw new ForbiddenActionException($context);
        }

        $transformer = $this->get(EducationsFormDataTransformer::class);
        $educationsData = $transformer->transform($school, $year);
        $form = $this->createForm(SchoolEducationsType::class, $educationsData, [
            'types' => $school->getSchoolLevelTypes(),
            'levels' => $school->getSchoolLevelLevels(),
            'year' => $year,
        ]);

        $educationCount = 0;

        array_walk_recursive($educationsData, function ($education) use (&$educationCount) {
            if ($education instanceof SchoolEducation) {
                $educationCount = $educationCount + 1;
            }
        });

        $schoolYears = array_filter($this->get(SchoolYearRepository::class)->findActiveBackendSchoolYears(), function ($schoolYear) use ($year) {
            return $year !== $schoolYear;
        });

        $cloneForm = $this->createForm(SchoolEducationsCloneType::class, [], [
             'schoolYears' => $schoolYears,
        ]);

        $form->handleRequest($context->getRequest());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $educationsService = $this->get(EducationsService::class);
                $educationsService->saveEducations($school, $year, $importedData ?? $form->getData());

                $this->addFlash('success', 'app.admin.schools.educations.success');

                return new RedirectResponse($context->getRequest()->getUri());
            }

            $this->addFlash('danger', 'app.admin.schools.educations.error');
        }

        $cloneForm->handleRequest($context->getRequest());

        if ($cloneForm->isSubmitted()) {
            if ($cloneForm->isValid()) {
                $educationsService = $this->get(EducationsService::class);
                $cloneService = $this->get(EducationCloner::class);
                $schoolEducationRepository = $this->get(SchoolEducationRepository::class);

                $educationsService->saveEducations($school, $year, new SchoolEducationsData());

                $educations = $school->getEducationsForYear($cloneForm->get('year')->getData());

                foreach ($educations as $education) {
                    $newEducation = $cloneService->clone($education, $year);
                    $schoolEducationRepository->persist($newEducation);
                }

                $schoolEducationRepository->flush();

                $this->addFlash('success', 'app.admin.schools.educations.success');

                return new RedirectResponse($context->getRequest()->getUri());
            }
        }

        return $this->render('Admin/School/edit_educations.html.twig', [
            'school' => $school,
            'schoolYear' => $year,
            'institutionNumber' => $institutionNumber,
            'establishmentNumbers' => $establishmentNumbers,
            'form' => $form->createView(),
            'cloneForm' => $cloneForm->createView(),
            'yearID' => $year->getId(),
            'educationCount' => $educationCount,
            'schoolYearLinks' => $this->getSchoolYearLinks($school, 'editEducations'),
            'importUrl' => \count($educationsData->getEducationsCollections()) <= 0 ? $this->get(AdminUrlGenerator::class)
                ->set(EA::CRUD_ACTION, 'importEducations')
                ->set(EA::CRUD_CONTROLLER_FQCN, self::class)
                ->set(EA::ENTITY_ID, $school->getId())
                ->set(self::YEAR_ID, $year->getId())
                ->generateUrl() : false,
            'subMenu' => $this->getSubMenu($context, $school),
            'exportUrl' => $this->get(AdminUrlGenerator::class)
                ->set(EA::CRUD_ACTION, 'exportNumbers')
                ->set(EA::CRUD_CONTROLLER_FQCN, __CLASS__)
                ->set(EA::ENTITY_ID, $school->getId())
                ->set(self::YEAR_ID, $context->getRequest()->get(self::YEAR_ID))
                ->set(EA::SUBMENU_INDEX, 4)
                ->generateUrl(),
        ]);
    }

    public function importEducations(AdminContext $context): Response
    {
        $school = $context->getEntity()->getInstance();
        $year = $this->get(SchoolYearRepository::class)->find($context->getRequest()->get(self::YEAR_ID));
        if (!$year instanceof SchoolYear || !$school instanceof School || !$this->isGranted(Role::ROLE_IMPORT_SCHOOL_EDUCATIONS)) {
            throw new ForbiddenActionException($context);
        }

        $redirectUrl = $this->get(AdminUrlGenerator::class)
            ->set(EA::CRUD_ACTION, 'editEducations')
            ->set(EA::CRUD_CONTROLLER_FQCN, self::class)
            ->set(EA::ENTITY_ID, $school->getId())
            ->set(self::YEAR_ID, $year->getId())
            ->set(EA::SUBMENU_INDEX, 1)
            ->generateUrl();

        $levels = $this->get(SchoolLevelRepository::class)->findByLevelsTypes($school->getSchoolLevelLevels(), $school->getSchoolLevelTypes());
        $officialEducations = $this->get(SchoolOfficialEducationRepository::class)->findByLevelsEstablishmentNumbers($levels, $school->getEstablishmentNumbers());

        if (\count($officialEducations) > 0) {
            foreach ($officialEducations as $officialEducation) {
                $education = new SchoolEducation();
                $education->setYear($year);
                $education->setSchool($school);
                $education->setName($officialEducation->getName());
                $education->setLevel($officialEducation->getLevel());
                $education->setFormType($officialEducation->getFormType());

                $school->addEducation($education);
            }

            $this->get(SchoolRepository::class)->save($school);

            return $this->redirect($redirectUrl);
        }

        $this->addFlash('danger', $this->get(TranslatorInterface::class)->trans('app.admin.schools.educations.import.error', [
            '%establishmentNumber%' => implode(', ', $school->getEstablishmentNumbers()),
        ]));

        return $this->redirect($redirectUrl);
    }

    public function exportNumbers(AdminContext $context): Response
    {
        $school = $context->getEntity()->getInstance();
        $year = $this->get(SchoolYearRepository::class)->find($context->getRequest()->get(self::YEAR_ID));
        if (!$year instanceof SchoolYear || !$school instanceof School || !$this->isGranted(Role::ROLE_EXPORT_SCHOOL_EDUCATION_NUMBERS)) {
            throw new ForbiddenActionException($context);
        }

        $transformer = $this->get(EducationsFormDataTransformer::class);
        $schoolNumbersData = $transformer->transform($school, $year, SchoolNumbersData::class);

        $response = new Response($this->get(EducationNumbersCsvGenerator::class)->generate($schoolNumbersData));
        $disposition = HeaderUtils::makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s %s-%s %s.csv', $school->getName() ?? '', $year->getStartYear(), $year->getEndYear(), (new \DateTime())->format('d-m-Y H:i:s')),
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }

    public function editNumbers(AdminContext $context): Response
    {
        $request = $context->getRequest();
        $school = $context->getEntity()->getInstance();
        $year = $this->get(SchoolYearRepository::class)->find($request->get(self::YEAR_ID));

        if (!$year instanceof SchoolYear || !$school instanceof School || !$this->isGranted(Role::ROLE_EDIT_SCHOOL_EDUCATION_NUMBERS)) {
            throw new ForbiddenActionException($context);
        }

        $transformer = $this->get(EducationsFormDataTransformer::class);
        $form = $this->createForm(SchoolNumbersOverviewType::class, $transformer->transform($school, $year, SchoolNumbersData::class), [
            'types' => $school->getSchoolLevelTypes(),
            'levels' => $school->getSchoolLevelLevels(),
            'year' => $year,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $educationsService = $this->get(EducationsService::class);
                $educationsService->saveNumbers($form->getData());

                $this->addFlash('success', 'app.admin.schools.numbers.success');

                return new RedirectResponse($request->getUri());
            }

            $this->addFlash('danger', 'app.admin.schools.numbers.error');
        }

        return $this->render('Admin/School/edit_numbers.html.twig', [
            'school' => $school,
            'schoolYear' => $year,
            'schoolYearLinks' => $this->getSchoolYearLinks($school, 'editNumbers'),
            'form' => $form->createView(),
            'subMenu' => $this->getSubMenu($context, $school),
            'exportUrl' => $this->get(AdminUrlGenerator::class)
                ->set(EA::CRUD_ACTION, 'exportNumbers')
                ->set(EA::CRUD_CONTROLLER_FQCN, __CLASS__)
                ->set(EA::ENTITY_ID, $school->getId())
                ->set(self::YEAR_ID, $context->getRequest()->get(self::YEAR_ID))
                ->set(EA::SUBMENU_INDEX, 4)
                ->generateUrl(),
        ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        $activeYears = $this->get(SchoolYearRepository::class)->findActiveBackendSchoolYears();

        foreach ($activeYears as $activeYear) {
            $dividerAction = Action::new('divider' . $activeYear->getId(), '', '')
                ->linkToUrl('#')->setTemplatePath('Admin/School/dropdown_divider.html.twig');

            $actions->add(Crud::PAGE_INDEX, $dividerAction);

            $label = $this->get(TranslatorInterface::class)->trans('app.admin.schools.index.export_numbers', [
                '%startYear%' => $activeYear->getStartYear(),
                '%endYear%' => $activeYear->getEndYear(),
            ]);

            $exportNumbersAction = Action::new('exportNumbers' . $activeYear->getId(), $label, 'fa fa-download')
                ->linkToUrl(function (School $school) use ($activeYear): string {
                    return $this->get(AdminUrlGenerator::class)
                        ->set(EA::CRUD_ACTION, 'exportNumbers')
                        ->set(EA::CRUD_CONTROLLER_FQCN, self::class)
                        ->set(EA::ENTITY_ID, $school->getId())
                        ->set(self::YEAR_ID, $activeYear->getId())
                        ->set(EA::SUBMENU_INDEX, 3)
                        ->generateUrl();
                });

            $actions->add(Crud::PAGE_INDEX, $exportNumbersAction);
            $actions->setPermission('exportNumbers' . $activeYear->getId(), Role::ROLE_EXPORT_SCHOOL_EDUCATION_NUMBERS);

            $label = $this->get(TranslatorInterface::class)->trans('app.admin.schools.index.edit_educations', [
                '%startYear%' => $activeYear->getStartYear(),
                '%endYear%' => $activeYear->getEndYear(),
            ]);

            $editEducationsAction = Action::new('editEducations' . $activeYear->getId(), $label, 'fa fa-list')
                ->linkToUrl(function (School $school) use ($activeYear): string {
                    return $this->get(AdminUrlGenerator::class)
                        ->set(EA::CRUD_ACTION, 'editEducations')
                        ->set(EA::CRUD_CONTROLLER_FQCN, self::class)
                        ->set(EA::ENTITY_ID, $school->getId())
                        ->set(self::YEAR_ID, $activeYear->getId())
                        ->set(EA::SUBMENU_INDEX, 1)
                        ->generateUrl();
                });

            $actions->add(Crud::PAGE_INDEX, $editEducationsAction);
            $actions->setPermission('editEducations' . $activeYear->getId(), Role::ROLE_EDIT_SCHOOL_EDUCATION_NUMBERS);

            $label = $this->get(TranslatorInterface::class)->trans('app.admin.schools.index.edit_numbers', [
                '%startYear%' => $activeYear->getStartYear(),
                '%endYear%' => $activeYear->getEndYear(),
            ]);

            $editNumbersAction = Action::new('editNumbers' . $activeYear->getId(), $label, 'fa fa-table')
                ->linkToUrl(function (School $school) use ($activeYear): string {
                    return $this->get(AdminUrlGenerator::class)
                        ->set(EA::CRUD_ACTION, 'editNumbers')
                        ->set(EA::CRUD_CONTROLLER_FQCN, self::class)
                        ->set(EA::ENTITY_ID, $school->getId())
                        ->set(self::YEAR_ID, $activeYear->getId())
                        ->set(EA::SUBMENU_INDEX, 2)
                        ->generateUrl();
                });

            $actions->setPermission('editNumbers' . $activeYear->getId(), Role::ROLE_EDIT_SCHOOL_EDUCATION_NUMBERS);
            $actions->add(Crud::PAGE_INDEX, $editNumbersAction);

            $translation = $activeYear->isCurrent() ? 'app.admin.schools.index.current_year' : 'app.admin.schools.index.year';
            $label = $this->get(TranslatorInterface::class)->trans($translation, [
                '%startYear%' => $activeYear->getStartYear(),
                '%endYear%' => $activeYear->getEndYear(),
            ]);

            $schoolYearAction = Action::new('schoolYear' . $activeYear->getId(), $label, '')
                ->linkToUrl('#')->setTemplatePath('Admin/School/dropdown_header.html.twig');

            $actions->add(Crud::PAGE_INDEX, $schoolYearAction);
        }

        $deleteAction = $actions->getAsDto(Crud::PAGE_INDEX)->getAction(Crud::PAGE_INDEX, Action::DELETE);
        if (null !== $deleteAction) {
            $deleteAction->setLabel('app.admin.schools.index.delete');
        }

        $editAction = $actions->getAsDto(Crud::PAGE_INDEX)->getAction(Crud::PAGE_INDEX, Action::EDIT);
        if (null !== $editAction) {
            $editAction->setLabel('app.admin.schools.index.edit');
        }

        $saveAction = $actions->getAsDto(Crud::PAGE_NEW)->getAction(Crud::PAGE_NEW, Action::SAVE_AND_RETURN);
        if (null !== $saveAction) {
            $saveAction->setLabel('app.admin.schools.edit.save');
        }

        $newAction = $actions->getAsDto(Crud::PAGE_INDEX)->getAction(Crud::PAGE_INDEX, Action::NEW);
        if (null !== $newAction) {
            $newAction->setLabel('app.admin.schools.create.save');
        }

        $actions->disable(Action::SAVE_AND_CONTINUE, Action::SAVE_AND_ADD_ANOTHER);

        $actions->setPermission(Action::NEW, Role::ROLE_ADD_SCHOOL);
        $actions->setPermission(Action::DELETE, Role::ROLE_DELETE_SCHOOL);
        $actions->setPermission(Action::EDIT, Role::ROLE_EDIT_SCHOOL);

        $bulkImportAction = Action::new('bulkImport', 'Bulk Import', 'fa fa-upload')
            ->linkToCrudAction('bulkImport')
            ->setCssClass('btn btn-primary')
            ->createAsGlobalAction(); // Maak het een globale actie (bovenaan de index)

        $actions->add(Crud::PAGE_INDEX, $bulkImportAction);
        $actions->setPermission('bulkImport', Role::ROLE_ADD_SCHOOL);


        return $actions;
    }

    /**
     * @return array<array-key, MenuItemDto>
     */
    public function getSubMenu(AdminContext $context, School $school): array
    {
        $items = [];
        $items[] = MenuItem::linkToUrl('app.admin.menu.edit_educations', 'fas fa-list', $this->get(AdminUrlGenerator::class)
            ->set(EA::CRUD_ACTION, 'editEducations')
            ->set(EA::CRUD_CONTROLLER_FQCN, __CLASS__)
            ->set(EA::ENTITY_ID, $school->getId())
            ->set(self::YEAR_ID, $context->getRequest()->get(self::YEAR_ID))
            ->set(EA::SUBMENU_INDEX, 1)
            ->generateUrl()
        )->setPermission(Role::ROLE_EDIT_SCHOOL_EDUCATIONS)->getAsDto();

        $items[] = MenuItem::linkToUrl('app.admin.menu.edit_numbers', 'fas fa-table', $this->get(AdminUrlGenerator::class)
            ->set(EA::CRUD_ACTION, 'editNumbers')
            ->set(EA::CRUD_CONTROLLER_FQCN, __CLASS__)
            ->set(EA::ENTITY_ID, $school->getId())
            ->set(self::YEAR_ID, $context->getRequest()->get(self::YEAR_ID))
            ->set(EA::SUBMENU_INDEX, 2)
            ->generateUrl()
        )->setPermission(Role::ROLE_EDIT_SCHOOL_EDUCATION_NUMBERS)->getAsDto();

        $items[] = MenuItem::linkToUrl('app.admin.menu.edit_school', 'fas fa-edit', $this->get(AdminUrlGenerator::class)
            ->set(EA::CRUD_ACTION, Crud::PAGE_EDIT)
            ->set(EA::CRUD_CONTROLLER_FQCN, __CLASS__)
            ->set(EA::ENTITY_ID, $school->getId())
            ->set(EA::SUBMENU_INDEX, 3)
            ->generateUrl()
        )->setPermission(Role::ROLE_EDIT_SCHOOL)->getAsDto();

        return $items;
    }

    /**
     * @return array<array-key, array{url: string, year: SchoolYear}>
     */
    private function getSchoolYearLinks(School $school, string $crudAction): array
    {
        $schoolYears = $this->get(SchoolYearRepository::class)->findActiveBackendSchoolYears();

        $links = [];
        foreach ($schoolYears as $schoolYear) {
            $links[] = [
                'url' => $this->get(AdminUrlGenerator::class)
                    ->set(EA::CRUD_ACTION, $crudAction)
                    ->set(EA::CRUD_CONTROLLER_FQCN, self::class)
                    ->set(EA::ENTITY_ID, $school->getId())
                    ->set(self::YEAR_ID, $schoolYear->getId())
                    ->set(EA::SUBMENU_INDEX, 2)
                    ->generateUrl(),
                'year' => $schoolYear,
            ];
        }

        return $links;
    }

    /**
     * @Route("/admin/school/{id}/{year}/fetch-educations", name="admin_fetch_educations", methods={"POST"})
     */
    public function fetchEducations($id, $year): Response
    {
        if (!$id) {
            return new JsonResponse(['error' => 'Geen school gevonden'], Response::HTTP_BAD_REQUEST);
        }

        $schoolYear = $this->em->getRepository(SchoolYear::class)->findOneBy(['id' => $year]);

        $school = $this->em->getRepository(School::class)->findOneBy(['id' => $id]);

        if (!$school instanceof School) {
            return new JsonResponse(['error' => 'School niet gevonden'], Response::HTTP_BAD_REQUEST);
        }

        $importedData = $this->educationsAPIService->getEducationsFromAPI($school);

        if (!$importedData) {
            return new JsonResponse(['error' => 'Kan onderwijsgegevens niet ophalen'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $educationsService = $this->get(EducationsService::class);
        $educationsService->saveEducations($school, $schoolYear, $importedData);

        return new JsonResponse(['success' => 'Onderwijsgegevens succesvol opgehaald']);
    }

    /**
     * @Route("/admin/school/{instellingsnummer}/{vestigingsnummer}/fetch-school-data", name="admin_fetch_school_data", methods={"GET"})
     */
    public function fetchSchoolData($instellingsnummer, $vestigingsnummer): Response
    {
        if (!$instellingsnummer || !$vestigingsnummer) {
            return new JsonResponse(['error' => 'Geen school gevonden'], Response::HTTP_BAD_REQUEST);
        }
        $schoolData = $this->schoolDataAPIService->fetchSchoolData($instellingsnummer, $vestigingsnummer);
        return new JsonResponse($schoolData, Response::HTTP_OK);
    }

    /**
     * @Route("/admin/school/bulk-import", name="admin_school_bulk_import")
     */
    public function bulkImport(AdminContext $context): Response
    {
        $form = $this->createForm(SchoolBulkImportType::class);
        $form->handleRequest($context->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $postcode = $form->get('postcode')->getData();
            $onderwijsvormen = $form->get('onderwijsvormen')->getData();
            $gemeente = $form->get('gemeente')->getData();
            $schooljaren = $form->get('schooljaren')->getData();
            $importedSchools = $this->schoolDataAPIService->bulkImportSchoolData($postcode, $onderwijsvormen);

            if ($importedSchools === null) {
                $this->addFlash('danger', 'Er is een fout opgetreden bij het importeren van de scholen.');
            } elseif (\count($importedSchools) === 0) {
                $this->addFlash('warning', 'Geen scholen gevonden voor de opgegeven postcode');
            }


        }

        return $this->render('Admin/School/bulk_import.html.twig', [
            'form' => $form->createView(),
            'importedSchools' => $importedSchools ?? null,
            'gemeente' => $gemeente ?? null,
            'schooljaren' => $schooljaren ?? null,
        ]);
    }


    /**
     * @Route("/admin/school/bulk-import/confirm", name="admin_school_bulk_import_confirm", methods={"POST"})
     */
    public function bulkImportConfirm(Request $request): Response
    {
        $selectedSchools = $request->request->get('selectedSchools', []);
        $gemeente = $request->request->get('gemeente', null);
        $schooljaren = explode(',', $request->request->get('schooljaren', ''));
        if (empty($selectedSchools)) {
            $this->addFlash('warning', 'Geen scholen geselecteerd voor import.');
            return $this->redirectToRoute('admin_school_bulk_import');
        }

        foreach ($selectedSchools as $schoolId) {
                [$instellingNummer, $vestigingsNummer] = explode('-', $schoolId);

                $existingSchool = $this->getDoctrine()->getRepository(School::class)
                    ->findOneByInstitutionAndEstablishment($instellingNummer, $vestigingsNummer);
                if ($existingSchool) {
                    unset($selectedSchools[$schoolId]);
                    continue;
                }
                $schoolData = $this->schoolDataAPIService->fetchSchoolData($instellingNummer, $vestigingsNummer);
                $school = new School();
                $school->setInstitutionNumber($vestigingsNummer);
                $school->setEstablishmentNumbers([$instellingNummer]);
                $school->setName($schoolData['name']);
                $school->setAddress($schoolData['address']);
                $school->setPostalCode($schoolData['postal_code']);
                $school->setRegion($schoolData['region']);
                $school->setCity($gemeente);
                $school->setPhoneNumber($schoolData['phone']);
                $school->setEmail($schoolData['email']);
                $school->setWebsite($schoolData['website']);
                $school->setType($schoolData['level']);
                $school->setLevel($schoolData['education_type']);
                $this->em->persist($school);

                if ($school->getType() === School::TYPE_SECONDARY_EDUCATION) {
                    foreach ($schooljaren as $schooljaar) {
                        $year = $this->em->getRepository(SchoolYear::class)->findOneBy(['id' => $schooljaar]);
                        if ($year instanceof SchoolYear) {
                            $importedData = $this->educationsAPIService->getEducationsFromAPI($school);
                            $educationsService = $this->get(EducationsService::class);
                            $educationsService->saveEducations($school, $year, $importedData);
                        }
                    }

                }
        }
        $this->em->flush();

        $this->addFlash('success', 'De geselecteerde scholen zijn succesvol geïmporteerd.');
        $url = $this->get(AdminUrlGenerator::class)
            ->setController(self::class)
            ->setAction('bulkImport')
            ->generateUrl();

        return $this->redirect($url);

    }
}
