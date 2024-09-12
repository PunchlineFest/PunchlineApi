<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Général'),
            FormField::addColumn(6),
            TextField::new('name', 'Titre'),
            TextareaField::new('description', 'Description'),
            TextField::new('type', 'Type d\'évènement'),
            FormField::addColumn(6),
            TextField::new('category', 'Genre'),
            AssociationField::new('artists', 'Intervenants'),
            DateTimeField::new('date', 'Date'),

            FormField::addTab('Localisation'),
            TextField::new('address', 'Adresse'),
            ArrayField::new('coordinates', 'Coordonnées')
        ];
    }

}
