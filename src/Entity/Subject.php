<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $shortName = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: "headSubject")]
    private ?User $headOfSubject = null;

    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'subject')]
    private Collection $book;

    public function __construct()
    {
        $this->book = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): void
    {
        $this->shortName = $shortName;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getHeadOfSubject(): ?User
    {
        return $this->headOfSubject;
    }

    public function setHeadOfSubject(?User $headOfSubject): void
    {
        $this->headOfSubject = $headOfSubject;
    }

    public function getBook(): Collection
    {
        return $this->book;
    }

    public function setBook(Collection $book): void
    {
        $this->book = $book;
    }


}
