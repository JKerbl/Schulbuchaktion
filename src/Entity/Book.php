<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $bnr = null;

    #[ORM\Column(length: 255)]
    private ?string $shortTitle = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?int $listtype = null;

    #[ORM\Column]
    private ?int $schoolForm = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info = null;

    #[ORM\Column]
    private ?bool $eBook = null;

    #[ORM\Column]
    private ?bool $eBookPlus = null;

    #[ORM\Column(nullable: true)]
    private ?int $mainBookId = null;

    #[ORM\Column]
    private ?int $bookPriceId = null;

    #[ORM\Column]
    private ?int $subjectId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBnr(): ?int
    {
        return $this->bnr;
    }

    public function setBnr(int $bnr): static
    {
        $this->bnr = $bnr;

        return $this;
    }

    public function getShortTitle(): ?string
    {
        return $this->shortTitle;
    }

    public function setShortTitle(string $shortTitle): static
    {
        $this->shortTitle = $shortTitle;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getListtype(): ?int
    {
        return $this->listtype;
    }

    public function setListtype(?int $listtype): static
    {
        $this->listtype = $listtype;

        return $this;
    }

    public function getSchoolForm(): ?int
    {
        return $this->schoolForm;
    }

    public function setSchoolForm(int $schoolForm): static
    {
        $this->schoolForm = $schoolForm;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): static
    {
        $this->info = $info;

        return $this;
    }

    public function isEBook(): ?bool
    {
        return $this->eBook;
    }

    public function setEBook(bool $eBook): static
    {
        $this->eBook = $eBook;

        return $this;
    }

    public function isEBookPlus(): ?bool
    {
        return $this->eBookPlus;
    }

    public function setEBookPlus(bool $eBookPlus): static
    {
        $this->eBookPlus = $eBookPlus;

        return $this;
    }

    public function getMainBookId(): ?int
    {
        return $this->mainBookId;
    }

    public function setMainBookId(?int $mainBookId): static
    {
        $this->mainBookId = $mainBookId;

        return $this;
    }

    public function getBookPrice(): ?int
    {
        return $this->bookPrice;
    }

    public function setBookPrice(int $bookPrice): static
    {
        $this->bookPrice = $bookPrice;

        return $this;
    }

    public function getSubjectId(): ?int
    {
        return $this->subjectId;
    }

    public function setSubjectId(int $subjectId): static
    {
        $this->subjectId = $subjectId;

        return $this;
    }

    public function getBookPriceId(): ?int
    {
        return $this->bookPriceId;
    }

    public function setBookPriceId(int $bookPriceId): static
    {
        $this->bookPriceId = $bookPriceId;

        return $this;
    }
}
