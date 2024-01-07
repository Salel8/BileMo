<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'app_product', methods: ['GET'])]
    public function getProductList(ProductRepository $productRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        //$productList = $productRepository->findAll();
        //$jsonProductList = $serializer->serialize($productList, 'json', ['groups' => 'getProductsList']);

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getAllProducts-" . $page . "-" . $limit;

        /*$productList = $productRepository->findAllWithPagination($page, $limit);
        $jsonProductList = $serializer->serialize($productList, 'json');*/

        $jsonProductList = $cache->get($idCache, function (ItemInterface $item) use ($productRepository, $page, $limit, $serializer) {
            $item->tag("productsCache");
            $item->expiresAfter(180);
            $productList = $productRepository->findAllWithPagination($page, $limit);
            return $serializer->serialize($productList, 'json');
        });

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }


    #[Route('/api/products/{id}', name: 'detailProduct', methods: ['GET'])]
    public function getDetailProduct($id, Product $product, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse 
    {
        /*$jsonProduct = $serializer->serialize($product, 'json');*/

        $idCache = "getProduct-" . $id;

        $jsonProduct = $cache->get($idCache, function (ItemInterface $item) use ($product, $serializer) {
            $item->tag("productCache");
            $item->expiresAfter(180);
            return $serializer->serialize($product, 'json');
        });

        return new JsonResponse($jsonProduct, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
