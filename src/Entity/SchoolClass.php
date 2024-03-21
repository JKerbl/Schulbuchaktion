<?php

namespace App\Entity;

use App\Repository\SchoolClassRepository;
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

    #[ORM\Column]
    private ?int $departmentId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getGrade(): ?int
    {
        return $this->grade;
    }

    public function setGrade(int $grade): static
    {
        $this->grade = $grade;

        return $this;
    }

    public function getStudentsAmount(): ?int
    {
        return $this->studentsAmount;
    }

    public function setStudentsAmount(int $studentsAmount): static
    {
        $this->studentsAmount = $studentsAmount;

        return $this;
    }

    public function getRepAmount(): ?int
    {
        return $this->repAmount;
    }

    public function setRepAmount(?int $repAmount): static
    {
        $this->repAmount = $repAmount;

        return $this;
    }

    public function getUsedBudget(): ?float
    {
        return $this->usedBudget;
    }

    public function setUsedBudget(float $usedBudget): static
    {
        $this->usedBudget = $usedBudget;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): static
    {
        $this->budget = $budget;

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

    public function getDepartmentId(): ?int
    {
        return $this->departmentId;
    }

    public function setDepartmentId(int $departmentId): static
    {
        $this->departmentId = $departmentId;

        return $this;
    }
}
