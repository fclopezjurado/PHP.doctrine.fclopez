<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 03/12/2016
 * Time: 13:27
 */

namespace MiW16\Results\Scripts\Users;

use Doctrine\ORM\EntityRepository;
use MiW16\Results\Entity\Result;
use Doctrine\ORM\EntityManager;

require_once __DIR__ . '/../../../bootstrap.php';

$scriptController = new ListResultScriptController($argc, $argv);
$scriptController->main();

class ListResultScriptController
{
    const HELP_SHORT_COMMAND_LINE_OPTION = '-h';
    const HELP_LONG_COMMAND_LINE_OPTION = '--help';
    const OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION = '-j';
    const OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION = '--json';

    const RESULT_ID_ARGUMENT_INDEX = 1;

    const RESULT_ID_ARGUMENT = 'result_id';

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
        echo '  List a user result from database by internal id.' . PHP_EOL . PHP_EOL;
        echo 'Usage:' . PHP_EOL;
        echo '  ' . basename(__FILE__) . ' [' . ListResultScriptController::RESULT_ID_ARGUMENT . '] [options]' . PHP_EOL
            . PHP_EOL;
        echo 'Options:' . PHP_EOL;
        echo '  ' . ListResultScriptController::HELP_SHORT_COMMAND_LINE_OPTION . ', '
            . ListResultScriptController::HELP_LONG_COMMAND_LINE_OPTION . ' Display this help message.' . PHP_EOL;
        echo '  ' . ListResultScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION . ', '
            . ListResultScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION
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
        $resultID = $commandLineArguments[ListResultScriptController::RESULT_ID_ARGUMENT_INDEX];

        if (empty($resultID))
            return 'Invalid [' . ListResultScriptController::RESULT_ID_ARGUMENT . '] argument';
        else if (!is_numeric($resultID))
            return 'Invalid [' . ListResultScriptController::RESULT_ID_ARGUMENT
                . '] argument. This argument must be an integer.';

        return $errorMessage;
    }

    /**
     * @return Result
     */
    private function execute()
    {
        $commandLineArguments = $this->commandLineArguments;
        $resultID = $commandLineArguments[ListResultScriptController::RESULT_ID_ARGUMENT_INDEX];

        $this->setEntityRepository(Result::CLASS_NAME);

        /**
         * @var Result $result;
         */
        $result = $this->entityRepository->findOneBy(array(Result::ID_ATTRIBUTE => $resultID));

        if (empty($result)) {
            echo 'Result with "' . $resultID . '" internal ID does not exist';
            exit;
        }

        return $result;
    }

    /**
     * @param Result $result
     */
    private function output($result)
    {
        $commandLineArguments = $this->commandLineArguments;

        if (in_array(ListResultScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
            || in_array(ListResultScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
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
        if (($this->numberOfCommandLineArguments < 2)
            || in_array(ListResultScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $this->commandLineArguments)
            || in_array(ListResultScriptController::HELP_LONG_COMMAND_LINE_OPTION, $this->commandLineArguments)
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