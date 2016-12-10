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
use MiW16\Results\Entity\User;
use Doctrine\ORM\EntityManager;

require_once __DIR__ . '/../../../bootstrap.php';

$scriptController = new ListResultsScriptController($argc, $argv);
$scriptController->main();

class ListResultsScriptController
{
    const HELP_SHORT_COMMAND_LINE_OPTION = '-h';
    const HELP_LONG_COMMAND_LINE_OPTION = '--help';
    const OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION = '-j';
    const OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION = '--json';
    const USER_TOKEN_SHORT_COMMAND_LINE_OPTION = '-t';
    const USER_TOKEN_LONG_COMMAND_LINE_OPTION = '--token';

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
        echo '  List all user results stored in database. Results can be filtered by an internal user ID or user token'
            . PHP_EOL . PHP_EOL;
        echo 'Usage:' . PHP_EOL;
        echo '  ' . basename(__FILE__) . ' [' . ListResultsScriptController::FILTER_ARGUMENT . '] [options]' . PHP_EOL
            . PHP_EOL;
        echo 'Options:' . PHP_EOL;
        echo '  ' . ListResultsScriptController::HELP_SHORT_COMMAND_LINE_OPTION . ', '
            . ListResultsScriptController::HELP_LONG_COMMAND_LINE_OPTION . ' Display this help message.' . PHP_EOL;
        echo '  ' . ListResultsScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION . ', '
            . ListResultsScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION
            . ' Display script output in JSON format.' . PHP_EOL;
        echo '  ' . ListResultsScriptController::USER_TOKEN_SHORT_COMMAND_LINE_OPTION . ', '
            . ListResultsScriptController::USER_TOKEN_LONG_COMMAND_LINE_OPTION . ' List results by user token.'
            . PHP_EOL;

        exit;
    }

    /**
     *
     */
    /**
     * @return null|string
     */
    private function checkArgumentValues()
    {
        $errorMessage = null;
        $commandLineArguments = $this->commandLineArguments;

        if (count($commandLineArguments) > 1) {
            $filterArgument = $commandLineArguments[ListResultsScriptController::FILTER_ARGUMENT_INDEX];

            if (!empty($filterArgument)
                && (ListResultsScriptController::HELP_SHORT_COMMAND_LINE_OPTION !== $filterArgument)
                && (ListResultsScriptController::HELP_LONG_COMMAND_LINE_OPTION !== $filterArgument)
                && (ListResultsScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION !== $filterArgument)
                && (ListResultsScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION !== $filterArgument)
                && (ListResultsScriptController::USER_TOKEN_SHORT_COMMAND_LINE_OPTION !== $filterArgument)
                && (ListResultsScriptController::USER_TOKEN_LONG_COMMAND_LINE_OPTION !== $filterArgument)
                && !in_array(ListResultsScriptController::USER_TOKEN_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
                && !in_array(ListResultsScriptController::USER_TOKEN_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
                && !is_numeric($filterArgument)
            ) {
                return 'Invalid [' . ListResultsScriptController::FILTER_ARGUMENT
                    . '] argument. The argument must be an integer';
            }
        }

        return $errorMessage;
    }

    /**
     * @return array
     */
    private function execute()
    {
        $errorMessage = null;
        $commandLineArguments = $this->commandLineArguments;

        if (count($commandLineArguments) > 1) {
            $filterArgument = $commandLineArguments[ListResultsScriptController::FILTER_ARGUMENT_INDEX];

            if (!empty($filterArgument)
                && (ListResultsScriptController::HELP_SHORT_COMMAND_LINE_OPTION !== $filterArgument)
                && (ListResultsScriptController::HELP_LONG_COMMAND_LINE_OPTION !== $filterArgument)
                && (ListResultsScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION !== $filterArgument)
                && (ListResultsScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION !== $filterArgument)
                && (ListResultsScriptController::USER_TOKEN_SHORT_COMMAND_LINE_OPTION !== $filterArgument)
                && (ListResultsScriptController::USER_TOKEN_LONG_COMMAND_LINE_OPTION !== $filterArgument)
            ) {
                $this->setEntityRepository(User::CLASS_NAME);

                if (in_array(ListResultsScriptController::USER_TOKEN_SHORT_COMMAND_LINE_OPTION, $commandLineArguments)
                    || in_array(ListResultsScriptController::USER_TOKEN_LONG_COMMAND_LINE_OPTION, $commandLineArguments)
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
            }
        }

        $this->setEntityRepository(Result::CLASS_NAME);

        if (isset($user))
            $results = $this->entityRepository->findBy(array(Result::USER_ATTRIBUTE => $user));
        else
            $results = $this->entityRepository->findAll();

        return $results;
    }

    /**
     * @param array $results
     */
    private function output($results)
    {
        $commandLineArguments = $this->commandLineArguments;
        $outputInJSONFormat = in_array(ListResultsScriptController::OUTPUT_IN_JSON_SHORT_COMMAND_LINE_OPTION,
                $commandLineArguments) || in_array(ListResultsScriptController::OUTPUT_IN_JSON_LONG_COMMAND_LINE_OPTION,
                $commandLineArguments);

        echo 'Number of results: ' . count($results) . PHP_EOL . PHP_EOL;

        if (count($results) > 0) {
            if ($outputInJSONFormat) {
                $jsonOutput = '';

                foreach ($results as $result) {
                    if (empty($jsonOutput))
                        $jsonOutput .= '[' . json_encode($result->jsonSerialize());
                    else
                        $jsonOutput .= ', ' . json_encode($result->jsonSerialize());
                }

                echo $jsonOutput . ']';
            } else
                echo implode(',' . PHP_EOL, $results);
        }
    }

    /**
     *
     */
    public function main()
    {
        if (in_array(ListResultsScriptController::HELP_SHORT_COMMAND_LINE_OPTION, $this->commandLineArguments)
            || in_array(ListResultsScriptController::HELP_LONG_COMMAND_LINE_OPTION, $this->commandLineArguments)
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