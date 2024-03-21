<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column]
    private ?int $headOfDepartmentId = null;

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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return float|null
     */
    public function getBudget(): ?float
    {
        return $this->budget;
    }

    /**
     * @param float|null $budget
     */
    public function setBudget(?float $budget): void
    {
        $this->budget = $budget;
    }

    /**
     * @return float|null
     */
    public function getUsedBudget(): ?float
    {
        return $this->usedBudget;
    }

    /**
     * @param float|null $usedBudget
     */
    public function setUsedBudget(?float $usedBudget): void
    {
        $this->usedBudget = $usedBudget;
    }

    /**
     * @return int|null
     */
    public function getUmew(): ?int
    {
        return $this->umew;
    }

    /**
     * @param int|null $umew
     */
    public function setUmew(?int $umew): void
    {
        $this->umew = $umew;
    }

    /**
     * @return int|null
     */
    public function getHeadOfDepartmentId(): ?int
    {
        return $this->headOfDepartmentId;
    }

    /**
     * @param int|null $headOfDepartmentId
     */
    public function setHeadOfDepartmentId(?int $headOfDepartmentId): void
    {
        $this->headOfDepartmentId = $headOfDepartmentId;
    }
}
