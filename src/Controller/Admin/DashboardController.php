<?php

namespace App\Controller\Admin;

use App\Entity\Publication;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ) {

    }
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN', statusCode: 404)]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(PublicationCrudController::class)
            ->setController(UserCrudController::class)
            ->generateUrl();

         return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CRMC');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Carnet d\'adresses');
        yield MenuItem::section('Users');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
              MenuItem::linkToCrud('Add user', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
              MenuItem::linkToCrud('Show users', 'fas fa-plus', User::class)
        ]);
        yield MenuItem::section('Publications');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
              MenuItem::linkToCrud('Add publication', 'fas fa-plus', Publication::class)->setAction(Crud::PAGE_NEW),
              MenuItem::linkToCrud('Show publications', 'fas fa-eye', Publication::class)
        ]);

        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
