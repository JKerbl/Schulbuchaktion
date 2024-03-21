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

    #[ORM\Column]
    private ?int $bookId = null;

    #[ORM\Column]
    private ?float $price = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int|null $year
     */
    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return float|null
     */
    public function getPriceInclusiveEbook(): ?float
    {
        return $this->priceInclusiveEbook;
    }

    /**
     * @param float|null $priceInclusiveEbook
     */
    public function setPriceInclusiveEbook(?float $priceInclusiveEbook): void
    {
        $this->priceInclusiveEbook = $priceInclusiveEbook;
    }

    /**
     * @return float|null
     */
    public function getPriceEbook(): ?float
    {
        return $this->priceEbook;
    }

    /**
     * @param float|null $priceEbook
     */
    public function setPriceEbook(?float $priceEbook): void
    {
        $this->priceEbook = $priceEbook;
    }

    /**
     * @return float|null
     */
    public function getEbookPlusPrice(): ?float
    {
        return $this->ebookPlusPrice;
    }

    /**
     * @param float|null $ebookPlusPrice
     */
    public function setEbookPlusPrice(?float $ebookPlusPrice): void
    {
        $this->ebookPlusPrice = $ebookPlusPrice;
    }

    /**
     * @return int|null
     */
    public function getBookId(): ?int
    {
        return $this->bookId;
    }

    /**
     * @param int|null $bookId
     */
    public function setBookId(?int $bookId): void
    {
        $this->bookId = $bookId;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }


}
