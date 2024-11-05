<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "subject")]
    private ?User $headOfSubject = null;

    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'subject')]
    private Collection $book;

    #[ORM\OneToMany(targetEntity: BookOrder::class, mappedBy: 'subject')]
    private Collection $bookOrders;

    #[ORM\OneToMany(targetEntity: ImportSubjectMap::class, mappedBy: 'subject')]
    private Collection $importSubjectMaps;

    public function __construct()
    {
        $this->book = new ArrayCollection();
        $this->bookOrders = new ArrayCollection();
        $this->subjectMaps = new ArrayCollection();
        $this->importSubjectMaps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getHeadOfSubject(): ?User
    {
        return $this->headOfSubject;
    }

    public function setHeadOfSubject(?User $headOfSubject): void
    {
        $this->headOfSubject = $headOfSubject;
    }

    public function getBook(): Collection
    {
        return $this->book;
    }

    public function setBook(Collection $book): void
    {
        $this->book = $book;
    }

    public function addBook(Book $book): static
    {
        if (!$this->book->contains($book)) {
            $this->book->add($book);
            $book->setSubject($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->book->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getSubject() === $this) {
                $book->setSubject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BookOrder>
     */
    public function getBookOrders(): Collection
    {
        return $this->bookOrders;
    }

    public function addBookOrder(BookOrder $bookOrder): static
    {
        if (!$this->bookOrders->contains($bookOrder)) {
            $this->bookOrders->add($bookOrder);
            $bookOrder->setSubject($this);
        }

        return $this;
    }

    public function removeBookOrder(BookOrder $bookOrder): static
    {
        if ($this->bookOrders->removeElement($bookOrder)) {
            // set the owning side to null (unless already changed)
            if ($bookOrder->getSubject() === $this) {
                $bookOrder->setSubject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ImportSubjectMap>
     */
    public function getSubjectMaps(): Collection
    {
        return $this->subjectMaps;
    }

    public function addSubjectMap(ImportSubjectMap $subjectMap): static
    {
        if (!$this->subjectMaps->contains($subjectMap)) {
            $this->subjectMaps->add($subjectMap);
            $subjectMap->addSubject($this);
        }

        return $this;
    }

    public function removeSubjectMap(ImportSubjectMap $subjectMap): static
    {
        if ($this->subjectMaps->removeElement($subjectMap)) {
            $subjectMap->removeSubject($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ImportSubjectMap>
     */
    public function getImportSubjectMaps(): Collection
    {
        return $this->importSubjectMaps;
    }

    public function addImportSubjectMap(ImportSubjectMap $importSubjectMap): static
    {
        if (!$this->importSubjectMaps->contains($importSubjectMap)) {
            $this->importSubjectMaps->add($importSubjectMap);
            $importSubjectMap->setSubject($this);
        }

        return $this;
    }

    public function removeImportSubjectMap(ImportSubjectMap $importSubjectMap): static
    {
        if ($this->importSubjectMaps->removeElement($importSubjectMap)) {
            // set the owning side to null (unless already changed)
            if ($importSubjectMap->getSubject() === $this) {
                $importSubjectMap->setSubject(null);
            }
        }

        return $this;
    }


}
