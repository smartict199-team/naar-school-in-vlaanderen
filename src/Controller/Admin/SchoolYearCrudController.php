<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\SchoolYear;
use App\Model\Role;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Contracts\Translation\TranslatorInterface;

class SchoolYearCrudController extends AbstractCrudController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class => '?' . TranslatorInterface::class,
            ]
        );
    }

    public static function getEntityFqcn(): string
    {
        return SchoolYear::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setPageTitle('index', 'app.admin.school_year.title');
        $crud->setPageTitle('new', 'app.admin.school_year.title');
        $crud->setPageTitle('detail', 'app.admin.school_year.title');
        $crud->setPageTitle('edit', 'app.admin.school_year.title');

        $translator = $this->get(TranslatorInterface::class);
        if ($translator instanceof TranslatorInterface) {
            $crud->setPageTitle('edit', function (SchoolYear $schoolYear) use ($translator) {
                return sprintf('%s %s', $translator->trans('app.admin.school_year.title'), (string) $schoolYear);
            });
        }

        $crud->setDefaultSort(['startYear' => 'ASC']);

        return parent::configureCrud($crud);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        $actions->setPermission(Action::NEW, Role::ROLE_ADD_SCHOOL_YEAR);
        $actions->remove('index', Action::DELETE);
        $actions->setPermission(Action::EDIT, Role::ROLE_EDIT_SCHOOL_YEAR);

        $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setLabel('app.admin.school_year.create');
        });

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('startYear', 'app.admin.school_year.index.start_year')->onlyWhenCreating();
        yield IntegerField::new('startYear', 'app.admin.school_year.index.start_year')->onlyOnIndex();
        yield IntegerField::new('endYear', 'app.admin.school_year.index.end_year')->onlyWhenCreating();
        yield IntegerField::new('endYear', 'app.admin.school_year.index.end_year')->onlyOnIndex();
        yield BooleanField::new('visibleFrontend', 'app.admin.school_year.index.visible_frontend');
        yield BooleanField::new('visibleBackend', 'app.admin.school_year.index.visible_backend');
    }
}
