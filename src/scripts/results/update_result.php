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
use Doctrine\ORM\EntityManager;

require_once __DIR__ . '/../../../bootstrap.php';

$scriptController = new UpdateResultScriptController($argc, $argv);
$scriptController->main();

class UpdateResultScriptController
{
    const HELP_SHORT_COMMAND_LINE_OPTION = '-h';
    const HELP_LONG_COMMAND_LINE_OPTION = '--help';
    const OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION = '-j';
    const OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION = '--json';

    const RESULT_ARGUMENT_INDEX = 1;
    const ID_ARGUMENT_INDEX = 2;

    const RESULT_ARGUMENT = 'result';
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
        echo '  Update a user result from database by internal result id.' . PHP_EOL
            . PHP_EOL;
        echo 'Usage:' . PHP_EOL;
        echo '  ' . basename(__FILE__) . ' [' . UpdateResultScriptController::RESULT_ARGUMENT . '] ['
            . UpdateResultScriptController::ID_ARGUMENT . '] [options]' . PHP_EOL . PHP_EOL;
        echo 'Options:' . PHP_EOL;
        echo '  ' . UpdateResultScriptController::HELP_SHORT_COMMAND_LINE_OPTION . ', '
            . UpdateResultScriptController::HELP_LONG_COMMAND_LINE_OPTION . ' Display this help message.' . PHP_EOL;
        echo '  ' . UpdateResultScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION . ', '
            . UpdateResultScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION
            . ' Display script output in JSON format.' . PHP_EOL;

        exit;
    }

    /**
     * @return null|string
     */
    private function checkArgumentValues()
    {
        $errorMessage = null;
        $commandLineArguments = $this->commandLineArguments;
        $mainCommandLineArguments = [$commandLineArguments[UpdateResultScriptController::RESULT_ARGUMENT_INDEX],
            $commandLineArguments[UpdateResultScriptController::ID_ARGUMENT_INDEX]
        ];

        if (in_array(UpdateResultScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(UpdateResultScriptController::HELP_LONG_COMMAND_LINE_OPTION, $mainCommandLineArguments)
            || in_array(UpdateResultScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION,
                $mainCommandLineArguments)
            || in_array(UpdateResultScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION,
                $mainCommandLineArguments)
        )
            $errorMessage = 'Invalid argument. Options cannot be arguments';

        if (is_null($errorMessage))
            foreach ($this->commandLineArguments as $commandLineArgumentType => $commandLineArgumentValue)
                switch ($commandLineArgumentType) {
                    case UpdateResultScriptController::RESULT_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue) || (!empty($commandLineArgumentValue)
                                && !is_numeric($commandLineArgumentValue))
                        )
                            $errorMessage = 'Invalid [' . UpdateResultScriptController::RESULT_ARGUMENT . '] argument';
                        break;
                    case UpdateResultScriptController::ID_ARGUMENT_INDEX:
                        if (empty($commandLineArgumentValue) || (!empty($commandLineArgumentValue)
                                && !is_numeric($commandLineArgumentValue))
                        )
                            $errorMessage = 'Invalid [' . UpdateResultScriptController::ID_ARGUMENT . '] argument';
                        break;
                }

        return $errorMessage;
    }

    /**
     * @return Result
     */
    private function execute()
    {
        $commandLineArguments = $this->commandLineArguments;
        $userResult = intval($commandLineArguments[UpdateResultScriptController::RESULT_ARGUMENT_INDEX]);
        $userResultID = intval($commandLineArguments[UpdateResultScriptController::ID_ARGUMENT_INDEX]);

        $this->setEntityRepository(Result::CLASS_NAME);

        /**
         * @var Result $result
         */
        $result = $this->entityRepository->findOneBy(array(Result::ID_ATTRIBUTE => $userResultID));

        if (is_null($result)) {
            echo 'Result with "' . $userResultID . '" internal ID does not exist';
            exit;
        }

        $result->setResult($userResult);
        $result->setTime(new \DateTime());

        $this->entityManager->merge($result);
        $this->entityManager->flush();

        return $result;
    }

    /**
     * @param Result $result
     */
    private function output($result)
    {
        $commandLineArguments = $this->commandLineArguments;

        if (in_array(UpdateResultScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(UpdateResultScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
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
            || in_array(UpdateResultScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $this->commandLineArguments)
            || in_array(UpdateResultScriptController::HELP_LONG_COMMAND_LINE_OPTION, $this->commandLineArguments)
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