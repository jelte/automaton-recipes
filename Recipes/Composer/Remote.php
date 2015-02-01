<?php


namespace Automaton\Resources\Recipes\Composer;


use Automaton\RuntimeEnvironment;
use Automaton\Server\ServerInterface;
use Automaton\Recipe\Annotation as Automaton;

/**
 * Class Composer
 * @package Automaton\Resources\Recipes
 */
class Composer
{
    /**
     * @param ServerInterface $server
     *
     * @Automaton\Task
     * @Automaton\Before(task="composer:remote:install")
     */
    public function download(RuntimeEnvironment $env, ServerInterface $server)
    {
        $server->run("cd {$server->cwd('releases/'. $env->get('release'))} && curl -s http://getcomposer.org/installer | php");
    }

    /**
     * @param ServerInterface $server
     *
     * @Automaton\Task
     * @Automaton\After(task="deploy", priority=20)
     */
    public function install(RuntimeEnvironment $env, ServerInterface $server)
    {
        $server->run("cd {$server->cwd('releases/'. $env->get('release'))} && php composer.phar install --no-scripts --no-dev --verbose --prefer-dist --optimize-autoloader --no-progress > composer.log");
    }

    /**
     * @param ServerInterface $server
     *
     * @Automaton\Task
     * @Automaton\Before(task="composer:remote:install")
     */
    public function copyPreviousVendors(RuntimeEnvironment $env, ServerInterface $server)
    {
        $server->run("cd {$server->cwd('releases/'. $env->get('release'))} && if test -d {$server->cwd('release/vendor')}; then cp -R {$server->cwd('release/vendor')} . ; fi");
    }

    /**
     * @param ServerInterface $server
     *
     * @Automaton\Task
     * @Automaton\After(task="composer:remote:install")
     */
    public function cleanup(RuntimeEnvironment $env, ServerInterface $server)
    {
        $server->run("cd {$server->cwd('releases/'. $env->get('release'))} && rm -rf composer.*");
    }
}
