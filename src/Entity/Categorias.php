<?php

namespace App\Entity;

use App\Repository\CategoriasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoriasRepository::class)
 */
class Categorias
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="integer")
     */
    private $numImagenes;

    /**
     * @ORM\OneToMany(targetEntity=Imagenes::class, mappedBy="categoria")
     */
    private $imagenes;

    public function __construct()
    {
        $this->imagenes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getNumImagenes(): ?int
    {
        return $this->numImagenes;
    }

    public function setNumImagenes(int $numImagenes): self
    {
        $this->numImagenes = $numImagenes;

        return $this;
    }

    /**
     * @return Collection|Imagenes[]
     */
    public function getImagenes(): Collection
    {
        return $this->imagenes;
    }

    public function addImagene(Imagenes $imagene): self
    {
        if (!$this->imagenes->contains($imagene)) {
            $this->imagenes[] = $imagene;
            $imagene->setCategoria($this);
        }

        return $this;
    }

    public function removeImagene(Imagenes $imagene): self
    {
        if ($this->imagenes->removeElement($imagene)) {
            // set the owning side to null (unless already changed)
            if ($imagene->getCategoria() === $this) {
                $imagene->setCategoria(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNombre();
    }
}