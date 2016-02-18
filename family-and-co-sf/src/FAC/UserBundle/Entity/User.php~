<?php
namespace FAC\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    protected $sharedFiles;

    /**
     * @var integer
     *
     * @ORM\Column(name="selected_folder", type="integer", nullable=true)
     */
    protected $selected_folder;

    /**
     * @var array
     *
     * @ORM\Column(name="back_params", type="array", nullable=true)
     */
    protected $back_params;

    /**
     * @var array
     *
     * @ORM\Column(name="front_params", type="array", nullable=true)
     */
    protected $front_params;

    /**
     * @ORM\OneToMany(targetEntity="FAC\FileBundle\Entity\File", mappedBy="user")
     */
    protected $files;


    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->files = new ArrayCollection();
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
     * Add files
     *
     * @param \FAC\FileBundle\Entity\File $files
     * @return User
     */
    public function addFile(\FAC\FileBundle\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \FAC\FileBundle\Entity\File $files
     */
    public function removeFile(\FAC\FileBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set selected_folder
     *
     * @param integer $selectedFolder
     * @return User
     */
    public function setSelectedFolder($selectedFolder)
    {
        $this->selected_folder = $selectedFolder;

        return $this;
    }

    /**
     * Get selected_folder
     *
     * @return integer 
     */
    public function getSelectedFolder()
    {
        return $this->selected_folder;
    }

    /**
     * Set back_params
     *
     * @param array $backParams
     * @return User
     */
    public function setBackParams($backParams)
    {
        $this->back_params = $backParams;

        return $this;
    }

    /**
     * Get back_params
     *
     * @return array 
     */
    public function getBackParams()
    {
        return $this->back_params;
    }

    /**
     * Set front_params
     *
     * @param array $frontParams
     * @return User
     */
    public function setFrontParams($frontParams)
    {
        $this->front_params = $frontParams;

        return $this;
    }

    /**
     * Get front_params
     *
     * @return array 
     */
    public function getFrontParams()
    {
        return $this->front_params;
    }

    /**
     * Add sharedFiles
     *
     * @param \FAC\FileBundle\Entity\File $sharedFiles
     * @return User
     */
    public function addSharedFile(\FAC\FileBundle\Entity\File $sharedFiles)
    {
        $this->sharedFiles[] = $sharedFiles;

        return $this;
    }

    /**
     * Remove sharedFiles
     *
     * @param \FAC\FileBundle\Entity\File $sharedFiles
     */
    public function removeSharedFile(\FAC\FileBundle\Entity\File $sharedFiles)
    {
        $this->sharedFiles->removeElement($sharedFiles);
    }

    /**
     * Get sharedFiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSharedFiles()
    {
        return $this->sharedFiles;
    }
}
