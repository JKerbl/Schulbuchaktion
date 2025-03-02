<?php

namespace App\Entity;

use App\Repository\BookOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookOrderRepository::class)]
class BookOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?String $orderFor = null;

    #[ORM\Column(nullable: true)]
    private ?bool $teacherCopy = null;

    #[ORM\Column(nullable: true)]
    private ?bool $eBook = null;

    #[ORM\Column(nullable: true)]
    private ?bool $eBookPlus = null;

    #[ORM\ManyToOne(targetEntity: SchoolClass::class, inversedBy: "bookOrder")]
    private ?SchoolClass $schoolclass = null;

    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: "bookOrder")]
    private ?Book $book = null;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'bookOrders')]
    #[ORM\JoinColumn(name: "subject_id", referencedColumnName: "id", nullable: false)]
    private ?Subject $subject = null;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getOrderFor(): ?string
    {
        return $this->orderFor;
    }

    public function setOrderFor(?string $orderFor): void
    {
        $this->orderFor = $orderFor;
    }

    public function getTeacherCopy(): ?bool
    {
        return $this->teacherCopy;
    }

    public function setTeacherCopy(?bool $teacherCopy): void
    {
        $this->teacherCopy = $teacherCopy;
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

    public function getSchoolclass(): ?SchoolClass
    {
        return $this->schoolclass;
    }

    public function setSchoolclass(?SchoolClass $schoolclass): void
    {
        $this->schoolclass = $schoolclass;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): void
    {
        $this->book = $book;
    }

    public function isTeacherCopy(): ?bool
    {
        return $this->teacherCopy;
    }

    public function isEBook(): ?bool
    {
        return $this->eBook;
    }

    public function isEBookPlus(): ?bool
    {
        return $this->eBookPlus;
    }
  
   public function __toString(): string
    {
        return $this->book->getTitle();
    }

   public function getSubject(): Subject
   {
       return $this->subject;
   }

   public function setSubject(?subject $subject): static
   {
       $this->subject = $subject;

       return $this;
   }
}
