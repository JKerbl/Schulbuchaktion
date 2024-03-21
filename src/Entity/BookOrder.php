<?php

namespace App\Entity;

use App\Repository\BookOrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookOrderRepository::class)]
class BookOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $count = null;

    #[ORM\Column(nullable: true)]
    private ?bool $teacherCopy = null;

    #[ORM\Column(nullable: true)]
    private ?bool $eBook = null;

    #[ORM\Column(nullable: true)]
    private ?bool $eBookPlus = null;

    #[ORM\Column]
    private ?int $schoolclassId = null;

    #[ORM\Column]
    private ?int $bookId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function isTeacherCopy(): ?bool
    {
        return $this->teacherCopy;
    }

    public function setTeacherCopy(?bool $teacherCopy): static
    {
        $this->teacherCopy = $teacherCopy;

        return $this;
    }

    public function isEBook(): ?bool
    {
        return $this->eBook;
    }

    public function setEBook(?bool $eBook): static
    {
        $this->eBook = $eBook;

        return $this;
    }

    public function isEBookPlus(): ?bool
    {
        return $this->eBookPlus;
    }

    public function setEBookPlus(?bool $eBookPlus): static
    {
        $this->eBookPlus = $eBookPlus;

        return $this;
    }

    public function getSchoolclassId(): ?int
    {
        return $this->schoolclassId;
    }

    public function setSchoolclassId(int $schoolclassId): static
    {
        $this->schoolclassId = $schoolclassId;

        return $this;
    }

    public function getBookId(): ?int
    {
        return $this->bookId;
    }

    public function setBookId(int $bookId): static
    {
        $this->bookId = $bookId;

        return $this;
    }
}
