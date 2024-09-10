<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
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
        $i = 1;
        foreach ($this->getData() as $data) {

            $entity = $this->createEvent($data);
            $artist = $this->getReference(ArtistFixtures::getArtistReference((string) $i));
            $entity->addArtist($artist);
            $manager->persist($entity);
            ++$i;
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

    private function getData(): iterable
    {
        $faker = $this->fakerFactory;

        for ($i = 0; $i < 9; ++$i) {
            yield [
                'name' => $faker->word,
                'date' => $faker->dateTimeBetween('12-07-2024', '14-07-2024'),
                'category' => '',
                'type' => 'concert',
                'description' => $faker->paragraph,
                'address' => $faker->address,
                'coordinates' => [$faker->latitude, $faker->longitude]
            ];
        }

        for ($i = 10; $i < 18; ++$i) {
            yield [
                'name' => $faker->word,
                'date' => $faker->dateTimeBetween('12-07-2024', '14-07-2024'),
                'category' => '',
                'type' => 'conférence',
                'description' => $faker->paragraph,
                'address' => $faker->address,
                'coordinates' => [$faker->latitude, $faker->longitude]
            ];
        }
    }
}
