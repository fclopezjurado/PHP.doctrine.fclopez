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

$scriptController = new UpdateUserScriptController($argc, $argv);
$scriptController->main();

class UpdateUserScriptController
{
    const HELP_SHORT_COMMAND_LINE_OPTION = '-h';
    const HELP_LONG_COMMAND_LINE_OPTION = '--help';
    const OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION = '-j';
    const OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION = '--json';
    const ENABLE_USER_SHORT_COMMAND_LINE_OPTION = '-e';
    const ENABLE_USER_LONG_COMMAND_LINE_OPTION = '--enable';

    const USERNAME_ARGUMENT_INDEX = 1;
    const EMAIL_ARGUMENT_INDEX = 2;
    const PASSWORD_ARGUMENT_INDEX = 3;
    const TOKEN_ARGUMENT_INDEX = 4;
    const ID_ARGUMENT_INDEX = 5;

    const USERNAME_ARGUMENT = 'username';
    const EMAIL_ARGUMENT = 'email';
    const PASSWORD_ARGUMENT = 'password';
    const TOKEN_ARGUMENT = 'token';
    const ID_ARGUMENT = 'id';

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
        echo '  Update a user from database by user id.' . PHP_EOL
            . PHP_EOL;
        echo 'Usage:' . PHP_EOL;
        echo '  ' . basename(__FILE__) . ' [' . UpdateUserScriptController::USERNAME_ARGUMENT . '] ['
            . UpdateUserScriptController::EMAIL_ARGUMENT . '] [' . UpdateUserScriptController::PASSWORD_ARGUMENT . '] ['
            . UpdateUserScriptController::TOKEN_ARGUMENT . '] [' . UpdateUserScriptController::ID_ARGUMENT . '] '
            . '[options]' . PHP_EOL . PHP_EOL;
        echo 'Options:' . PHP_EOL;
        echo '  ' . UpdateUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION . ', '
            . UpdateUserScriptController::HELP_LONG_COMMAND_LINE_OPTION . ' Display this help message.' . PHP_EOL;
        echo '  ' . UpdateUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION . ', '
            . UpdateUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION
            . ' Display script output in JSON format.' . PHP_EOL;
        echo '  ' . UpdateUserScriptController::ENABLE_USER_SHORT_COMMAND_LINE_OPTION . ', '
            . UpdateUserScriptController::ENABLE_USER_LONG_COMMAND_LINE_OPTION . ' Enable the user.' . PHP_EOL;

        exit;
    }

    /**
     * @return null|string
     */
    private function checkArgumentValues()
    {
        $errorMessage = null;
        $commandLineArguments = $this->commandLineArguments;
        $mainCommandLineArguments = [$commandLineArguments[UpdateUserScriptController::USERNAME_ARGUMENT_INDEX],
            $commandLineArguments[UpdateUserScriptController::EMAIL_ARGUMENT_INDEX],
            $commandLineArguments[UpdateUserScriptController::PASSWORD_ARGUMENT_INDEX],
            $commandLineArguments[UpdateUserScriptController::TOKEN_ARGUMENT_INDEX],
            $commandLineArguments[UpdateUserScriptController::ID_ARGUMENT_INDEX]
        ];

        if (in_array(UpdateUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(UpdateUserScriptController::HELP_LONG_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(UpdateUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(UpdateUserScriptController::ENABLE_USER_SHORT_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(UpdateUserScriptController::ENABLE_USER_LONG_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(UpdateUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION,
                $mainCommandLineArguments)
        )
            $errorMessage = 'Invalid argument. Options cannot be arguments';

        if (is_null($errorMessage))
            foreach ($this->commandLineArguments as $commandLineArgumentType => $commandLineArgumentValue)
                switch ($commandLineArgumentType) {
                    case UpdateUserScriptController::USERNAME_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . UpdateUserScriptController::USERNAME_ARGUMENT . '] argument';
                        break;
                    case UpdateUserScriptController::EMAIL_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . UpdateUserScriptController::EMAIL_ARGUMENT . '] argument';
                        break;
                    case UpdateUserScriptController::PASSWORD_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . UpdateUserScriptController::PASSWORD_ARGUMENT . '] argument';
                        break;
                    case UpdateUserScriptController::TOKEN_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . UpdateUserScriptController::TOKEN_ARGUMENT . '] argument';
                        break;
                    case UpdateUserScriptController::ID_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue) || (!empty($commandLineArgumentValue)
                                && !is_numeric($commandLineArgumentValue))
                        )
                            $errorMessage = 'Invalid [' . UpdateUserScriptController::ID_ARGUMENT . '] argument';
                        break;
                }

        return $errorMessage;
    }

    /**
     * @return User
     */
    private function execute()
    {
        $commandLineArguments = $this->commandLineArguments;
        $userID = intval($commandLineArguments[UpdateUserScriptController::ID_ARGUMENT_INDEX]);

        $this->setEntityRepository(User::CLASS_NAME);

        /**
         * @var User $user
         */
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));

        if (is_null($user)) {
            echo 'User with "' . $userID . '" internal ID does not exist';
            exit;
        }

        $user->setUsername($commandLineArguments[UpdateUserScriptController::USERNAME_ARGUMENT_INDEX]);
        $user->setEmail($commandLineArguments[UpdateUserScriptController::EMAIL_ARGUMENT_INDEX]);
        $user->setPassword($commandLineArguments[UpdateUserScriptController::PASSWORD_ARGUMENT_INDEX]);
        $user->setToken($commandLineArguments[UpdateUserScriptController::TOKEN_ARGUMENT_INDEX]);
        $user->setEnabled(false);

        if (in_array(UpdateUserScriptController::ENABLE_USER_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(UpdateUserScriptController::ENABLE_USER_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
        )
            $user->setEnabled(true);

        $user->setLastLogin(new \DateTime());

        $users = $this->entityRepository->findBy(array(User::TOKEN_ATTRIBUTE => $user->getToken()));

        /**
         * @var User $userFromDatabase
         */
        foreach ($users as $userFromDatabase)
            if (!empty($userFromDatabase) && ($userFromDatabase->getId() !== $userID)) {
                echo 'User with "' . $user->getToken() . '" token already exists';
                exit;
            }

        $this->entityManager->merge($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param User $user
     */
    private function output($user)
    {
        $commandLineArguments = $this->commandLineArguments;

        if (in_array(UpdateUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(UpdateUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
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
        if (($this->numberOfCommandLineArguments < 6)
            || in_array(UpdateUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $this->commandLineArguments)
            || in_array(UpdateUserScriptController::HELP_LONG_COMMAND_LINE_OPTION, $this->commandLineArguments)
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