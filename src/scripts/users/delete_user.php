<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 03/12/2016
 * Time: 13:27
 */

namespace MiW16\Results\Scripts\Users;

use Doctrine\ORM\EntityRepository;
use MiW16\Results\Models\Result;
use MiW16\Results\Models\User;
use Doctrine\ORM\EntityManager;

require_once __DIR__ . '/../../../bootstrap.php';

$scriptController = new DeleteUserScriptController($argc, $argv);
$scriptController->main();

class DeleteUserScriptController
{
    const HELP_SHORT_COMMAND_LINE_OPTION = '-h';
    const HELP_LONG_COMMAND_LINE_OPTION = '--help';
    const OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION = '-j';
    const OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION = '--json';
    const DELETE_BY_TOKEN_SHORT_COMMAND_LINE_OPTION = '-t';
    const DELETE_BY_TOKEN_LONG_COMMAND_LINE_OPTION = '--token';

    const FILTER_ARGUMENT_INDEX = 1;

    const FILTER_ARGUMENT = 'filter';

    /**
     * @var int
     */
    private $numberOfCommandLineArguments;

    /**
     * @var array
     */
    private $commandLineArguments;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * ScriptController constructor.
     *
     * @param $numberOfCommandLineArguments
     * @param $commandLineArguments
     */
    public function __construct($numberOfCommandLineArguments, $commandLineArguments)
    {
        $this->setNumberOfCommandLineArguments($numberOfCommandLineArguments);
        $this->setCommandLineArguments($commandLineArguments);
        $this->setEntityManager();
    }

    /**
     * @param mixed $numberOfCommandLineArguments
     */
    private function setNumberOfCommandLineArguments($numberOfCommandLineArguments)
    {
        $this->numberOfCommandLineArguments = $numberOfCommandLineArguments;
    }

    /**
     * @param mixed $commandLineArguments
     */
    private function setCommandLineArguments($commandLineArguments)
    {
        $this->commandLineArguments = $commandLineArguments;
    }

    /**
     *
     */
    public function setEntityManager()
    {
        $this->entityManager = getEntityManager();
    }

    /**
     * @param string $classRepository
     */
    public function setEntityRepository($classRepository)
    {
        $this->entityRepository = $this->entityManager->getRepository($classRepository);
    }

    /**
     *
     */
    private function scriptHelp()
    {
        echo 'Description:' . PHP_EOL;
        echo '  Delete any user from database by internal user id (by default) or user token.' . PHP_EOL . PHP_EOL;
        echo 'Usage:' . PHP_EOL;
        echo '  ' . basename(__FILE__) . ' [' . DeleteUserScriptController::FILTER_ARGUMENT . '] [options]' . PHP_EOL
            . PHP_EOL;
        echo 'Options:' . PHP_EOL;
        echo '  ' . DeleteUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION . ', '
            . DeleteUserScriptController::HELP_LONG_COMMAND_LINE_OPTION . ' Display this help message.' . PHP_EOL;
        echo '  ' . DeleteUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION . ', '
            . DeleteUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION
            . ' Display script output in JSON format.' . PHP_EOL;
        echo '  ' . DeleteUserScriptController::DELETE_BY_TOKEN_SHORT_COMMAND_LINE_OPTION . ', '
            . DeleteUserScriptController::DELETE_BY_TOKEN_LONG_COMMAND_LINE_OPTION . ' Delete user by token.' . PHP_EOL;

        exit;
    }

    /**
     * @return null|string
     */
    private function checkArgumentValues()
    {
        $errorMessage = null;
        $commandLineArguments = $this->commandLineArguments;
        $filterArgument = $commandLineArguments[DeleteUserScriptController::FILTER_ARGUMENT_INDEX];

        if (empty($filterArgument))
            return 'Invalid [' . DeleteUserScriptController::FILTER_ARGUMENT . '] argument';

        if ((DeleteUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION === $filterArgument)
            || (DeleteUserScriptController::HELP_LONG_COMMAND_LINE_OPTION === $filterArgument)
            || (DeleteUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION === $filterArgument)
            || (DeleteUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION === $filterArgument)
            || (DeleteUserScriptController::DELETE_BY_TOKEN_SHORT_COMMAND_LINE_OPTION === $filterArgument)
            || (DeleteUserScriptController::DELETE_BY_TOKEN_LONG_COMMAND_LINE_OPTION === $filterArgument)
        )
            return 'Invalid argument. Options cannot be arguments';

        if (!in_array(DeleteUserScriptController::DELETE_BY_TOKEN_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            && !in_array(DeleteUserScriptController::DELETE_BY_TOKEN_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
            && !is_numeric($filterArgument)
        )
            return 'Invalid [' . DeleteUserScriptController::FILTER_ARGUMENT
                . '] argument. The argument must be a number';

        return $errorMessage;
    }

    /**
     * @return User
     */
    private function execute()
    {
        $commandLineArguments = $this->commandLineArguments;
        $filterArgument = $commandLineArguments[DeleteUserScriptController::FILTER_ARGUMENT_INDEX];

        $this->setEntityRepository(User::CLASS_NAME);

        if (in_array(DeleteUserScriptController::DELETE_BY_TOKEN_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(DeleteUserScriptController::DELETE_BY_TOKEN_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
        ) {
            /**
             * @var User $user
             */
            $user = $this->entityRepository->findOneBy(array(User::TOKEN_ATTRIBUTE => $filterArgument));

            if (empty($user)) {
                echo 'User with "' . $filterArgument . '" token does not exist';
                exit;
            }
        } else {
            /**
             * @var User $user
             */
            $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $filterArgument));

            if (empty($user)) {
                echo 'User with "' . $filterArgument . '" internal ID does not exist';
                exit;
            }
        }

        $this->setEntityRepository(Result::CLASS_NAME);
        $results = $this->entityRepository->findBy(array(Result::USER_ATTRIBUTE => $user));

        foreach ($results as $result)
            $this->entityManager->remove($result);

        $userID = $user->getId();

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $user->setId($userID);

        return $user;
    }

    /**
     * @param User $user
     */
    private function output($user)
    {
        $commandLineArguments = $this->commandLineArguments;

        if (in_array(DeleteUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(DeleteUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
        )
            echo json_encode($user->jsonSerialize());
        else
            echo $user;
    }

    /**
     *
     */
    public function main()
    {
        if (($this->numberOfCommandLineArguments < 2)
            || in_array(DeleteUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $this->commandLineArguments)
            || in_array(DeleteUserScriptController::HELP_LONG_COMMAND_LINE_OPTION, $this->commandLineArguments)
        ) {
            $this->scriptHelp();
            exit;
        }

        $errorMessage = $this->checkArgumentValues();

        if (!is_null($errorMessage)) {
            echo $errorMessage;
            exit;
        }

        $this->output($this->execute());
    }
}