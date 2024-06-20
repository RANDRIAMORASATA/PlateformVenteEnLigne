<?php 
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="payments")
 */
class Payment{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity=Order::class, inversedBy="payments")
     * @ORM\JoinColumn( nullable=false)
     */
    private $order;

    /**
     * @ORM\Column(type="decimal")
     */
    private $amount;

    /**
     * @ORM\Column(type="string")
     */
    private $paymentMethod;

    /**
     * @ORM\Column(type="datetime")
    */
     private $created_at;

    

    
    public function __construct(){
        $this->created_at = new \DateTime();
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of orderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set the value of orderId
     */
    public function setOrderId($orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get the value of amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     */
    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }
    /**
     * Get the value of paymentMethod
     */
    public function getPaymentMethode()
    {
        return $this->paymentMethod;
    }

    /**
     * Set the value of paymentMethod
     */
    public function setPaymentMethode($paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }


    /**
     * Get the value of created_at
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     */
    public function setCreatedAt($created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

}