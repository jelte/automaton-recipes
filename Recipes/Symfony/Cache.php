<?php

namespace Automaton\Resources\Recipes\Symfony;

use Automaton\Recipe\Annotation as Automaton;
use Automaton\RuntimeEnvironment;
use Automaton\Server\ServerInterface;

class Cache
{

    /**
     * @Automaton\Task(description="Warm up Symfony cache")
     * @Automaton\Before(task="environment:createSymlink")
     */
    public function warmup(RuntimeEnvironment $env, ServerInterface $server)
    {
        $server->run("php {$server->cwd('releases/'. $env->get('release').'/app/console')} --env={$env->get('symfony.stage')} -v cache:warmup");
    }

    /**
     * @Automaton\Task(description="Build Symfony bootstrap cache")
     * @Automaton\Before(task="symfony:cache:warmup", priority=99)
     */
    public function build_bootstrap(RuntimeEnvironment $env, ServerInterface $server)
    {
        $server->run("php {$server->cwd('releases/'. $env->get('release').'/vendor/sensio/distribution-bundle/Sensio/Bundle/DistributionBundle/Resources/bin/build_bootstrap.php')}");
    }

}
