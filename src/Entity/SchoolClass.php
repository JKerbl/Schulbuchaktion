<?php

namespace App\Entity;

use App\Repository\SchoolClassRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolClassRepository::class)]
class SchoolClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $grade = null;

    #[ORM\Column]
    private ?int $studentsAmount = null;

    #[ORM\Column(nullable: true)]
    private ?int $repAmount = null;

    #[ORM\Column]
    private ?float $usedBudget = null;

    #[ORM\Column]
    private ?float $budget = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: "schoolclass")]
    private ?Department $department = null;

    #[ORM\OneToMany(targetEntity: BookOrder::class, mappedBy: "schoolclass")]
    private Collection $bookOrder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getGrade(): ?int
    {
        return $this->grade;
    }

    public function setGrade(?int $grade): void
    {
        $this->grade = $grade;
    }

    public function getStudentsAmount(): ?int
    {
        return $this->studentsAmount;
    }

    public function setStudentsAmount(?int $studentsAmount): void
    {
        $this->studentsAmount = $studentsAmount;
    }

    public function getRepAmount(): ?int
    {
        return $this->repAmount;
    }

    public function setRepAmount(?int $repAmount): void
    {
        $this->repAmount = $repAmount;
    }

    public function getUsedBudget(): ?float
    {
        return $this->usedBudget;
    }

    public function setUsedBudget(?float $usedBudget): void
    {
        $this->usedBudget = $usedBudget;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): void
    {
        $this->budget = $budget;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): void
    {
        $this->department = $department;
    }

    public function getBookOrder(): Collection
    {
        return $this->bookOrder;
    }

    public function setBookOrder(Collection $bookOrder): void
    {
        $this->bookOrder = $bookOrder;
    }

    public function __toString(): string
    {
        return $this->name;
    }


}
