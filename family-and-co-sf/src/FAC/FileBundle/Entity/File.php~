<?php

namespace FAC\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Filesystem\Filesystem;

/**
 * File
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="FAC\FileBundle\Entity\FileRepository")
 */
class File
{
   /**
     * @ORM\ManyToOne(targetEntity="FAC\UserBundle\Entity\User", inversedBy="files")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    public function __construct($user){
        $this->user = $user;
        $this->parentId = $user->getSelectedFolder();
        $generator = new SecureRandom();
        $random = bin2hex($generator->nextBytes(4));
        $this->uniqueKey = $random;
    }

    /**
     * @Assert\File(maxSize="6000000000")
     */
    public $file;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="unique_key", type="string", length=255)
     * 
     */
    private $uniqueKey;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    private $level;

    /**
     * @var integer
     *
     * @ORM\Column(name="root_id", type="integer", nullable=true)
     */
    private $rootId;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="time", nullable=true)
     */
    private $time;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

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
     * Set level
     *
     * @param integer $level
     * @return File
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set rootId
     *
     * @param integer $rootId
     * @return File
     */
    public function setRootId($rootId)
    {
        $this->rootId = $rootId;

        return $this;
    }

    /**
     * Get rootId
     *
     * @return integer 
     */
    public function getRootId()
    {
        return $this->rootId;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     * @return File
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer 
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return File
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir();
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return null === $this->path ? '/uploads/'.$this->user->getUsername() : $this->path;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // faites ce que vous voulez pour générer un nom unique
            $this->path = $this->getUploadDir();
            $this->size = $this->file->getClientSize();
            $this->name = $this->file->getClientOriginalName();
            $this->type = $this->file->getMimeType(); 
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // s'il y a une erreur lors du déplacement du fichier, une exception
        // va automatiquement être lancée par la méthode move(). Cela va empêcher
        // proprement l'entité d'être persistée dans la base de données si
        // erreur il y a
        $this->file->move($this->getUploadRootDir(), $this->name);
        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            $fs = new Filesystem();
            try {
                $fs->remove($file);
            } catch (IOExceptionInterface $e) {
                echo "An error occurred while deleting at ".$e->getPath();
            }
            
            //unlink($file);
        }
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return File
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return File
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }
    

    /**
     * Set password
     *
     * @param string $password
     * @return File
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set users
     *
     * @param \FAC\UserBundle\Entity\User $user
     * @return File
     */
    public function setUser(\FAC\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get users
     *
     * @return \FAC\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return File
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set uniqueKey
     *
     * @param string $uniqueKey
     * @return File
     */
    public function setUniqueKey($uniqueKey)
    {
        $this->uniqueKey = $uniqueKey;

        return $this;
    }

    /**
     * Get uniqueKey
     *
     * @return string 
     */
    public function getUniqueKey()
    {
        return $this->uniqueKey;
    }

     /**
     * @ORM\ManyToMany(targetEntity="FAC\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="files_shared", joinColumns={
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")}, inverseJoinColumns={
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")})
     */
    protected $allowedUsers;

    /**
     * Add allowedUsers
     *
     * @param \FAC\UserBundle\Entity\User $allowedUsers
     * @return File
     */
    public function addAllowedUser(\FAC\UserBundle\Entity\User $allowedUsers)
    {
        $this->allowedUsers[] = $allowedUsers;

        return $this;
    }

    /**
     * Remove allowedUsers
     *
     * @param \FAC\UserBundle\Entity\User $allowedUsers
     */
    public function removeAllowedUser(\FAC\UserBundle\Entity\User $allowedUsers)
    {
        $this->allowedUsers->removeElement($allowedUsers);
    }

    /**
     * Get allowedUsers
     *
     * @return array 
     */
    public function getAllowedUsers()
    {
        return $this->allowedUsers;
    }
}
