<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ArtistFixtures extends Fixture implements FixtureGroupInterface
{
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
            $this->addReference(self::getArtistReference((string)$i), $entity);
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
        $i = 0;
        foreach ($this->getArtistDatas() as $key => $value) {
            $category = match ($i % 5) {
                0 => "hip-hop",
                3, 1 => "rap",
                2, 4 => "r'n'b"
            };
            $data = [
                'name' => $key,
                'description' => $value,
                'category' => $category
            ];
            yield $data;
            ++$i;
        }
    }

    private function getArtistDatas(): array
    {
        return [
            "Zola" => "Zola est un rappeur français originaire d'Évry, connu pour son style percutant et ses paroles authentiques.",
            "Leto" => "Leto est un rappeur parisien, ancien membre du groupe PSO Thug, il s'est fait remarquer pour son style trap.",
            "Dinos" => "Dinos, de son vrai nom Jules Jomby, est un rappeur de La Courneuve, connu pour ses textes profonds et poétiques.",
            "Koba LaD" => "Koba LaD est un rappeur français originaire d'Évry, connu pour ses flows rapides et ses paroles sombres.",
            "Uzi" => "Uzi est un rappeur issu de la nouvelle génération du rap français, il se distingue par ses punchlines et son flow unique.",
            "Josman" => "Josman est un rappeur et beatmaker français, connu pour ses productions atmosphériques et ses textes introspectifs.",
            "Laylow" => "Laylow est un artiste français de rap et de musique électronique, reconnu pour ses sons futuristes et sa créativité.",
            "Ziak" => "Ziak est un rappeur masqué français, connu pour ses paroles crues et son style drill distinctif.",
            "Gambi" => "Gambi est un jeune rappeur de Fontenay-sous-Bois, il s'est fait connaître pour ses morceaux festifs et ses refrains accrocheurs.",
            "Ninho" => "Ninho est l'un des rappeurs les plus prolifiques de sa génération, connu pour ses nombreux projets certifiés et son style polyvalent.",
            "SCH" => "SCH est un rappeur marseillais connu pour son esthétique visuelle unique et ses paroles souvent sombres et mélancoliques.",
            "Jul" => "Jul est l'un des rappeurs français les plus populaires, avec un style auto-tuné reconnaissable et une productivité impressionnante.",
            "Vald" => "Vald est un rappeur français connu pour son humour noir, ses paroles provocatrices et son approche décalée du rap.",
            "PNL" => "PNL est un duo de rappeurs français composé des frères Ademo et N.O.S, ils sont connus pour leurs textes introspectifs et leur style cloud rap.",
            "Heuss L’Enfoiré" => "Heuss L’Enfoiré est un rappeur français, célèbre pour son style décomplexé et ses punchlines humoristiques.",
            "Damso" => "Damso est un rappeur belge, connu pour ses textes profonds, souvent sombres, et sa capacité à aborder des sujets introspectifs.",
            "Soso Maness" => "Soso Maness est un rappeur marseillais qui mélange rap, musique populaire et humour dans ses morceaux.",
            "Nekfeu" => "Nekfeu est un rappeur et acteur français, membre des groupes 1995 et S-Crew, il est apprécié pour ses textes profonds et poétiques.",
            "Gazo" => "Gazo est un rappeur français pionnier du style drill en France, connu pour son énergie brute et ses sons percutants."
        ];

    }
}