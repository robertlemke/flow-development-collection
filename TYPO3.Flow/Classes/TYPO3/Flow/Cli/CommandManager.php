<?php
namespace TYPO3\Flow\Cli;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A helper for CLI Commands
 *
 * @Flow\Scope("singleton")
 */
class CommandManager
{
    /**
     * @var array<Command>
     */
    protected $availableCommands = null;

    /**
     * @var array
     */
    protected $shortCommandIdentifiers = null;

    /**
     * @var \TYPO3\Flow\Reflection\ReflectionService
     */
    protected $reflectionService;

    /**
     * @var \TYPO3\Flow\Core\Bootstrap
     */
    protected $bootstrap;

    /**
     * @param \TYPO3\Flow\Reflection\ReflectionService $reflectionService
     * @return void
     */
    public function injectReflectionService(\TYPO3\Flow\Reflection\ReflectionService $reflectionService)
    {
        $this->reflectionService = $reflectionService;
    }

    /**
     * @param \TYPO3\Flow\Core\Bootstrap $bootstrap
     * @return void
     */
    public function injectBootstrap(\TYPO3\Flow\Core\Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    /**
     * Returns an array of all commands
     *
     * @return array<Command>
     * @api
     */
    public function getAvailableCommands()
    {
        if ($this->availableCommands === null) {
            $this->availableCommands = array();

            $commandControllerClassNames = $this->reflectionService->getAllSubClassNamesForClass(\TYPO3\Flow\Cli\CommandController::class);
            foreach ($commandControllerClassNames as $className) {
                if (!class_exists($className)) {
                    continue;
                }
                foreach (get_class_methods($className) as $methodName) {
                    if (substr($methodName, -7, 7) === 'Command') {
                        $this->availableCommands[] = new Command($className, substr($methodName, 0, -7));
                    }
                }
            }
        }
        return $this->availableCommands;
    }

    /**
     * Returns a Command that matches the given identifier.
     * If no Command could be found a CommandNotFoundException is thrown
     * If more than one Command matches an AmbiguousCommandIdentifierException is thrown that contains the matched Commands
     *
     * @param string $commandIdentifier command identifier in the format foo:bar:baz
     * @return \TYPO3\Flow\Cli\Command
     * @throws \TYPO3\Flow\Mvc\Exception\NoSuchCommandException if no matching command is available
     * @throws \TYPO3\Flow\Mvc\Exception\AmbiguousCommandIdentifierException if more than one Command matches the identifier (the exception contains the matched commands)
     * @api
     */
    public function getCommandByIdentifier($commandIdentifier)
    {
        $commandIdentifier = strtolower(trim($commandIdentifier));
        if ($commandIdentifier === 'help') {
            $commandIdentifier = 'typo3.flow:help:help';
        }
        if ($commandIdentifier === 'sys') {
            $commandIdentifier = 'typo3.flow:cache:sys';
        }

        $matchedCommands = $this->getCommandsByIdentifier($commandIdentifier);
        if (count($matchedCommands) === 0) {
            throw new \TYPO3\Flow\Mvc\Exception\NoSuchCommandException('No command could be found that matches the command identifier "' . $commandIdentifier . '".', 1310556663);
        }
        if (count($matchedCommands) > 1) {
            throw new \TYPO3\Flow\Mvc\Exception\AmbiguousCommandIdentifierException('More than one command matches the command identifier "' . $commandIdentifier . '"', 1310557169, null, $matchedCommands);
        }
        return current($matchedCommands);
    }

    /**
     * Returns an array of Commands that matches the given identifier.
     * If no Command could be found, an empty array is returned
     *
     * @param string $commandIdentifier command identifier in the format foo:bar:baz
     * @return array<\TYPO3\Flow\Mvc\Cli\Command>
     * @api
     */
    public function getCommandsByIdentifier($commandIdentifier)
    {
        $availableCommands = $this->getAvailableCommands();
        $matchedCommands = array();
        foreach ($availableCommands as $command) {
            if ($this->commandMatchesIdentifier($command, $commandIdentifier)) {
                $matchedCommands[] = $command;
            }
        }
        return $matchedCommands;
    }

    /**
     * Returns the shortest, non-ambiguous command identifier for the given command
     *
     * @param Command $command The command
     * @return string The shortest possible command identifier
     * @api
     */
    public function getShortestIdentifierForCommand(Command $command)
    {
        if ($command->getCommandIdentifier() === 'typo3.flow:help:help') {
            return 'help';
        }
        $shortCommandIdentifiers = $this->getShortCommandIdentifiers();
        if (!isset($shortCommandIdentifiers[$command->getCommandIdentifier()])) {
            return $command->getCommandIdentifier();
        }
        return $shortCommandIdentifiers[$command->getCommandIdentifier()];
    }

    /**
     * Returns an array that contains all available command identifiers and their shortest non-ambiguous alias
     *
     * @return array in the format array('full.command:identifier1' => 'alias1', 'full.command:identifier2' => 'alias2')
     */
    protected function getShortCommandIdentifiers()
    {
        if ($this->shortCommandIdentifiers === null) {
            $commandsByCommandName = array();
            foreach ($this->getAvailableCommands() as $availableCommand) {
                list($packageKey, $controllerName, $commandName) = explode(':', $availableCommand->getCommandIdentifier());
                if (!isset($commandsByCommandName[$commandName])) {
                    $commandsByCommandName[$commandName] = array();
                }
                if (!isset($commandsByCommandName[$commandName][$controllerName])) {
                    $commandsByCommandName[$commandName][$controllerName] = array();
                }
                $commandsByCommandName[$commandName][$controllerName][] = $packageKey;
            }
            foreach ($this->getAvailableCommands() as $availableCommand) {
                list($packageKey, $controllerName, $commandName) = explode(':', $availableCommand->getCommandIdentifier());
                if (count($commandsByCommandName[$commandName][$controllerName]) > 1 || $this->bootstrap->isCompiletimeCommand($availableCommand->getCommandIdentifier())) {
                    $packageKeyParts = array_reverse(explode('.', $packageKey));
                    for ($i = 1; $i <= count($packageKeyParts); $i++) {
                        $shortCommandIdentifier = implode('.', array_slice($packageKeyParts, 0, $i)) .  ':' . $controllerName . ':' . $commandName;
                        try {
                            $this->getCommandByIdentifier($shortCommandIdentifier);
                            $this->shortCommandIdentifiers[$availableCommand->getCommandIdentifier()] = $shortCommandIdentifier;
                            break;
                        } catch (\TYPO3\Flow\Mvc\Exception\CommandException $exception) {
                        }
                    }
                } else {
                    $this->shortCommandIdentifiers[$availableCommand->getCommandIdentifier()] = sprintf('%s:%s', $controllerName, $commandName);
                }
            }
        }
        return $this->shortCommandIdentifiers;
    }

    /**
     * Returns TRUE if the specified command identifier matches the identifier of the specified command.
     * This is the case, if
     *  - the identifiers are the same
     *  - if at least the last two command parts match (case sensitive) or
     *  - if only the package key is specified and matches the commands package key
     * The first part (package key) can be reduced to the last subpackage, as long as the result is unambiguous.
     *
     * @param Command $command
     * @param string $commandIdentifier command identifier in the format foo:bar:baz (all lower case)
     * @return boolean TRUE if the specified command identifier matches this commands identifier
     */
    protected function commandMatchesIdentifier(Command $command, $commandIdentifier)
    {
        $commandIdentifierParts = explode(':', $command->getCommandIdentifier());
        $searchedCommandIdentifierParts = explode(':', $commandIdentifier);
        $packageKey = array_shift($commandIdentifierParts);
        $searchedCommandIdentifierPartsCount = count($searchedCommandIdentifierParts);
        if ($searchedCommandIdentifierPartsCount === 3 || $searchedCommandIdentifierPartsCount === 1) {
            $searchedPackageKey = array_shift($searchedCommandIdentifierParts);
            if ($searchedPackageKey !== $packageKey
                    && substr($packageKey, - (strlen($searchedPackageKey) + 1)) !== '.' . $searchedPackageKey) {
                return false;
            }
        }
        if ($searchedCommandIdentifierPartsCount === 1) {
            return true;
        } elseif (count($searchedCommandIdentifierParts) !== 2) {
            return false;
        }
        return $searchedCommandIdentifierParts === $commandIdentifierParts;
    }
}
