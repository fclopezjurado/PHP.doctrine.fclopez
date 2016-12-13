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

$scriptController = new CreateUserScriptController($argc, $argv);
$scriptController->main();

class CreateUserScriptController
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

    const USERNAME_ARGUMENT = 'username';
    const EMAIL_ARGUMENT = 'email';
    const PASSWORD_ARGUMENT = 'password';
    const TOKEN_ARGUMENT = 'token';

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
        echo '  Stores a new user in database.' . PHP_EOL . PHP_EOL;
        echo 'Usage:' . PHP_EOL;
        echo '  ' . basename(__FILE__) . ' [' . CreateUserScriptController::USERNAME_ARGUMENT . '] ['
            . CreateUserScriptController::EMAIL_ARGUMENT . '] [' . CreateUserScriptController::PASSWORD_ARGUMENT . '] ['
            . CreateUserScriptController::TOKEN_ARGUMENT . '] [options]' . PHP_EOL . PHP_EOL;
        echo 'Options:' . PHP_EOL;
        echo '  ' . CreateUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION . ', '
            . CreateUserScriptController::HELP_LONG_COMMAND_LINE_OPTION . ' Display this help message.' . PHP_EOL;
        echo '  ' . CreateUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION . ', '
            . CreateUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION
            . ' Display script output in JSON format.' . PHP_EOL;
        echo '  ' . CreateUserScriptController::ENABLE_USER_SHORT_COMMAND_LINE_OPTION . ', '
            . CreateUserScriptController::ENABLE_USER_LONG_COMMAND_LINE_OPTION . ' Enable the new user.' . PHP_EOL;

        exit;
    }

    /**
     * @return null|string
     */
    private function checkArgumentValues()
    {
        $errorMessage = null;
        $commandLineArguments = $this->commandLineArguments;
        $mainCommandLineArguments = [$commandLineArguments[CreateUserScriptController::USERNAME_ARGUMENT_INDEX],
            $commandLineArguments[CreateUserScriptController::EMAIL_ARGUMENT_INDEX],
            $commandLineArguments[CreateUserScriptController::PASSWORD_ARGUMENT_INDEX],
            $commandLineArguments[CreateUserScriptController::TOKEN_ARGUMENT_INDEX]
        ];

        if (in_array(CreateUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(CreateUserScriptController::HELP_LONG_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(CreateUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(CreateUserScriptController::ENABLE_USER_SHORT_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(CreateUserScriptController::ENABLE_USER_LONG_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(CreateUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION,
                $mainCommandLineArguments)
        )
            $errorMessage = 'Invalid argument. Options cannot be arguments';

        if (is_null($errorMessage))
            foreach ($this->commandLineArguments as $commandLineArgumentType => $commandLineArgumentValue)
                switch ($commandLineArgumentType) {
                    case CreateUserScriptController::USERNAME_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . CreateUserScriptController::USERNAME_ARGUMENT . '] argument';
                        break;
                    case CreateUserScriptController::EMAIL_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . CreateUserScriptController::EMAIL_ARGUMENT . '] argument';
                        break;
                    case CreateUserScriptController::PASSWORD_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . CreateUserScriptController::PASSWORD_ARGUMENT . '] argument';
                        break;
                    case CreateUserScriptController::TOKEN_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue))
                            $errorMessage = 'Invalid [' . CreateUserScriptController::TOKEN_ARGUMENT . '] argument';
                        break;
                }

        return $errorMessage;
    }

    /**
     * @return User
     */
    private function execute()
    {
        $user = new User();
        $commandLineArguments = $this->commandLineArguments;

        $user->setUsername($commandLineArguments[CreateUserScriptController::USERNAME_ARGUMENT_INDEX]);
        $user->setEmail($commandLineArguments[CreateUserScriptController::EMAIL_ARGUMENT_INDEX]);
        $user->setPassword($commandLineArguments[CreateUserScriptController::PASSWORD_ARGUMENT_INDEX]);
        $user->setToken($commandLineArguments[CreateUserScriptController::TOKEN_ARGUMENT_INDEX]);
        $user->setEnabled(false);

        if (in_array(CreateUserScriptController::ENABLE_USER_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(CreateUserScriptController::ENABLE_USER_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
        )
            $user->setEnabled(true);

        $user->setLastLogin(new \DateTime());
        $this->setEntityRepository(get_class($user));
        $users = $this->entityRepository->findBy(array(User::TOKEN_ATTRIBUTE => $user->getToken()));

        if (!empty($users)) {
            echo 'User with "' . $user->getToken() . '" token already exists';
            exit;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param User $user
     */
    private function output($user)
    {
        $commandLineArguments = $this->commandLineArguments;

        if (in_array(CreateUserScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(CreateUserScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
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
        if (($this->numberOfCommandLineArguments < 5)
            || in_array(CreateUserScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $this->commandLineArguments)
            || in_array(CreateUserScriptController::HELP_LONG_COMMAND_LINE_OPTION, $this->commandLineArguments)
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