<?php


namespace Automaton\Recipes\Composer;


use Automaton\RuntimeEnvironment;
use Automaton\Server\ServerInterface;
use Automaton\Recipe\Annotation as Automaton;
use Automaton\System\SystemInterface;

/**
 * Class Composer
 * @package Automaton\Resources\Recipes
 */
class Local
{
    /**
     * @Automaton\Task
     * @Automaton\Before(task="composer:local:install")
     */
    public function download(RuntimeEnvironment $env, SystemInterface $system)
    {
        $system->run("cd {$env->get('repository.local_path')} && curl -s http://getcomposer.org/installer | php");
    }

    /**
     * @Automaton\Task
     * @Automaton\Before(task="source:prepare")
     */
    public function install(RuntimeEnvironment $env, SystemInterface $system)
    {
        $system->run("cd {$env->get('repository.local_path')} && php composer.phar install --no-scripts --no-dev --verbose --prefer-dist --optimize-autoloader");
    }

    /**
     * @Automaton\Task
     * @Automaton\After(task="composer:local:install")
     */
    public function cleanup(RuntimeEnvironment $env, SystemInterface $system)
    {
        $system->run("cd {$env->get('repository.local_path')} && rm -rf composer.*");
    }
}
