<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Page::class, cascade: ['persist', 'remove'])]
    private Collection $pages;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Page[]
     */
    public function getPages(): array
    {
        return $this->pages->toArray();
    }

    public function addPage(Page $page): Book
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setBook($this);
        }

        return $this;
    }

    public function removePage(Page $page): Book
    {
        if ($this->pages->removeElement($page)) {
            // set the owning side to null (unless already changed)
            if ($page->getBook() === $this) {
                $page->setBook(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Book
    {
        $this->name = $name;

        return $this;
    }
}
