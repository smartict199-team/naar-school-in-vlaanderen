<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Exception\UserExistsException;
use App\Form\User\UserType;
use App\Model\Auth0Filter;
use App\Model\Role;
use App\Model\User;
use App\Service\Auth0\UserRepository;
use App\Service\Provider\UserProvider;
use App\Service\Transformer\UserTransformer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public const PAGINATION_PAGE = 'page';
    public const REQUEST_SEARCH = 'query';

    /**
     * @Route("/admin/users", name="user_index")
     */
    public function index(Request $request, UserProvider $userProvider, UserTransformer $transformer, AdminUrlGenerator $routeBuilder): Response
    {
        $query = $request->get(self::REQUEST_SEARCH);
        $filter = new Auth0Filter();

        if ($query) {
            $filter->setQuery($query);
        }

        $requestPage = (int) $request->get(self::PAGINATION_PAGE, 1);

        $filter->setPage($requestPage - 1);

        /** @var User $user */
        $user = $this->getUser();
        $filter->setUser($user);

        $pagerFanta = $userProvider->getUsersPagination($filter);
        $pagerFanta->setCurrentPage($requestPage);

        $currentPageResults = $pagerFanta->getCurrentPageResults();
        $crudActions = [];

        if ($this->isGranted(Role::ROLE_CREATE_USER)) {
            $createAction = [
                'label' => 'app.admin.user.create_new',
                'action' => $routeBuilder->setRoute('user_create')->generateUrl(),
            ];

            $crudActions[] = $createAction;
        }

        $userResponseData = [];
        $indexItems = $transformer->transformResponseArrayToModels((array) $currentPageResults);

        foreach ($indexItems as $indexItem) {
            $data = [];
            $data['item'] = $indexItem;

            if ($this->isGranted(Role::ROLE_DELETE_USER)) {
                $data['crudActions'] = ['app.admin.user.edit.button' => $routeBuilder->setRoute('user_edit')->setEntityId($indexItem->getId())->generateUrl()];
            }

            $userResponseData[] = $data;
        }

        return $this->render('Users/index.html.twig',
            [
                'pagerfanta' => $pagerFanta,
                'users' => $userResponseData,
                'crudActions' => $crudActions,
            ]
        );
    }

    /**
     * @Route("/admin/users/create", name="user_create")
     */
    public function create(
        Request $request,
        UserRepository $userRepository,
        UserTransformer $transformer,
        AdminUrlGenerator $adminUrlGenerator): Response
    {
        $this->denyAccessUnlessGranted(Role::ROLE_CREATE_USER);

        $newUser = new User('');

        $form = $this->createForm(UserType::class, $newUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newUser = $form->getData();

            $createUserRequest = $transformer->transformModelToCreateRequest($newUser);
            try {
                $userRepository->createUser($createUserRequest);
                $userRepository->resetPassword($createUserRequest->getEmail());
                $this->addFlash('success', 'app.admin.user.create.flash.user_created');

                return $this->redirect($adminUrlGenerator->setRoute('user_index')->generateUrl());
            } catch (UserExistsException $exception) {
                $this->addFlash('danger', 'app.admin.user.create.flash.user_exists');
            }
        }

        return $this->render('Users/create.html.twig', [
            'form' => $form->createView(),
            'type' => 'create',
        ]);
    }

    /**
     * @Route("/admin/users/edit", name="user_edit")
     */
    public function edit(Request $request, UserRepository $userRepository, AdminUrlGenerator $adminUrlGenerator, UserTransformer $transformer): Response
    {
        $this->denyAccessUnlessGranted(Role::ROLE_EDIT_USER);

        $entityId = $request->get(EA::ENTITY_ID);
        $user = $userRepository->findById($entityId, true);
        $user = $transformer->transformResponseToModel($user);
        $editedUserSchools = $user->getSchools()->toArray();
        $form = $this->createForm(UserType::class, $user);

        $loggedInUserSchools = [];
        $loggedInUser = $this->getUser();
        if ($loggedInUser instanceof User) {
            $loggedInUserSchools = $loggedInUser->getSchools()->toArray();
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $newUser */
            $newUser = $form->getData();
            $extraSchools = [];

            if (!$this->isGranted(Role::ROLE_SUPER_ADMIN)) {
                foreach ($editedUserSchools as $editedUserSchool) {
                    if (!\in_array($editedUserSchool, $loggedInUserSchools)) {
                        $extraSchools[] = $editedUserSchool;
                    }
                }
            }

            foreach ($extraSchools as $extraSchool) {
                $newUser->addSchool($extraSchool);
            }

            $updateUserRequest = $transformer->transformModelToUpdateRequest($newUser);

            $userRepository->updateUser($entityId, $updateUserRequest);

            $this->addFlash('success', 'app.admin.user.edit.flash.user_updated');

            return $this->redirect($adminUrlGenerator->setRoute('user_index')->generateUrl());
        }

        return $this->render('Users/create.html.twig', [
            'form' => $form->createView(),
            'type' => 'edit',
        ]);
    }

    /**
     * @Route("/admin/users/delete/{id}", name="user_delete")
     */
    public function delete(string $id, UserRepository $userRepository, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $this->denyAccessUnlessGranted(Role::ROLE_DELETE_USER);

        $userRepository->deleteUser($id);

        $this->addFlash('success', 'app.admin.user.delete.flash.user_deleted');

        return $this->redirect($adminUrlGenerator->setRoute('user_index')->generateUrl());
    }
}
