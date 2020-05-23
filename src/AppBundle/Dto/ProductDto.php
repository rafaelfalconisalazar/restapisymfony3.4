<?php

namespace AppBundle\Dto;

use JMS\Serializer\Annotation\Type;
use AppBundle\Entity\Product;

class ProductDto
{
    /**
     * @Type("int")
     */
    private $id;

    /**
     * @Type("string")
     */
    private $name;

    /**
     * @Type("double")
     */
    private $price;
    /**
     * @var CategoryDto
     * @Type ("AppBundle\Dto\CategoryDto")
     */
    private $category;

    public function __construct(Product $product)
    {
        $this->id = $product->getId();
        $this->name = $product->getName();
        $this->price = $product->getPrice();
        $this->category = new CategoryDto($product->getCategory());
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

    /**
     * @return CategoryDto
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param CategoryDto $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }


}
