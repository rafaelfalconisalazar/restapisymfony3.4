<?php


namespace AppBundle\Dto;

use AppBundle\Entity\Category;
use JMS\Serializer\Annotation\Type;

class CategoryDto
{
    /**
     * @var int
     * @Type("int")
     */
    private $id;

    /**
     * @var string
     * @Type("string")
     */
    private $name;

    /**
     * CategoryDto constructor.
     */
    public function __construct(Category $category)
    {
        $this->id = $category->getId();
        $this->name = $category->getName();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


}