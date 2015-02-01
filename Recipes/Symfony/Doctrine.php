<?php
/**
 * Created by PhpStorm.
 * User: jeltesteijaert
 * Date: 31/01/15
 * Time: 19:30
 */

namespace Automaton\Resources\Recipes\Symfony;

use Automaton\Recipe\Annotation as Automaton;
use Automaton\RuntimeEnvironment;
use Automaton\Server\ServerInterface;

class Doctrine {

    /**
     * @Automaton\Task(description="Update database schema")
     * @Automaton\Alias(name="doctrine:schema:update")
     */
    public function update(RuntimeEnvironment $env, ServerInterface $server)
    {
        $server->run("php {$server->cwd('releases/'. $env->get('release/app/console'))} doctrine:schema:update --env={$env->get('symfony.stage')} --force");
    }
}