<?php

namespace App\Entity;

use App\Repository\BookPriceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookPriceRepository::class)]
class BookPrice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column]
    private ?float $priceInclusiveEbook = null;

    #[ORM\Column(nullable: true)]
    private ?float $priceEbook = null;

    #[ORM\Column(nullable: true)]
    private ?float $ebookPlusPrice = null;

    #[ORM\OneToOne(targetEntity: Book::class)]
    private Book $book;

    #[ORM\Column]
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    public function getPriceInclusiveEbook(): ?float
    {
        return $this->priceInclusiveEbook;
    }

    public function setPriceInclusiveEbook(?float $priceInclusiveEbook): void
    {
        $this->priceInclusiveEbook = $priceInclusiveEbook;
    }

    public function getPriceEbook(): ?float
    {
        return $this->priceEbook;
    }

    public function setPriceEbook(?float $priceEbook): void
    {
        $this->priceEbook = $priceEbook;
    }

    public function getEbookPlusPrice(): ?float
    {
        return $this->ebookPlusPrice;
    }

    public function setEbookPlusPrice(?float $ebookPlusPrice): void
    {
        $this->ebookPlusPrice = $ebookPlusPrice;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }


    public function setBook(?Book $book): void
    {
        $this->book = $book;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }




}
