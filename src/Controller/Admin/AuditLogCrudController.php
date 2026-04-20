<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\AuditLog;
use App\Model\AuditLogFields;
use App\Model\Role;
use App\Model\User;
use App\Service\Filter\DateFilter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuditLogCrudController extends AbstractCrudController
{
    /**
     * @return array<string, string>
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class => '?' . TranslatorInterface::class,
        ]);
    }

    public static function getEntityFqcn(): string
    {
        return AuditLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setDefaultSort(['createdAt' => 'DESC']);
        $crud->setSearchFields(['school.name', 'schoolYear.startYear', 'schoolYear.endYear', 'name', 'email']);

        return parent::configureCrud($crud);
    }

    public function configureFilters(Filters $filters): Filters
    {
        $createdAtFilter = DateFilter::new('createdAt', 'app.admin.audit_logs.index.filter.created_at');
        $filters->add($createdAtFilter);

        $fieldFilter = ChoiceFilter::new('field', 'app.admin.audit_logs.index.filter.field');
        $fieldFilter->setChoices($this->getFieldChoices());
        $filters->add($fieldFilter);

        $filters->add('school');
        $filters->add('schoolYear');

        return $filters;
    }

    /**
     * @return array<string, string>
     */
    public function getFieldChoices(): array
    {
        $translator = $this->get(TranslatorInterface::class);
        if (!$translator instanceof TranslatorInterface) {
            return [];
        }

        $fieldChoices = [];
        $auditFields = AuditLogFields::$fields;
        foreach ($auditFields as $field) {
            $label = $translator->trans('app.admin.audit_logs.index.filter.fields.' . $field);
            $fieldChoices[$label] = $field;
        }

        foreach (AuditLogFields::$underrepresentedGroupFields as $field) {
            $label = $translator->trans('app.admin.audit_logs.index.filter.fields.' . AuditLogFields::LOG_PREFIX .$field);
            $fieldChoices[$label] = AuditLogFields::LOG_PREFIX . $field;
        }

        return $fieldChoices;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW, Action::DELETE, Action::EDIT);
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('app.admin.audit_logs.index.general.panel.general')->onlyOnDetail();
        yield TextField::new('userId', 'app.admin.audit_logs.index.general.user_id')->onlyOnDetail();
        yield EmailField::new('email', 'app.admin.audit_logs.index.general.user_email');

        yield TextField::new('name', 'app.admin.audit_logs.index.general.name');

        yield DateTimeField::new('createdAt', 'app.admin.audit_logs.index.general.created_at');

        yield FormField::addPanel('app.admin.audit_logs.index.general.panel.school')->onlyOnDetail();
        yield TextField::new('schoolName', 'app.admin.audit_logs.index.general.school_name');
        yield TextField::new('schoolYearName', 'app.admin.audit_logs.index.general.school_year');

        yield FormField::addPanel('app.admin.audit_logs.index.general.panel.changes')->onlyOnDetail();
        yield ChoiceField::new('field', 'app.admin.audit_logs.index.general.field')->setChoices($this->getFieldChoices());
        yield TextField::new('oldValue', 'app.admin.audit_logs.index.general.old_value')->setTemplatePath('Admin/AuditLog/text.html.twig');
        yield TextField::new('newValue', 'app.admin.audit_logs.index.general.new_value')->setTemplatePath('Admin/AuditLog/text.html.twig');
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
            $qb->andWhere('entity.school IN (:ids)')
                ->setParameter('ids', $user->getSchools());
        }

        return $qb;
    }
}
