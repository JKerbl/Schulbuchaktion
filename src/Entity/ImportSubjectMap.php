<?php

namespace App\Entity;

use App\Repository\ImportSubjectMapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportSubjectMapRepository::class)]
class ImportSubjectMap
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'importSubjectMap')]
    private Collection $csvSubject;

    #[ORM\ManyToOne(inversedBy: 'importSubjectMaps')]
    private ?Subject $subject = null;


    public function __construct()
    {
        $this->csvSubject = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $subjectName): void
    {
        $this->name = $subjectName;
    }


    /**
     * @return Collection<int, Subject>
     */
    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getCsvSubject(): Collection
    {
        return $this->csvSubject;
    }

    public function addCsvSubject(Book $csvSubject): static
    {
        if (!$this->csvSubject->contains($csvSubject)) {
            $this->csvSubject->add($csvSubject);
            $csvSubject->setImportSubjectMap($this);
        }

        return $this;
    }

    public function removeCsvSubject(Book $csvSubject): static
    {
        if ($this->csvSubject->removeElement($csvSubject)) {
            // set the owning side to null (unless already changed)
            if ($csvSubject->getImportSubjectMap() === $this) {
                $csvSubject->setImportSubjectMap(null);
            }
        }

        return $this;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }
}
