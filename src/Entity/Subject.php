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

    public function __construct()
    {
        $this->book = new ArrayCollection();
        $this->bookOrders = new ArrayCollection();
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


}
