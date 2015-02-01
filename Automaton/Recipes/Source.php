<?php


namespace Automaton\Recipes;


use Automaton\RuntimeEnvironment;
use Automaton\Server\ServerInterface;
use Automaton\System\FilesystemInterface;
use Automaton\System\SystemInterface;

use Automaton\Recipe\Annotation as Automaton;

class Source
{
    /**
     * @param RuntimeEnvironment $env
     * @param FilesystemInterface $filesystem
     *
     * @Automaton\Task
     * @Automaton\After(task="deploy", priority=10)
     */
    public function prepare(RuntimeEnvironment $env, FilesystemInterface $filesystem)
    {
        $source = $env->get('repository.local_path');
        $release = date('YmdHis');
        $env->set('release', $release);
        $target = $source . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $release;
        $env->set('release.path', "releases/{$release}");
        $env->set('deploy.local_path', $target);
        if ($filesystem->exists($target)) {
            $filesystem->remove($target);
        }
        $filesystem->mirror($source, $target);
        $excludes = array_map(function ($value) use ($target) {
            return $target . DIRECTORY_SEPARATOR . $value;
        }, $env->get('excludes', array()));

        $filesystem->remove($excludes);
    }

    /**
     * @param RuntimeEnvironment $env
     * @param SystemInterface $system
     *
     * @Automaton\Task
     * @Automaton\After(task="source:prepare")
     */
    public function archive(RuntimeEnvironment $env, SystemInterface $system)
    {
        $release = $env->get('release');
        $path = realpath($env->get('repository.local_path') . DIRECTORY_SEPARATOR . '..');
        $archive = "{$release}.tar.gz";
        $system->run("cd {$path} && tar czf {$archive} {$release}");
        $env->set('release.archive', $archive);
    }

    /**
     * @param RuntimeEnvironment $env
     * @param ServerInterface $server
     *
     * @Automaton\Task
     * @Automaton\After(task="source:archive")
     */
    public function upload(RuntimeEnvironment $env, ServerInterface $server)
    {
        $archive = $env->get('release.archive');
        $target = "/tmp/{$archive}";
        $local = realpath($env->get('repository.local_path') . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $archive);
        $server->upload($local, $target);
    }

    /**
     * @param RuntimeEnvironment $env
     * @param ServerInterface $server
     *
     * @Automaton\Task
     * @Automaton\After(task="source:upload")
     */
    public function extract(RuntimeEnvironment $env, ServerInterface $server)
    {
        $archive = "/tmp/{$env->get('release.archive')}";
        $env->set('archive-path', $archive);
        $target = $server->cwd('releases');

        $server->run("cd {$target} && tar xzf {$archive} 1>archive.stdout.log 2>archive.stderr.log && rm archive.stdout.log && rm archive.stderr.log");
    }

    /**
     * @param RuntimeEnvironment $env
     * @param ServerInterface $server
     *
     * @Automaton\Task
     * @Automaton\After(task="source:extract")
     */
    public function cleanup(RuntimeEnvironment $env, ServerInterface $server)
    {
        $server->run("[ -f {$env->get('archive-path')} ] && rm {$env->get('archive-path')}");
    }
}
