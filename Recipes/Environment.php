<?php


namespace Automaton\Resources\Recipes;


use Automaton\RuntimeEnvironment;
use Automaton\Server\ServerInterface;
use Automaton\Stage\StageInterface;
use Automaton\Recipe\Annotation as Automaton;

class Environment
{
    /**
     * @param ServerInterface $server
     *
     * @Automaton\Task
     * @Automaton\Before(task="deploy")
     */
    public function init(ServerInterface $server)
    {
        $server->run("mkdir -p {$server->cwd('releases')}");
        $server->run("mkdir -p {$server->cwd('shared')}");
    }


    /**
     * @param RuntimeEnvironment $env
     * @param ServerInterface $server
     *
     * @Automaton\Task
     * @Automaton\After(task="deploy", priority=999)
     */
    public function createSymlink(RuntimeEnvironment $env, ServerInterface $server)
    {
        $current = $server->cwd('release');
        $release = $server->cwd("releases/{$env->get('release')}");
        $server->run("rm -f {$current} && ln -s {$release} {$current}");
    }

    /**
     * @param ServerInterface $server
     * @param StageInterface $stage
     *
     * @Automaton\Task
     * @Automaton\After(task="environment:createSymlink")
     */
    public function cleanup(ServerInterface $server, StageInterface $stage)
    {
        $releases = explode("\n", $server->run("ls -t1 --color=none {$server->cwd('releases')}")) ;

        $keep = $stage->get('keep_releases', 3);

        while ($keep > 0) {
            array_shift($releases);
            --$keep;
        }

        foreach ($releases as $release) {
            if ( !empty($release) ) {
                $server->run("rm -rf {$server->cwd('releases')}/{$release}");
            }
        }
    }
}
