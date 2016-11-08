<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserGallery
 *
 * @ORM\Table(name="user_gallery")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserGalleryRepository")
 */
class UserGallery
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="image_name", type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @var string
     *
     * @ORM\Column(name="image_path", type="string", length=255)
     */
    private $imagePath;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="images", cascade={"persist"})
     * @ORM\JoinColumn(name="owner_images", referencedColumnName="id")
     */
    private $owner_images;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set imageName
     *
     * @param string $imageName
     *
     * @return UserGallery
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * Set imagePath
     *
     * @param string $imagePath
     *
     * @return UserGallery
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * Get imagePath
     *
     * @return string
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * Set ownerImages
     *
     * @param \AppBundle\Entity\User $ownerImages
     *
     * @return UserGallery
     */
    public function setOwnerImages(\AppBundle\Entity\User $ownerImages = null)
    {
        $this->owner_images = $ownerImages;

        return $this;
    }

    /**
     * Get ownerImages
     *
     * @return \AppBundle\Entity\User
     */
    public function getOwnerImages()
    {
        return $this->owner_images;
    }
}
