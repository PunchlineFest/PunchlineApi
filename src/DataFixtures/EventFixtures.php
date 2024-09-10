<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EventFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private \Faker\Generator $fakerFactory;

    public function __construct() {
        $this->fakerFactory = \Faker\Factory::create('fr_FR');
    }

    public function getDependencies(): array
    {
        return [
            ArtistFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['artist'];
    }

    public function load(ObjectManager $manager): void
    {
        $events = new ArrayCollection();

        foreach ($this->getFirstDayEvents() as $data) {
            $entity = $this->createEvent($data);
            $manager->persist($entity);
            $events->add($entity);
        }

        foreach ($this->getSecondDayEvents() as $data) {
            $entity = $this->createEvent($data);
            $manager->persist($entity);
            $events->add($entity);
        }

        foreach ($this->getThirdDayEvents() as $data) {
            $entity = $this->createEvent($data);
            $manager->persist($entity);
            $events->add($entity);
        }

        $nb_artistes = 18;
        $artiste_index = 0;
        foreach ($events as $index => $event) {
            $artistes_a_ajouter = ($index < $nb_artistes % count($events)) ? 4 : 3;

            for ($i = 0; $i < $artistes_a_ajouter && $artiste_index < $nb_artistes; $i++) {
                $event->addArtist($this->getReference(ArtistFixtures::getArtistReference((string) $artiste_index)));
                $artiste_index++;
            }
        }

        $manager->flush();
    }

    private function createEvent(array $data): Event
    {
        $entity = new Event();

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();

        foreach ($data as $key => $value) {
            if ($propertyAccessor->isWritable($entity, $key)) {
                $propertyAccessor->setValue($entity, $key, $value);
            }
        }

        return $entity;
    }

    private function getFirstDayEvents(): iterable
    {
        $faker = $this->fakerFactory;
        $date = $faker->dateTimeBetween('12-07-2024', '14-07-2024');

        yield [
            'name' => 'Scène principale',
            'date' => $date,
            'category' => 'rap',
            'type' => 'concert',
            'description' => $faker->paragraph,
            'address' => '',
            'coordinates' => [43.258028, 5.381417]
        ];
        yield [
            'name' => 'Atelier graffiti',
            'date' => $date,
            'category' => 'hip-hop',
            'type' => 'atelier',
            'description' => $faker->paragraph,
            'address' => '',
            'coordinates' => [43.259528, 5.380278]
        ];
    }

    private function getSecondDayEvents(): iterable
    {
        $faker = $this->fakerFactory;
        $date = $faker->dateTimeBetween('12-07-2024', '14-07-2024');

        yield [
            'name' => 'Buvette du Lac',
            'date' => $date,
            'category' => null,
            'type' => 'restaurant',
            'description' => $faker->paragraph,
            'address' => '',
            'coordinates' => [43.261111, 5.381999]
        ];
        yield [
            'name' => 'Atelier DJing',
            'date' => $faker->dateTimeBetween('12-07-2024', '14-07-2024'),
            'category' => 'rap',
            'type' => 'atelier',
            'description' => $faker->paragraph,
            'address' => '',
            'coordinates' => [43.259833, 5.380083]
        ];
    }

    private function getThirdDayEvents(): iterable
    {
        $faker = $this->fakerFactory;
        $date = $faker->dateTimeBetween('12-07-2024', '14-07-2024');

        yield [
            'name' => 'Atelier de Breakdance',
            'date' => $date,
            'category' => 'rap',
            'type' => 'atelier',
            'description' => $faker->paragraph,
            'address' => '',
            'coordinates' => [43.260028, 5.380611]
        ];
        yield [
            'name' => 'Conférence',
            'date' => $date,
            'category' => 'hip-hop',
            'type' => 'conférence',
            'description' => $faker->paragraph,
            'address' => '',
            'coordinates' => [43.260250, 5.380111]
        ];
    }
}
