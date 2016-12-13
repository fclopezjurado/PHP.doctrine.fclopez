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

$scriptController = new ListUsersScriptController($argc, $argv);
$scriptController->main();

class ListUsersScriptController
{
    const HELP_SHORT_COMMAND_LINE_OPTION = '-h';
    const HELP_LONG_COMMAND_LINE_OPTION = '--help';
    const OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION = '-j';
    const OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION = '--json';

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
        echo '  List all users stored in database.' . PHP_EOL . PHP_EOL;
        echo 'Usage:' . PHP_EOL;
        echo '  ' . basename(__FILE__) . ' [options]' . PHP_EOL . PHP_EOL;
        echo 'Options:' . PHP_EOL;
        echo '  ' . ListUsersScriptController::HELP_SHORT_COMMAND_LINE_OPTION . ', '
            . ListUsersScriptController::HELP_LONG_COMMAND_LINE_OPTION . ' Display this help message.' . PHP_EOL;
        echo '  ' . ListUsersScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION . ', '
            . ListUsersScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION
            . ' Display script output in JSON format.' . PHP_EOL;

        exit;
    }

    /**
     * @return array
     */
    private function execute()
    {
        $this->setEntityRepository(User::CLASS_NAME);
        $users = $this->entityRepository->findAll();

        return $users;
    }

    /**
     * @param array $users
     */
    private function output($users)
    {
        $commandLineArguments = $this->commandLineArguments;
        $outputInJSONFormat = in_array(ListUsersScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION,
                $commandLineArguments) || in_array(ListUsersScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION,
                $commandLineArguments);

        echo 'Number of users: ' . count($users) . PHP_EOL . PHP_EOL;

        if (count($users) > 0) {
            if ($outputInJSONFormat) {
                $jsonOutput = '';

                foreach ($users as $user) {
                    if (empty($jsonOutput))
                        $jsonOutput .= '[' . json_encode($user->jsonSerialize());
                    else
                        $jsonOutput .= ', ' . json_encode($user->jsonSerialize());
                }

                echo $jsonOutput . ']';
            } else
                echo implode(',' . PHP_EOL, $users);
        }
    }

    /**
     *
     */
    public function main()
    {
        if (in_array(ListUsersScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $this->commandLineArguments)
            || in_array(ListUsersScriptController::HELP_LONG_COMMAND_LINE_OPTION, $this->commandLineArguments)
        ) {
            $this->scriptHelp();
            exit;
        }

        $this->output($this->execute());
    }
}