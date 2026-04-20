<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\AuditLog;
use App\Entity\School;
use App\Entity\SchoolYear;
use App\Model\Role;
use App\Model\User;
use App\Repository\SchoolYearRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(SchoolCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Naar School In Vlaanderen');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('app.admin.menu.schools', 'fas fa-list', School::class)->setPermission(Role::ROLE_SCHOOL_ADMIN);
        yield MenuItem::linkToCrud('app.admin.menu.school_years', 'fas fa-list', SchoolYear::class)->setPermission(Role::ROLE_SCHOOL_YEAR_ADMIN);
        yield MenuItem::linkToRoute('app.admin.menu.users', 'fas fa-list', 'user_index')->setPermission(Role::ROLE_USER_ADMIN);
        yield MenuItem::linkToRoute('app.admin.menu.exports', 'fas fa-list', 'export_index')->setPermission(Role::ROLE_SUPER_ADMIN);
        yield MenuItem::linkToCrud('app.admin.menu.logs', 'fas fa-list', AuditLog::class);
        yield MenuItem::linkToUrl('app.admin.menu.LOP', 'fas fa-list', "https://lop.vlaanderen/lopstart/ ") -> setLinkTarget('_blank');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        if ($user instanceof User) {
            return parent::configureUserMenu($user)
                ->setName($user->getEmail());
        }

        return parent::configureUserMenu($user);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            AdminContextProvider::class => '?' . AdminContextProvider::class,
            SchoolYearRepository::class => '?' . SchoolYearRepository::class,
            TranslatorInterface::class => '?' . TranslatorInterface::class,
        ]);
    }
}
