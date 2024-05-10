<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Page;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private const NUMBER_OF_BOOKS = 3;
    private const NUMBER_OF_PAGES_PER_BOOK = 10;

    public static function getGroups(): array
    {
        return ['app'];
    }

    public function load(ObjectManager $manager): void
    {
        assert(empty($manager->getRepository(Book::class)->findAll()));
        $this->resetAutoIncrement($manager, Book::class);

        assert(empty($manager->getRepository(Page::class)->findAll()));
        $this->resetAutoIncrement($manager, Page::class);

        for ($i = 0; $i < self::NUMBER_OF_BOOKS; $i++) {
            $book = new Book();
            $book->setName('Book from Fixture');
            for ($j = 0; $j < self::NUMBER_OF_PAGES_PER_BOOK; $j++) {
                $page = new Page();
                $page->setTitle('Page from Fixture');
                $page->setBook($book);
                $manager->persist($page);
            }
            $manager->persist($book);
        }

        $manager->flush();
    }

    private function resetAutoIncrement(ObjectManager $objectManager, string $entityName): bool
    {
        $id = 0;
        $command = $objectManager->getClassMetadata($entityName);
        $connection = $objectManager->getConnection();
        $connection->executeStatement('ALTER TABLE ' . $command->getTableName() . ' AUTO_INCREMENT = ' . ++$id . ';');
        $connection->beginTransaction();

        return true;
    }
}
