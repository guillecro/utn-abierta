<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="reserva")
 * @ORM\HasLifecycleCallbacks
 */
class Reserva
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * One Institucion has One Reserva
    * @ORM\OneToOne(targetEntity="Institucion", inversedBy="reserva",  fetch="EAGER" )
    * @ORM\JoinColumn(name="institucion_id", referencedColumnName="id")
    */
    private $institucion;

     /**
     * Many Reserva have One Fecha.
     * @ORM\ManyToOne(targetEntity="Fecha", inversedBy="reservas",  fetch="EAGER")
     * @ORM\JoinColumn(name="fecha_id", referencedColumnName="id")
     */
    private $fecha;

    /**
     * @ORM\Column(type="integer")
     */
    private $cupo;

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

    /**
     * updated Time/Date
     * @var \DateTime
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

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
     * Set cupo
     *
     * @param integer $cupo
     *
     * @return Reserva
     */
    public function setCupo($cupo)
    {
        $this->cupo = $cupo;

        return $this;
    }

    /**
     * Get cupo
     *
     * @return integer
     */
    public function getCupo()
    {
        return $this->cupo;
    }

    /**
     * Set institucion
     *
     * @param \AppBundle\Entity\Institucion $institucion
     *
     * @return Reserva
     */
    public function setInstitucion(\AppBundle\Entity\Institucion $institucion = null)
    {
        $this->institucion = $institucion;

        return $this;
    }

    /**
     * Get institucion
     *
     * @return \AppBundle\Entity\Institucion
     */
    public function getInstitucion()
    {
        return $this->institucion;
    }

    /**
     * Set fecha
     *
     * @param \AppBundle\Entity\Fecha $fecha
     *
     * @return Reserva
     */
    public function setFecha(\AppBundle\Entity\Fecha $fecha = null)
    {
        if($fecha != null){
        $fecha->addReserva($this);
        }
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \AppBundle\Entity\Fecha
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return Reserva
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
