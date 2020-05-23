<?php

namespace AppBundle\Controller;

use AppBundle\Dto\ProductDto;
use AppBundle\Entity\Category;
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
     * @param $name
     * @return array
     * @Rest\Get("/categoryname/{name}")
     */
    public function getProductByCategoryName($name)
    {
        $productsDB = $this->getDoctrine()->getRepository(Product::class)->getProductsByCategoryName($name);
        $products = array();
        foreach ($productsDB as $productDB) {
            $product = new ProductDto($productDB);
            array_push($products, $product);
        }
        return $products;
    }
    /**
     * @param $name
     * @return array
     * @Rest\Get("/categorynameFrom/{name}")
     */
    public function getProductByCategoryNameFrom($name)
    {
        $productsDB = $this->getDoctrine()->getRepository(Product::class)->getProductByCategoryNameFrom($name);
        $products = array();
        foreach ($productsDB as $productDB) {
            $product = new ProductDto($productDB);
            array_push($products, $product);
        }
        return $products;
    }

    /**
     * @param $name
     * @return mixed
     * @Rest\Get("/categorynamesql/{name}")
     */
    public function getProductByCategoryNameSql($name)
    {
        $sql = "SELECT product.id, product.name, price, category_id 
                FROM product , category 
                where category_id= category.id 
                and category.name='$name'";
        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll();
        $array_num = count($products);
        for ($i = 0; $i < $array_num; $i++) {
             $products[$i]["name"]=$products[$i]["name"]." editado";
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
            $productDb = $this->getDoctrine()->getRepository(Product::class)->findOneBy(array("name" => $productDto->getName()));
            if ($productDb != null) return new View("a product with this name allready exist", Response::HTTP_CONFLICT);
            $category = $this->getDoctrine()->getRepository(Category::class)->find($productDto->getCategory()->getId());
            if ($category == null) return new View("a category with this id doesn't exist", Response::HTTP_CONFLICT);
            $product = new Product();
            $product->setName($productDto->getName());
            $product->setPrice($productDto->getPrice());
            $product->setCategory($category);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return new View("product create", Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new View("bad information send", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return View
     * @Rest\Put("/{id}")
     */
    function editProduct(Request $request, $id)
    {
        $productDB = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($productDB == null) return new View("product don't find", Response::HTTP_NOT_FOUND);
        $serializar = SerializerBuilder::create()->build();
        try {
            $productDto = $serializar->deserialize($request->getContent(), ProductDto::class, 'json');
            $productDb = $this->getDoctrine()->getRepository(Product::class)->findOneBy(array("name" => $productDto->getName()));
            if ($productDb != null) {
                if ($id != $productDb->getId()) return new View("a product with this name allready exist", Response::HTTP_CONFLICT);
            }
            $category = $this->getDoctrine()->getRepository(Category::class)->find($productDto->getCategory()->getId());
            if ($category == null) return new View("a category with this id doesn't exist", Response::HTTP_CONFLICT);
            $productDB->setName($productDto->getName());
            $productDB->setPrice($productDto->getPrice());
            $productDB->setCategory($category);
            $em = $this->getDoctrine()->getManager();
            $em->persist($productDB);
            $em->flush();
            return new View("product edited", Response::HTTP_ACCEPTED);

        } catch (\Exception $e) {
            return new View("bad information send", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $id
     * @return View
     * @Rest\Delete("/{id}")
     */
    public function deleteProduct($id)
    {
        $productDB = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($productDB == null) return new View("product don't find", Response::HTTP_NOT_FOUND);
        $em = $this->getDoctrine()->getManager();
        $em->remove($productDB);
        $em->flush();
        return new View("product deleted", Response::HTTP_ACCEPTED);
    }
}
