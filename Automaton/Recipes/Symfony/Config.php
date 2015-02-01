<?php
/**
 * Created by PhpStorm.
 * User: jeltesteijaert
 * Date: 01/02/15
 * Time: 15:01
 */

namespace Automaton\Recipes\Symfony;


use Automaton\Recipe\Annotation as Automaton;
use Automaton\RuntimeEnvironment;
use Automaton\Server\ServerInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Config {
    /**
     * @Automaton\Task(description="Config symfony parameters")
     * @Automaton\After(task="symfony:cache:build_bootstrap")
     */
    public function install(RuntimeEnvironment $env, ServerInterface $server, OutputInterface $output, HelperSet $helperSet)
    {
        $server->runInteractively(
            "cd {$server->cwd('releases/'. $env->get('release'))} && php app/console --env={$env->get('symfony.stage')} -v install:parameters",
            '.* \(.*\): ', 'Finished configuring paramaters\.',
            $output, $helperSet
        );
    }
}