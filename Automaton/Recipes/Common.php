<?php


namespace Automaton\Recipes;

use Automaton\Recipe\Annotation as Automaton;
use Symfony\Component\Console\Output\OutputInterface;

class Common
{

    /**
     * @Automaton\Task(description="Deploy your application")
     * @Automaton\Alias(name="deploy")
     */
    public function deploy()
    {

    }

    /**
     * @Automaton\Task(description="Rollback your application to a previous version")
     * @Automaton\Alias(name="rollback")
     */
    public function rollback()
    {

    }

    /**
     * @param OutputInterface $output
     *
     * @Automaton\Task(progress=false)
     * @Automaton\After(task="deploy", priority=9999)
     */
    public function finishDeploy(OutputInterface $output)
    {
        $output->writeln("<info>Automaton successfully deployed your application</info>");
    }
}
