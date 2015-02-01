<?php
/**
 * Created by PhpStorm.
 * User: jeltesteijaert
 * Date: 31/01/15
 * Time: 19:20
 */

namespace Automaton\Resources\Recipes\Symfony;

use Automaton\Recipe\Annotation as Automaton;
use Automaton\RuntimeEnvironment;
use Automaton\Server\ServerInterface;

class Assets
{
    /**
     * @Automaton\Task(description="install bundle assets")
     * @Automaton\Before(task="environment:createSymlink")
     */
    public function install(RuntimeEnvironment $env, ServerInterface $server)
    {
        $server->run("php {$server->cwd('releases/'. $env->get('release').'/app/console')} --env={$env->get('symfony.stage')} -v assets:install --symlink --relative {$server->cwd('releases/'. $env->get('release').'/web')}");
    }
}