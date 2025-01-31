<?php

namespace App\Entity;

use App\Repository\ParameterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

#[ORM\Entity(repositoryClass: ParameterRepository::class)]
class Parameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?int $limit4100 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?int $limit3100 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?int $limitRK4100 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?int $limitRK3100 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?int $year = null;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getLimit4100(): ?int
    {
        return $this->limit4100;
    }

    public function setLimit4100(?int $limit4100): void
    {
        $this->limit4100 = $limit4100;
    }

    public function getLimit3100(): ?int
    {
        return $this->limit3100;
    }

    public function setLimit3100(?int $limit3100): void
    {
        $this->limit3100 = $limit3100;
    }

    public function getLimitRK4100(): ?int
    {
        return $this->limitRK4100;
    }

    public function setLimitRK4100(?int $limitRK4100): void
    {
        $this->limitRK4100 = $limitRK4100;
    }

    public function getLimitRK3100(): ?int
    {
        return $this->limitRK3100;
    }

    public function setLimitRK3100(?int $limitRK3100): void
    {
        $this->limitRK3100 = $limitRK3100;
    }
}
