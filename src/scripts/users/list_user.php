<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 03/12/2016
 * Time: 13:27
 */

namespace MiW16\Results\Scripts\Users;

use Doctrine\ORM\EntityRepository;
use MiW16\Results\Models\User;
use Doctrine\ORM\EntityManager;

require_once __DIR__ . '/../../../bootstrap.php';

$scriptController = new ListUserScriptController($argc, $argv);
$scriptController->main();

class ListUserScriptController
{
    const HELP_SHORT_COMMAND_LINE_OPTION = '-h';
    const HELP_LONG_COMMAND_LINE_OPTION = '--help';
    const OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION = '-j';
    const OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION = '--json';
    const LIST_BY_TOKEN_SHORT_COMMAND_LINE_OPTION = '-t';
    const LIST_BY_TOKEN_LONG_COMMAND_LINE_OPTION = '--token';

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
        echo '  List a user from database by internal user id (by default) or user token.' . PHP_EOL . PHP_EOL;
        echo 'Usage:' . PHP_EOL;
        echo '  ' . basename(__FILE__) . ' [' . ListUserScriptController::FILTER_ARGUMENT . '] [options]' . PHP_EOL
            . PHP_EOL;
        echo 'Options:' . PHP_EOL;
        echo '  ' . ListUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION . ', '
            . ListUserScriptController::HELP_LONG_COMMAND_LINE_OPTION . ' Display this help message.' . PHP_EOL;
        echo '  ' . ListUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION . ', '
            . ListUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION
            . ' Display script output in JSON format.' . PHP_EOL;
        echo '  ' . ListUserScriptController::LIST_BY_TOKEN_SHORT_COMMAND_LINE_OPTION . ', '
            . ListUserScriptController::LIST_BY_TOKEN_LONG_COMMAND_LINE_OPTION . ' List user by token.' . PHP_EOL;

        exit;
    }

    /**
     * @return null|string
     */
    private function checkArgumentValues()
    {
        $errorMessage = null;
        $commandLineArguments = $this->commandLineArguments;
        $filterArgument = $commandLineArguments[ListUserScriptController::FILTER_ARGUMENT_INDEX];

        if (empty($filterArgument))
            return 'Invalid [' . ListUserScriptController::FILTER_ARGUMENT . '] argument';

        if ((ListUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION === $filterArgument)
            || (ListUserScriptController::HELP_LONG_COMMAND_LINE_OPTION === $filterArgument)
            || (ListUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION === $filterArgument)
            || (ListUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION === $filterArgument)
            || (ListUserScriptController::LIST_BY_TOKEN_SHORT_COMMAND_LINE_OPTION === $filterArgument)
            || (ListUserScriptController::LIST_BY_TOKEN_LONG_COMMAND_LINE_OPTION === $filterArgument)
        )
            return 'Invalid argument. Options cannot be arguments';

        if (!in_array(ListUserScriptController::LIST_BY_TOKEN_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            && !in_array(ListUserScriptController::LIST_BY_TOKEN_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
            && !is_numeric($filterArgument)
        )
            return 'Invalid [' . ListUserScriptController::FILTER_ARGUMENT
                . '] argument. The argument must be a number';

        return $errorMessage;
    }

    /**
     * @return User
     */
    private function execute()
    {
        $commandLineArguments = $this->commandLineArguments;
        $filterArgument = $commandLineArguments[ListUserScriptController::FILTER_ARGUMENT_INDEX];

        $this->setEntityRepository(User::CLASS_NAME);

        if (in_array(ListUserScriptController::LIST_BY_TOKEN_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(ListUserScriptController::LIST_BY_TOKEN_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
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

        return $user;
    }

    /**
     * @param User $user
     */
    private function output($user)
    {
        $commandLineArguments = $this->commandLineArguments;

        if (in_array(ListUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(ListUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
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
            || in_array(ListUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $this->commandLineArguments)
            || in_array(ListUserScriptController::HELP_LONG_COMMAND_LINE_OPTION, $this->commandLineArguments)
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