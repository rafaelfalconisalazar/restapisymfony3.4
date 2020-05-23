<?php

namespace AppBundle\Controller;

use AppBundle\Dto\CategoryDto;
use AppBundle\Entity\Category;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerBuilder;

/**
 * @Rest\Route("api/v1/categories")
 */
class CategoryController extends FOSRestController
{
    /**
     * @param Request $request
     * @return View
     * @Rest\Post("")
     */
    public function createCategory(Request $request)
    {
        $serializar = SerializerBuilder::create()->build();
        try {
            $categoryDto = $serializar->deserialize($request->getContent(), CategoryDto::class, 'json');
            $categoryDb = $this->getDoctrine()->getRepository(Category::class)->findOneBy(array('name' => $categoryDto->getName()));
            if ($categoryDb != null) return new View("a category with this name allready exist", Response::HTTP_CONFLICT);
            $category = new Category();
            $category->setName($categoryDto->getName());
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return new View("category created", Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new View("bad information send", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return array
     * @Rest\Get("")
     */
    public function listaAllCategory()
    {
        $catogories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $categoriesDto = array();
        foreach ($catogories as $catogory) {
            $categoryDto = new CategoryDto($catogory);
            array_push($categoriesDto, $categoryDto);
        }
        return $categoriesDto;
    }
}
