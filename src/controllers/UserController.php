<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 10/12/2016
 * Time: 16:38
 */

namespace MiW16\Results\Controllers;

use MiW16\Results\Models\User;
use MiW16\Results\Models\Result;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    const TOKEN_REQUEST_PARAMETER = 'token';
    const USER_ID_REQUEST_PARAMETER = 'id';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->entityManager = getEntityManager();
        $this->entityRepository = $this->entityManager->getRepository(User::CLASS_NAME);
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return Response
     */
    public function getUserByToken($frontController, $requestParameters)
    {
        $userToken = $requestParameters[UserController::TOKEN_REQUEST_PARAMETER];
        $user = $this->entityRepository->findOneBy(array(User::TOKEN_ATTRIBUTE => $userToken));

        if (is_null($user))
            $user = new User();

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($user->jsonSerialize()));
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return Response
     */
    public function getUserByID($frontController, $requestParameters)
    {
        $userID = $requestParameters[UserController::USER_ID_REQUEST_PARAMETER];
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($user->jsonSerialize()));
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return Response
     */
    public function create($frontController, $requestParameters)
    {
        $user = new User();
        $request = Request::createFromGlobals();
        $requestParameters = json_decode($request->getContent(), true);

        $user->setUsername($requestParameters['name']);
        $user->setEmail($requestParameters['mail']);
        $user->setPassword($requestParameters['password']);
        $user->setToken($requestParameters['token']);
        $user->setEnabled($requestParameters['enabled']);
        $user->setLastLogin(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($user->jsonSerialize()));
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return mixed
     */
    public function getUsers($frontController, $requestParameters)
    {
        $users = $this->entityRepository->findAll();
        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($users));
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return Response
     */
    public function delete($frontController, $requestParameters)
    {
        $userID = $requestParameters[UserController::USER_ID_REQUEST_PARAMETER];
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));

        $this->entityRepository = $this->entityManager->getRepository(Result::CLASS_NAME);
        $results = $this->entityRepository->findBy(array(Result::USER_ATTRIBUTE => $user));

        foreach ($results as $result)
            $this->entityManager->remove($result);

        $userID = $user->getId();

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $user->setId($userID);

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($user->jsonSerialize()));
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return Response
     */
    public function update($frontController, $requestParameters)
    {
        $request = Request::createFromGlobals();
        $requestParameters = json_decode($request->getContent(), true);
        $userID = $requestParameters['id'];

        /**
         * @var User $user
         */
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));

        $user->setUsername($requestParameters['name']);
        $user->setEmail($requestParameters['mail']);
        $user->setPassword($requestParameters['password']);
        $user->setToken($requestParameters['token']);
        $user->setEnabled($requestParameters['enabled']);
        $user->setLastLogin(new \DateTime());

        $this->entityManager->merge($user);
        $this->entityManager->flush();

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($user->jsonSerialize()));
    }
}