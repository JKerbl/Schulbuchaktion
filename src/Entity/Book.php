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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info = null;

    #[ORM\Column]
    private ?bool $eBook = null;

    #[ORM\Column]
    private ?bool $eBookPlus = null;

    #[ORM\Column(nullable: true)]
    private ?int $mainBookId = null;

    #[ORM\OneToOne(targetEntity: BookPrice::class, inversedBy: "book")]
    private BookPrice $bookPrice;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: "book")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Subject $subject = null;

    #[ORM\OneToMany(targetEntity: BookOrder::class, mappedBy: "book")]
    private Collection $bookOrder;

    #[ORM\ManyToMany(targetEntity: SchoolGrades::class, mappedBy: "books")]
    private Collection $schoolGrades;

    public function __construct()
    {
        $this->schoolGrades = new ArrayCollection();
    }

    public function addSchoolGrade(SchoolGrades $schoolGrade): self
    {
        if (!$this->schoolGrades->contains($schoolGrade)) {
            $this->schoolGrades[] = $schoolGrade;
            $schoolGrade->addBook($this);
        }

        return $this;
    }

    public function removeSchoolGrade(SchoolGrades $schoolGrade): self
    {
        if ($this->schoolGrades->removeElement($schoolGrade)) {
            $schoolGrade->removeBook($this);
        }

        return $this;
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

    public function getMainBookId(): ?int
    {
        return $this->mainBookId;
    }

    public function setMainBookId(?int $mainBookId): void
    {
        $this->mainBookId = $mainBookId;
    }

    public function getBookPrice(): ?BookPrice
    {
        return $this->bookPrice;
    }

    public function setBookPrice(?BookPrice $bookPrice): void
    {
        $this->bookPrice = $bookPrice;
    }

    public function getSubjectId(): ?int
    {
        return $this->subjectId;
    }

    public function setSubjectId(?int $subjectId): void
    {
        $this->subjectId = $subjectId;
    }

    public function isEBook(): ?bool
    {
        return $this->eBook;
    }

    public function isEBookPlus(): ?bool
    {
        return $this->eBookPlus;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBookOrder(): Collection
    {
        return $this->bookOrder;
    }

    public function setBookOrder(Collection $bookOrder): void
    {
        $this->bookOrder = $bookOrder;
    }

    public function getSchoolGrades(): Collection
    {
        return $this->schoolGrades;
    }

    public function setSchoolGrades(Collection $schoolGrades): void
    {
        $this->schoolGrades = $schoolGrades;
    }


}
