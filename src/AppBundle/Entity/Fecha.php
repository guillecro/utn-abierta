<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fecha")
 * @ORM\HasLifecycleCallbacks
 */
class Fecha
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="time")
     */
    private $hora;

    /**
     * @ORM\Column(type="datetime")
     */
    private $metafecha;

    /**
     * @ORM\Column(type="integer")
     */
    private $cupos;

     /**
     * @ORM\Column(type="integer")
     */
    private $cuposRestantes;

     /**
     * One Fecha has Many Reservas.
     * @ORM\OneToMany(targetEntity="Reserva", mappedBy="fecha")
     */
    private $reservas;
    /**
     * created Time/Date
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * updated Time/Date
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    // =====================================
    // SETTERS AND GETTERS FOR createdAt & updatedAt DATES

    /**
     * Set createdAt
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get createdAt
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get updatedAt
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    // =====================================


     public function __construct() {
        $this->reservas = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set hora
     *
     * @param \DateTime $hora
     *
     * @return Fecha
     */
    public function setHora($hora)
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * Get hora
     *
     * @return \DateTime
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Set metafecha
     *
     * @param \DateTime $metafecha
     *
     * @return Fecha
     */
    public function setMetafecha($metafecha)
    {
        $this->metafecha = $metafecha;

        return $this;
    }

    /**
     * Get metafecha
     *
     * @return \DateTime
     */
    public function getMetafecha()
    {
        return $this->metafecha;
    }

    /**
     * Set cupos
     *
     * @param integer $cupos
     *
     * @return Fecha
     */
    public function setCupos($cupos)
    {
        $this->cupos = $cupos;

        return $this;
    }

    /**
     * Get cupos
     *
     * @return integer
     */
    public function getCupos()
    {
        return $this->cupos;
    }

    /**
     * Set cuposRestantes
     *
     * @param integer $cuposRestantes
     *
     * @return Fecha
     */
    public function setCuposRestantes($cuposRestantes)
    {
        $this->cuposRestantes = $cuposRestantes;

        return $this;
    }

    /**
     * Get cuposRestantes
     *
     * @return integer
     */
    public function getCuposRestantes()
    {
        return $this->cuposRestantes;
    }

    /**
     * Add reserva
     *
     * @param \AppBundle\Entity\Reserva $reserva
     *
     * @return Fecha
     */
    public function addReserva(\AppBundle\Entity\Reserva $reserva)
    {
        $this->reservas[] = $reserva;

        return $this;
    }

    /**
     * Remove reserva
     *
     * @param \AppBundle\Entity\Reserva $reserva
     */
    public function removeReserva(\AppBundle\Entity\Reserva $reserva)
    {
        $this->reservas->removeElement($reserva);
    }

    /**
     * Get reservas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReservas()
    {
        return $this->reservas;
    }
}
