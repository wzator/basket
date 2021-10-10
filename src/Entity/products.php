<?php
 
namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use JMS\Serializer\Annotation as Serializer;
 
/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="products")
 * @ApiResource(attributes={"pagination_items_per_page"=3})
 */
class products
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;
 
    /**
     * @ORM\Column(length=70)
     * @Assert\NotBlank()
     */
    public string $name;

    /**
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=true)
     * @Assert\NotBlank()
     */
    public float $price;

    /**
     * @var Cart
     *
     * @ORM\ManyToMany(targetEntity="cart", mappedBy="products", cascade={"all"})
     * @ORM\JoinColumn(name="cart_id", referencedColumnName="id")
     */
    protected $cart;


    /**
     * @ORM\Column(type="datetime")
     */
    public ?\DateTime $created_at = null;
 
    /******** METHODS ********/
 
    public function getId()
    {
        return $this->id;
    }

    public function getPrice()
    {
        return $this->price;
    }
 
    /**
     * Prepersist gets triggered on Insert
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        if ($this->created_at == null) {
            $this->created_at = new \DateTime('now');
        }
    }
 
    public function __toString()
    {
        return $this->name;
    }
}