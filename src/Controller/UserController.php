<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
//use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class UserController extends AbstractController
{
    /*#[Route('/api/users/{client}', name: 'app_user', methods: ['GET'])]
    public function getUserList(string $client, CustomerRepository $customerRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        //$userList = $userRepository->findAll();

        //$userList = $userRepository->findBy(
            //['customer' => array('name'=>$client)],
            //['id' => 'ASC']
        //);

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $customerList = $customerRepository->findByWithPagination($client, $page, $limit);
        $jsonUserList = $serializer->serialize($customerList, 'json', ['groups' => 'getUsersList']);
        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }*/

    /**
     * Cette méthode permet de récupérer l'ensemble des utilisateurs.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des utilisateurs",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUser"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Utilisateur")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/users', name: 'app_user', methods: ['GET'])]
    public function getUserListt(UserRepository $userRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $client = $request->get('client');
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getAllUsers-" . $client . "-" . $page . "-" . $limit;

        $jsonUserList = $cache->get($idCache, function (ItemInterface $item) use ($userRepository, $client, $page, $limit, $serializer) {
            $item->tag("usersCache");
            $item->expiresAfter(180);
            $userList = $userRepository->findByWithPagination($client, $page, $limit);
            $context = SerializationContext::create()->setGroups(['getUser']);
            //return $serializer->serialize($userList, 'json', ['groups' => 'getUser']);
            return $serializer->serialize($userList, 'json', $context);
        });

        /*$userList = $userRepository->findByWithPagination($client, $page, $limit);
        $jsonUserList = $serializer->serialize($userList, 'json', ['groups' => 'getUser']);*/
        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }


    /**
     * Cette méthode permet de récupérer un unique utilisateur.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne un unique utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUser"}))
     *     )
     * )
     * @OA\Tag(name="Utilisateur")
     *
     * @param User $user
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/users/{id}', name: 'detailUser', methods: ['GET'])]
    public function getDetailUser($id, User $user, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse 
    {
        /*$jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUser']);*/

        $idCache = "getUser-" . $id;

        $jsonUser = $cache->get($idCache, function (ItemInterface $item) use ($user, $serializer) {
            $item->tag("userCache");
            $item->expiresAfter(180);
            $context = SerializationContext::create()->setGroups(['getUser']);
            //return $serializer->serialize($user, 'json', ['groups' => 'getUser']);
            return $serializer->serialize($user, 'json', $context);
        });

        return new JsonResponse($jsonUser, Response::HTTP_OK, ['accept' => 'json'], true);
    }


    /**
     * Cette méthode permet de supprimer un utilisateur.
     *
     * @OA\Response(
     *     response=200,
     *     description="Supprime un utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @OA\Tag(name="Utilisateur")
     *
     * @param User $user
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $em, TagAwareCacheInterface $cache): JsonResponse 
    {
        $cachePool->invalidateTags(["usersCache"]);
        $cachePool->invalidateTags(["userCache"]);
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * Cette méthode permet de créer un utilisateur.
     *
     * @OA\Response(
     *     response=200,
     *     description="Crée un utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @OA\Tag(name="Utilisateur")
     *
     * @param User $user
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param CustomerRepository $customerRepository
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @return JsonResponse
     */
    #[Route('/api/users', name:"createUser", methods: ['POST'])]
    public function createUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, CustomerRepository $customerRepository, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher): JsonResponse 
    {

        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        // On vérifie les erreurs
        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        // Récupération de l'ensemble des données envoyées sous forme de tableau
        $content = $request->toArray();

        // Récupération de l'idCustomer. S'il n'est pas défini, alors on met 5 par défaut.
        //$idCustomer = $content['idCustomer'] ?? 5;
        //$user->setCustomer($customerRepository->find($idCustomer));
        $nameCustomer=$content['nameCustomer'];
        $user->setCustomer($customerRepository->findOneBy(['name' => $nameCustomer]));

        $password=$content['password'];
        $user->setPassword($userPasswordHasher->hashPassword($user, $password));

        $em->persist($user);
        $em->flush();

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUser']);
        
        $location = $urlGenerator->generate('detailUser', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
   }


   /**
     * Cette méthode permet de mettre à jour un utilisateur.
     *
     * @OA\Response(
     *     response=200,
     *     description="Met à jour un utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @OA\Tag(name="Utilisateur")
     *
     * @param User $currentUser
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param CustomerRepository $customerRepository
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @return JsonResponse
     */
   #[Route('/api/users/{id}', name:"updateUser", methods:['PUT'])]
    public function updateUser(Request $request, SerializerInterface $serializer, User $currentUser, EntityManagerInterface $em, CustomerRepository $customerRepository, ValidatorInterface $validator, TagAwareCacheInterface $cache, UserPasswordHasherInterface $userPasswordHasher): JsonResponse 
    {
        /*$updatedUser = $serializer->deserialize($request->getContent(), 
                User::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser]);*/

        $updatedUser = $serializer->deserialize($request->getContent(), User::class, 'json');
        $currentUser->setName($updatedUser->getName());
        $currentUser->setFirstName($updatedUser->getFirstName());
        $currentUser->setWork($updatedUser->getWork());
        $currentUser->setEmail($updatedUser->getEmail());
        $currentUser->setPassword($userPasswordHasher->hashPassword($currentUser, $updatedUser->getPassword()));
        //$utilisateur1->setName('xavier');
        //$utilisateur1->setFirstName('charles');
        //$utilisateur1->setWork('manager');
        //$utilisateur1->setCustomer($listCustomer[0]);
        //$utilisateur1->setEmail('xavier.charles@hotmail.fr');
        //$utilisateur1->setPassword($this->userPasswordHasher->hashPassword($utilisateur1, "xavier"));

        $errors = $validator->validate($currentUser);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        
        $content = $request->toArray();
        $nameCustomer=$content['nameCustomer'];

        //$updatedUser->setCustomer($customerRepository->findOneBy(['name' => $nameCustomer]));
        $currentUser->setCustomer($customerRepository->findOneBy(['name' => $nameCustomer]));
        
        
        //$em->persist($updatedUser);
        $em->persist($currentUser);
        $em->flush();

        $cachePool->invalidateTags(["usersCache"]);
        $cachePool->invalidateTags(["userCache"]);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
   }
}
