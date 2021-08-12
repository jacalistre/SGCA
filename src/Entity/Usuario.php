<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function PHPSTORM_META\type;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UsuarioRepository")
 * @UniqueEntity("usuario"){
 *  message="Ya esta en uso ese nombre de usuario"
 * )
 */
class Usuario implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *      pattern     = "/^[a-z]+$/i",
     *     htmlPattern = "^[a-zA-Z]+$",
     *   message="Ya esta en uso ese nombre de usuario, no debe contener numeros"
     * )
     */
    private $usuario;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    private $pass;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/^(([A-ZÑÁÉÍÓÚ]{1}[a-zñáéíóú]+|\s)+[a-z]{1,3})+$/",
     *     message="Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía",
     *     message="No puede contener numero"
     * )
     */
    private $nombre;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/^(([A-ZÑÁÉÍÓÚ]{1}[a-zñáéíóú]+|\s)+[a-z]{1,3})+$/",
     *     message="Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía",
     * )
     */
    private $apellidos;
    /**
     * @ORM\Column(type="array")
     *
     */
    private $roles;
    private $auth;

    /**
     * @ORM\OneToMany(targetEntity=Ingreso::class, mappedBy="usuario")
     */
    private $ingresos;

    /**
     * @ORM\ManyToOne(targetEntity=Centro::class, inversedBy="usuarios")
     */
    private $centro;

    /**
     * @ORM\ManyToOne(targetEntity=AreaSalud::class, inversedBy="usuarios")
     */
    private $area;

    /**
     * @ORM\ManyToOne(targetEntity=Provincia::class, inversedBy="usuarios",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $provincia;

    /**
     * @ORM\ManyToOne(targetEntity=Municipio::class, inversedBy="usuarios",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $municipio;

    /**
     * @ORM\OneToMany(targetEntity=Notificacion::class, mappedBy="origen", orphanRemoval=true)
     */
    private $notificacions;

    public function __construct()
    {
        $this->ingresos = new ArrayCollection();
        $this->notificacions = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(?string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function setPass(?string $pass): self
    {
        $this->pass = md5($pass);

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[] The user roles
     */
    public function getRoles()
    {   if(isset($this->auth) && $this->auth==1) return $this->roles;
        if(isset($this->roles)|| $this->roles!=null) {
            return $this->roles[0];
        }

    return $this->roles ;
    }

    /**
     * @param mixed $auth
     */
    public function setAuth($auth): void
    {
        $this->auth = $auth;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword()
    {
        return $this->pass;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->usuario;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }



    public function setRoles( $roles): self
    {
        if(is_array($roles)){
        $this->roles=$roles;
    }else {
        $this->roles = [$roles];
    }
        return $this;
    }

    public  function __toString()
    {
        return $this->nombre." ".$this->apellidos;
    }

    /**
     * @return Collection|Ingreso[]
     */
    public function getIngresos(): Collection
    {
        return $this->ingresos;
    }

    public function addIngreso(Ingreso $ingreso): self
    {
        if (!$this->ingresos->contains($ingreso)) {
            $this->ingresos[] = $ingreso;
            $ingreso->setUsuario($this);
        }

        return $this;
    }

    public function removeIngreso(Ingreso $ingreso): self
    {
        if ($this->ingresos->removeElement($ingreso)) {
            // set the owning side to null (unless already changed)
            if ($ingreso->getUsuario() === $this) {
                $ingreso->setUsuario(null);
            }
        }

        return $this;
    }

    public function getCentro(): ?Centro
    {
        return $this->centro;
    }

    public function setCentro(?Centro $centro): self
    {
        $this->centro = $centro;

        return $this;
    }

    public function getArea(): ?AreaSalud
    {
        return $this->area;
    }

    public function setArea(?AreaSalud $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getMunicipio(): ?Municipio
    {
        return $this->municipio;
    }

    public function setMunicipio(?Municipio $municipio): self
    {
        $this->municipio = $municipio;

        return $this;
    }

    /**
     * @return Collection|Notificacion[]
     */
    public function getNotificacions(): Collection
    {
        return $this->notificacions;
    }

    public function addNotificacion(Notificacion $notificacion): self
    {
        if (!$this->notificacions->contains($notificacion)) {
            $this->notificacions[] = $notificacion;
            $notificacion->setOrigen($this);
        }

        return $this;
    }

    public function removeNotificacion(Notificacion $notificacion): self
    {
        if ($this->notificacions->removeElement($notificacion)) {
            // set the owning side to null (unless already changed)
            if ($notificacion->getOrigen() === $this) {
                $notificacion->setOrigen(null);
            }
        }

        return $this;
    }

    public function getDataforMessage(){
        if($this->getRoles()=="ROLE_AREA"){
            return "Area de Salud:".$this->getArea();
        }

        if($this->getRoles()=="ROLE_CENTRO"){
            return "Centro Asistencial:".$this->getCentro();
        }
        if($this->getRoles()=="ROLE_COORDINADOR_MUNICIPAL"){
            return "Mesa Coordinadora Municipal ,".$this->getProvincia().",".$this->getMunicipio();
        }
        if($this->getRoles()=="ROLE_COORDINADOR_PROVINCIAL"){
            return "Mesa Coordinadora Provincial ,".$this->getProvincia();
        }
        return $this->getUsuario()." ".$this->getRoles();
}
public function getDataRoles(){
    if($this->getRoles()=="ROLE_AREA"){
        return "Area de Salud";
    }

    if($this->getRoles()=="ROLE_CENTRO"){
        return "Centro Asistencial";
    }
    if($this->getRoles()=="ROLE_COORDINADOR_MUNICIPAL"){
        return "Mesa Coordinadora Municipal ";
    }
    if($this->getRoles()=="ROLE_COORDINADOR_PROVINCIAL"){
        return "Mesa Coordinadora Provincial ";
    }
    if($this->getRoles()=="ROLE_LABORATORIO"){
        return "Laboratorio";
    }
    if($this->getRoles()=="ROLE_ADMIN_MUN"){
        return "Administrador Municipal";
    }
    if($this->getRoles()=="ROLE_HOSPITAL"){
        return "Hospital";
    }
    return "Administrador de Sistema";
}


}