<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ArtistFixtures extends Fixture implements FixtureGroupInterface
{
    protected \Faker\Generator $fakerFactory;
    public function __construct()
    {
        $this->fakerFactory = \Faker\Factory::create('fr_FR');
    }

    public static function getGroups(): array
    {
        return ['artist'];
    }

    public static function getArtistReference(string $key): string
    {
        return Artist::class . $key;
    }

    public function load(ObjectManager $manager): void
    {
        $i = 0;
        foreach ($this->getData() as $data) {
            $entity = $this->createArtist($data);
            $manager->persist($entity);
            $this->addReference(self::getArtistReference((string) $i), $entity);
            ++$i;
        }

        $manager->flush();
    }

    private function createArtist(array $data): Artist
    {
        $entity = new Artist();

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

        for ($i = 0; $i < 100; ++$i) {
            $data = [
                'email' => $faker->email,
                'name' => $faker->name,
                'description' => $faker->text,
                'category' => ''
            ];
            yield $data;
        }
    }
}