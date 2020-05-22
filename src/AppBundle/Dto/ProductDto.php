<?php

namespace AppBundle\Dto;

use JMS\Serializer\Annotation\Type;
use AppBundle\Entity\Product;

class ProductDto
{
    /**
     *@Type("int")
     */
    private $id;

    /**
     *@Type("string")
     */
    private $name;

    /**
     *@Type("double")
     */
    private $price;

    public function __construct(Product $product)
    {
        $this->id = $product->getId();
        $this->name = $product->getName();
        $this->price = $product->getPrice();
    }

    public function getId()
    {
        return $this->id;
    }


    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }


    public function getName()
    {
        return $this->name;
    }


    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }


    public function getPrice()
    {
        return $this->price;
    }
}
