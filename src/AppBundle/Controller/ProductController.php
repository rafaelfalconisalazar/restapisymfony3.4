<?php

namespace AppBundle\Controller;

use AppBundle\Dto\ProductDto;
use AppBundle\Entity\Product;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerBuilder;

/**
 * @Rest\Route("api/v1/products")
 */
class ProductController extends FOSRestController
{
    /**
     * @Rest\Get("")
     */
    public function getAllProducts()
    {
        $productsDB = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $products = array();
        foreach ($productsDB as $productDB) {
            $product = new ProductDto($productDB);
            array_push($products, $product);
        }
        return $products;
    }

    /**
     * @param $id
     * @return ProductDto|View
     * @Rest\Get("/{id}")
     *
     */
    public function getProductById($id)
    {
        $productDB = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($productDB == null) return new View("product don't find", Response::HTTP_NOT_FOUND);
        $product = new ProductDto($productDB);
        return $product;
    }

    /**
     * @param Request $request
     * @return View
     * @Rest\Post("")
     */
    function createProduct(Request $request)
    {
        $serializar = SerializerBuilder::create()->build();

        try {
            $productDto = $serializar->deserialize($request->getContent(), ProductDto::class, 'json');
            $product = new Product();
            $product->setName($productDto->getName());
            $product->setPrice($productDto->getPrice());
            $em=$this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return new View("product create", Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new View("bad information send",Response::HTTP_BAD_REQUEST);
        }
    }
}
