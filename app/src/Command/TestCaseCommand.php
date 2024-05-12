<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Page;
use App\Repository\BookRepository;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:test:case')]
class TestCaseCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BookRepository $bookRepository,
        private readonly PageRepository $pageRepository,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->bookRepository->findAll() as $book) {
            foreach ($book->getPages() as $page) {
                // dummy loop just to initialize all Book::$pages collections
            }
        }

        // Remove all entries with odd IDs
        foreach ($this->pageRepository->findAll() as $page) {
            if ($page->getId() % 2) {
                $this->entityManager->remove($page);
            }
        }

        /**
         * In doctrine/orm v2.19.5 for above remove() to have effect we need to either:
         * - place a flush below remove()
         * - or a persist(newPage), not book
         * - or remove persist(book)
         */
        foreach ($this->bookRepository->findAll() as $book) {
            $book->setName('Book from Command');

            // The loop here is not required since the same bug would occur with just ONE new entity,
            // here we're just creating more of them to showcase a possible real world example
            for ($i = 0; $i < 5; $i++) {
                $newPage = new Page();
                $newPage->setTitle('New Page from Command');
                $book->addPage($newPage);
            }
            $this->entityManager->persist($book);
        }

        $this->entityManager->flush();

        // Retrieve and output the results
        $pages = $this->pageRepository->findAll();
        $this->io->table(
            ['ID', 'Book ID', 'Title'],
            array_map(function($page) {
                return [
                    'id' => $page->getId(),
                    'bookId' => $page->getBook()->getId(),
                    'title' => $page->getTitle()
                ];
            }, $pages)
        );

        return Command::SUCCESS;
    }
}
