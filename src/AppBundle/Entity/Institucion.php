<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="institucion")
 * @ORM\HasLifecycleCallbacks
 */
class Institucion
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * One Institucion has One Reserva.
     * @ORM\OneToOne(targetEntity="Reserva", mappedBy="institucion")
     */
    private $reserva;

    /**
     * @ORM\Column(type="string")
     */
    private $nombre;

    /**
     * @ORM\Column(type="string")
     */
    private $direccion;

    /**
     * @ORM\Column(type="string")
     */
    private $telefono;

    /**
     * @ORM\Column(type="string")
     */
    private $localidad;

    /**
     * @ORM\Column(type="string")
     */
    private $provincia;

    /**
     * @ORM\Column(type="string")
     * @Assert\Email(message="El email '{{ value }}' no es un mail valido"),
     * @Assert\NotBlank(message="El mail no puede ser vacio")
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     */
    private $terminalidad;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $infoExtra;

    /**
     * @ORM\Column(type="string")
     */
    private $docenteNombre;

    /**
     * @ORM\Column(type="string")
     */
    private $docenteEmail;

    /**
     * @ORM\Column(type="string")
     */
    private $docenteTelefono;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $optativoNombre;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $optativoEmail;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $optativoTelefono;

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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Institucion
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Institucion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Institucion
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set localidad
     *
     * @param string $localidad
     *
     * @return Institucion
     */
    public function setLocalidad($localidad)
    {
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Get localidad
     *
     * @return string
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * Set provincia
     *
     * @param string $provincia
     *
     * @return Institucion
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Get provincia
     *
     * @return string
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Institucion
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set terminalidad
     *
     * @param string $terminalidad
     *
     * @return Institucion
     */
    public function setTerminalidad($terminalidad)
    {
        $this->terminalidad = $terminalidad;

        return $this;
    }

    /**
     * Get terminalidad
     *
     * @return string
     */
    public function getTerminalidad()
    {
        return $this->terminalidad;
    }

    /**
     * Set infoExtra
     *
     * @param string $infoExtra
     *
     * @return Institucion
     */
    public function setInfoExtra($infoExtra)
    {
        $this->infoExtra = $infoExtra;

        return $this;
    }

    /**
     * Get infoExtra
     *
     * @return string
     */
    public function getInfoExtra()
    {
        return $this->infoExtra;
    }

    /**
     * Set docenteNombre
     *
     * @param string $docenteNombre
     *
     * @return Institucion
     */
    public function setDocenteNombre($docenteNombre)
    {
        $this->docenteNombre = $docenteNombre;

        return $this;
    }

    /**
     * Get docenteNombre
     *
     * @return string
     */
    public function getDocenteNombre()
    {
        return $this->docenteNombre;
    }

    /**
     * Set docenteEmail
     *
     * @param string $docenteEmail
     *
     * @return Institucion
     */
    public function setDocenteEmail($docenteEmail)
    {
        $this->docenteEmail = $docenteEmail;

        return $this;
    }

    /**
     * Get docenteEmail
     *
     * @return string
     */
    public function getDocenteEmail()
    {
        return $this->docenteEmail;
    }

    /**
     * Set docenteTelefono
     *
     * @param string $docenteTelefono
     *
     * @return Institucion
     */
    public function setDocenteTelefono($docenteTelefono)
    {
        $this->docenteTelefono = $docenteTelefono;

        return $this;
    }

    /**
     * Get docenteTelefono
     *
     * @return string
     */
    public function getDocenteTelefono()
    {
        return $this->docenteTelefono;
    }

    /**
     * Set optativoNombre
     *
     * @param string $optativoNombre
     *
     * @return Institucion
     */
    public function setOptativoNombre($optativoNombre)
    {
        $this->optativoNombre = $optativoNombre;

        return $this;
    }

    /**
     * Get optativoNombre
     *
     * @return string
     */
    public function getOptativoNombre()
    {
        return $this->optativoNombre;
    }

    /**
     * Set optativoEmail
     *
     * @param string $optativoEmail
     *
     * @return Institucion
     */
    public function setOptativoEmail($optativoEmail)
    {
        $this->optativoEmail = $optativoEmail;

        return $this;
    }

    /**
     * Get optativoEmail
     *
     * @return string
     */
    public function getOptativoEmail()
    {
        return $this->optativoEmail;
    }

    /**
     * Set optativoTelefono
     *
     * @param string $optativoTelefono
     *
     * @return Institucion
     */
    public function setOptativoTelefono($optativoTelefono)
    {
        $this->optativoTelefono = $optativoTelefono;

        return $this;
    }

    /**
     * Get optativoTelefono
     *
     * @return string
     */
    public function getOptativoTelefono()
    {
        return $this->optativoTelefono;
    }

    /**
     * Set reserva
     *
     * @param \AppBundle\Entity\Reserva $reserva
     *
     * @return Institucion
     */
    public function setReserva(\AppBundle\Entity\Reserva $reserva = null)
    {
        $reserva->setInstitucion($this);
        $this->reserva = $reserva;

        return $this;
    }

    /**
     * Get reserva
     *
     * @return \AppBundle\Entity\Reserva
     */
    public function getReserva()
    {
        return $this->reserva;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return Institucion
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
