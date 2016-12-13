<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 10/12/2016
 * Time: 16:39
 */

namespace MiW16\Results\Controllers;

use MiW16\Results\Models\User;
use MiW16\Results\Models\Result;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class ResultController
{
    const ID_REQUEST_PARAMETER = 'id';
    const USER_ID_REQUEST_PARAMETER = 'user_id';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * ResultController constructor.
     */
    public function __construct()
    {
        $this->entityManager = getEntityManager();
        $this->entityRepository = $this->entityManager->getRepository(Result::CLASS_NAME);
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return Response
     */
    public function getResultByID($frontController, $requestParameters)
    {
        $resultID = $requestParameters[ResultController::ID_REQUEST_PARAMETER];
        $result = $this->entityRepository->findOneBy(array(Result::ID_ATTRIBUTE => $resultID));

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($result->jsonSerialize()));
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return Response
     */
    public function getResultsByUserID($frontController, $requestParameters)
    {
        $userID = $requestParameters[ResultController::ID_REQUEST_PARAMETER];
        $this->entityRepository = $this->entityManager->getRepository(User::CLASS_NAME);
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));

        $this->entityRepository = $this->entityManager->getRepository(Result::CLASS_NAME);
        $results = $this->entityRepository->findBy(array(Result::USER_ATTRIBUTE => $user));

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($results));
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return Response
     */
    public function create($frontController, $requestParameters)
    {
        $request = Request::createFromGlobals();
        $requestParameters = json_decode($request->getContent(), true);
        $userID = $requestParameters['user_id'];
        $result = $requestParameters['result'];

        $this->entityRepository = $this->entityManager->getRepository(User::CLASS_NAME);
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));
        $result = new Result($result, $user, new \DateTime());

        $this->entityManager->persist($result);
        $this->entityManager->flush();

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($result->jsonSerialize()));
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return mixed
     */
    public function getResults($frontController, $requestParameters)
    {
        $results = $this->entityRepository->findAll();
        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($results));
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return Response
     */
    public function delete($frontController, $requestParameters)
    {
        $results = $this->entityRepository->findAll();
        $resultIDs = array();

        /**
         * @var Result $result
         */
        foreach ($results as $result) {
            $resultIDs[] = $result->getId();
            $this->entityManager->remove($result);
        }

        $this->entityManager->flush();

        foreach ($results as $result)
            $result->setId(array_shift($resultIDs));

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($results));
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return Response
     */
    public function deleteResultByID($frontController, $requestParameters)
    {
        $resultID = $requestParameters[ResultController::ID_REQUEST_PARAMETER];
        $result = $this->entityRepository->findOneBy(array(Result::ID_ATTRIBUTE => $resultID));
        $resultID = $result->getId();

        $this->entityManager->remove($result);
        $this->entityManager->flush();
        $result->setId($resultID);

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($result->jsonSerialize()));
    }

    /**
     * @param FrontController $frontController
     * @param array $requestParameters
     * @return Response
     */
    public function deleteResultsByUserID($frontController, $requestParameters)
    {
        $userID = $requestParameters[ResultController::ID_REQUEST_PARAMETER];
        $this->entityRepository = $this->entityManager->getRepository(User::CLASS_NAME);
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));

        $this->entityRepository = $this->entityManager->getRepository(Result::CLASS_NAME);
        $results = $this->entityRepository->findBy(array(Result::USER_ATTRIBUTE => $user));
        $resultIDs = array();

        /**
         * @var Result $result
         */
        foreach ($results as $result) {
            $resultIDs[] = $result->getId();
            $this->entityManager->remove($result);
        }

        $this->entityManager->flush();

        foreach ($results as $result)
            $result->setId(array_shift($resultIDs));

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($results));
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
        $resultID = $requestParameters['id'];

        /**
         * @var Result $result
         */
        $result = $this->entityRepository->findOneBy(array(Result::ID_ATTRIBUTE => $resultID));

        $result->setResult($requestParameters['result']);

        $this->entityManager->merge($result);
        $this->entityManager->flush();

        return $frontController->generateResponseBody(Response::HTTP_OK, false, json_encode($result->jsonSerialize()));
    }
}