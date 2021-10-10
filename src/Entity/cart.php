<?php
 
namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="cart")
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 */
class cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @var ArrayCollection
     *
     * @Serializer\Expose()
     * @Serializer\SerializedName("carts")
     *
     * @ORM\ManyToMany(targetEntity="products", inversedBy="cart")
     * @ORM\OrderBy({"name" = "ASC"})
     * @Groups({"read", "write"})
     */
    protected $products;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read", "write"})
     */
    public ?\DateTime $created_at = null;

    /**
     * Private
     * @Groups({"read"})
     */
    public float $total = 0;

 
    /******** METHODS ********/

    /**
     * Devices constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|products[]
     * @Assert\Count(
     *      max = "3",
     *      maxMessage = "You must specify max 3 products"
     * )
     * @Assert\Valid()
     */
    public function getProducts(): \Doctrine\ORM\PersistentCollection
    {
        return $this->products;
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

    /**
     * Add Product
     *
     * @param products $product
     * @return $this
     */
    public function addProduct(products $product): self
    {
	$cdx = is_array($this->products) ? count($this->products) : 0;

        if (!$this->products->contains($product) && $cdx < 4) {
            $this->products->add($product);
        }

        return $this;
    }

    /**
     * Remove Product
     *
     * @param products $product
     * @return $this
     */
    public function removeProduct(products $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }

        return $this;
    }

    /**
     * @Groups({"read"})
     *
     */
    public function getTotal(): float
    {
	$total = 0;
        foreach ($this->getProducts() as $product) {
	    $total += $product->getPrice();
	}

        return round($total, 2);
    }

    public function __toString()
    {
        return $this->id;
    }
}