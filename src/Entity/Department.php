<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $budget = null;

    #[ORM\Column]
    private ?float $usedBudget = null;

    #[ORM\Column(nullable: true)]
    private ?int $umew = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "department")]
    private ?User $headOfDepartment = null;

    #[ORM\OneToMany(targetEntity: SchoolClass::class, mappedBy: "department")]
    private Collection $schoolclass;

    #[ORM\Column]
    private ?int $year = null;

    public function __construct()
    {
        $this->schoolclass = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): void
    {
        $this->budget = $budget;
    }

    public function getUsedBudget(): ?float
    {
        return $this->usedBudget;
    }

    public function setUsedBudget(?float $usedBudget): void
    {
        $this->usedBudget = $usedBudget;
    }

    public function getUmew(): ?int
    {
        return $this->umew;
    }

    public function setUmew(?int $umew): void
    {
        $this->umew = $umew;
    }

    public function getHeadOfDepartment(): ?User
    {
        return $this->headOfDepartment;
    }

    public function setHeadOfDepartment(?User $headOfDepartment): void
    {
        $this->headOfDepartment = $headOfDepartment;
    }

    public function getSchoolclass(): Collection
    {
        return $this->schoolclass;
    }

    public function setSchoolclass(Collection $schoolclass): void
    {
        $this->schoolclass = $schoolclass;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function addSchoolclass(SchoolClass $schoolclass): static
    {
        if (!$this->schoolclass->contains($schoolclass)) {
            $this->schoolclass->add($schoolclass);
            $schoolclass->setDepartment($this);
        }

        return $this;
    }

    public function removeSchoolclass(SchoolClass $schoolclass): static
    {
        if ($this->schoolclass->removeElement($schoolclass)) {
            // set the owning side to null (unless already changed)
            if ($schoolclass->getDepartment() === $this) {
                $schoolclass->setDepartment(null);
            }
        }

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
}
