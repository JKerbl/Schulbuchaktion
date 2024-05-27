<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
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

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $info = null;

    #[ORM\Column]
    private ?bool $eBook = null;

    #[ORM\Column]
    private ?bool $eBookPlus = null;

    #[ORM\Column]
    private string $schoolGrades;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: "book")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Subject $subject = null;

    #[ORM\OneToMany(targetEntity: BookOrder::class, mappedBy: "book")]
    private Collection $bookOrder;


    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    private ?float $priceBase = null;

    #[ORM\Column(nullable: true)]
    private ?float $eBookPlusPrice = null;

    #[ORM\Column]
    private ?bool $teacherVersion = null;

    #[ORM\Column]
    private ?int $year = null;

    public function __construct()
    {
        $this->schoolGrades = new ArrayCollection();
        $this->bookOrder = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getBnr(): ?int
    {
        return $this->bnr;
    }

    public function setBnr(?int $bnr): void
    {
        $this->bnr = $bnr;
    }

    public function getShortTitle(): ?string
    {
        return $this->shortTitle;
    }

    public function setShortTitle(?string $shortTitle): void
    {
        $this->shortTitle = $shortTitle;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getListtype(): ?int
    {
        return $this->listtype;
    }

    public function setListtype(?int $listtype): void
    {
        $this->listtype = $listtype;
    }

    public function getSchoolForm(): ?int
    {
        return $this->schoolForm;
    }

    public function setSchoolForm(?int $schoolForm): void
    {
        $this->schoolForm = $schoolForm;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): void
    {
        $this->info = $info;
    }

    public function getEBook(): ?bool
    {
        return $this->eBook;
    }

    public function setEBook(?bool $eBook): void
    {
        $this->eBook = $eBook;
    }

    public function getEBookPlus(): ?bool
    {
        return $this->eBookPlus;
    }

    public function setEBookPlus(?bool $eBookPlus): void
    {
        $this->eBookPlus = $eBookPlus;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): void
    {
        $this->subject = $subject;
    }

    public function getBookOrder(): Collection
    {
        return $this->bookOrder;
    }

    public function setBookOrder(Collection $bookOrder): void
    {
        $this->bookOrder = $bookOrder;
    }

    public function getSchoolGrades(): string
    {
        return $this->schoolGrades;
    }

    public function setSchoolGrades(string $schoolGrades): void
    {
        $this->schoolGrades = $schoolGrades;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getPriceBase(): ?float
    {
        return $this->priceBase;
    }

    public function setPriceBase(?float $priceBase): void
    {
        $this->priceBase = $priceBase;
    }

    public function getEBookPlusPrice(): ?float
    {
        return $this->eBookPlusPrice;
    }

    public function setEBookPlusPrice(?float $eBookPlusPrice): void
    {
        $this->eBookPlusPrice = $eBookPlusPrice;
    }

    public function isTeacherVersion(): ?bool
    {
        return $this->teacherVersion;
    }

    public function setTeacherVersion(bool $teacherVersion): static
    {
        $this->teacherVersion = $teacherVersion;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function isEBook(): ?bool
    {
        return $this->eBook;
    }

    public function isEBookPlus(): ?bool
    {
        return $this->eBookPlus;
    }

    public function addBookOrder(BookOrder $bookOrder): static
    {
        if (!$this->bookOrder->contains($bookOrder)) {
            $this->bookOrder->add($bookOrder);
            $bookOrder->setBook($this);
        }

        return $this;
    }

    public function removeBookOrder(BookOrder $bookOrder): static
    {
        if ($this->bookOrder->removeElement($bookOrder)) {
            // set the owning side to null (unless already changed)
            if ($bookOrder->getBook() === $this) {
                $bookOrder->setBook(null);
            }
        }

        return $this;
    }


}
