<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'app_product', methods: ['GET'])]
    public function getProductList(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        /*return new JsonResponse([
            'message' => 'welcome to your new controller!',
            'path' => 'src/Controller/BookController.php',
        ]);*/
        $productList = $productRepository->findAll();
        $jsonProductList = $serializer->serialize($productList, 'json', ['groups' => 'getProductsList']);
        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }


    #[Route('/api/products/{id}', name: 'detailProduct', methods: ['GET'])]
    public function getDetailProduct(Product $product, SerializerInterface $serializer): JsonResponse 
    {
        $jsonProduct = $serializer->serialize($product, 'json');
        return new JsonResponse($jsonProduct, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
