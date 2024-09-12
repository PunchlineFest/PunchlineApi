<?php

namespace App\Controller\Admin;

use App\Entity\Artist;
use App\Entity\Comment;
use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
         return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('PunchlineFest Api');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),
            MenuItem::section('Festival'),
            MenuItem::linkToCrud('Evènements', 'fas fa-calendar', Event::class),
            MenuItem::linkToCrud('Avis', 'fas fa-comment', Comment::class),
            MenuItem::section('Inscription'),
            MenuItem::linkToCrud('Artistes', 'fa fa-music', Artist::class)
        ];
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/admin.css');
    }
}
