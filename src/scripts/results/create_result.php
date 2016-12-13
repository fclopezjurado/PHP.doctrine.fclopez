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

$scriptController = new CreateResultScriptController($argc, $argv);
$scriptController->main();

class CreateResultScriptController
{
    const HELP_SHORT_COMMAND_LINE_OPTION = '-h';
    const HELP_LONG_COMMAND_LINE_OPTION = '--help';
    const OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION = '-j';
    const OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION = '--json';
    const USER_TOKEN_SHORT_COMMAND_LINE_OPTION = '-t';
    const USER_TOKEN_LONG_COMMAND_LINE_OPTION = '--token';

    const RESULT_ARGUMENT_INDEX = 1;
    const USER_ID_ARGUMENT_INDEX = 2;

    const RESULT_ARGUMENT = 'result';
    const USER_ID_ARGUMENT = 'user_id';

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
        echo '  Create a new user result in database.' . PHP_EOL . PHP_EOL;
        echo 'Usage:' . PHP_EOL;
        echo '  ' . basename(__FILE__) . ' [' . CreateResultScriptController::RESULT_ARGUMENT . '] ['
            . CreateResultScriptController::USER_ID_ARGUMENT . '] [options]' . PHP_EOL . PHP_EOL;
        echo 'Options:' . PHP_EOL;
        echo '  ' . CreateResultScriptController::HELP_SHORT_COMMAND_LINE_OPTION . ', '
            . CreateResultScriptController::HELP_LONG_COMMAND_LINE_OPTION . ' Display this help message.' . PHP_EOL;
        echo '  ' . CreateResultScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION . ', '
            . CreateResultScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION
            . ' Display script output in JSON format.' . PHP_EOL;
        echo '  ' . CreateResultScriptController::USER_TOKEN_SHORT_COMMAND_LINE_OPTION . ', '
            . CreateResultScriptController::USER_TOKEN_LONG_COMMAND_LINE_OPTION
            . ' The user identifier is the user token.' . PHP_EOL;

        exit;
    }

    /**
     * @return null|string
     */
    private function checkArgumentValues()
    {
        $errorMessage = null;
        $commandLineArguments = $this->commandLineArguments;
        $mainCommandLineArguments = [$commandLineArguments[CreateResultScriptController::RESULT_ARGUMENT_INDEX],
            $commandLineArguments[CreateResultScriptController::USER_ID_ARGUMENT_INDEX]
        ];

        if (in_array(CreateResultScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(CreateResultScriptController::HELP_LONG_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(CreateResultScriptController::USER_TOKEN_SHORT_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(CreateResultScriptController::USER_TOKEN_LONG_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(CreateResultScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION,
                $mainCommandLineArguments)
            || in_array(CreateResultScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION,
                $mainCommandLineArguments)
        )
            $errorMessage = 'Invalid argument. Options cannot be arguments';

        if (is_null($errorMessage)) {
            $userIDIsUserToken = (in_array(CreateResultScriptController::USER_TOKEN_SHORT_COMMAND_LINE_OPTION,
                    $commandLineArguments) || in_array(
                    CreateResultScriptController::USER_TOKEN_LONG_COMMAND_LINE_OPTION, $commandLineArguments));

            foreach ($this->commandLineArguments as $commandLineArgumentType => $commandLineArgumentValue)
                switch ($commandLineArgumentType) {
                    case CreateResultScriptController::RESULT_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . CreateResultScriptController::RESULT_ARGUMENT . '] argument';
                        else if (!is_numeric($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . CreateResultScriptController::RESULT_ARGUMENT
                                . '] argument. This argument must be an integer';
                        break;
                    case CreateResultScriptController::USER_ID_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . CreateResultScriptController::USER_ID_ARGUMENT . '] argument';
                        if (!$userIDIsUserToken && !is_numeric($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . CreateResultScriptController::USER_ID_ARGUMENT
                                . '] argument. This argument must be an integer';
                        break;
                }
        }

        return $errorMessage;
    }

    /**
     * @return Result
     */
    private function execute()
    {
        $user = new User();
        $commandLineArguments = $this->commandLineArguments;
        $filterArgument = $commandLineArguments[CreateResultScriptController::USER_ID_ARGUMENT_INDEX];
        $result = $commandLineArguments[CreateResultScriptController::RESULT_ARGUMENT_INDEX];

        $this->setEntityRepository(get_class($user));

        if (in_array(CreateResultScriptController::USER_TOKEN_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(CreateResultScriptController::USER_TOKEN_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
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

        $result = new Result($result, $user, new \DateTime());

        $this->entityManager->persist($result);
        $this->entityManager->flush();

        return $result;
    }

    /**
     * @param Result $result
     */
    private function output($result)
    {
        $commandLineArguments = $this->commandLineArguments;

        if (in_array(CreateResultScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(CreateResultScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
        )
            echo json_encode($result->jsonSerialize());
        else
            echo $result;
    }

    /**
     *
     */
    public function main()
    {
        if (($this->numberOfCommandLineArguments < 3)
            || in_array(CreateResultScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $this->commandLineArguments)
            || in_array(CreateResultScriptController::HELP_LONG_COMMAND_LINE_OPTION, $this->commandLineArguments)
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