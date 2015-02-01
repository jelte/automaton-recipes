<?php


namespace Automaton\Recipes;


use Automaton\RuntimeEnvironment;
use Automaton\Stage\StageInterface;
use Automaton\System\FilesystemInterface;
use Automaton\System\SystemInterface;
use Automaton\Recipe\Annotation as Automaton;


class Repository
{
    /**
     * @param RuntimeEnvironment $env
     * @param SystemInterface $system
     * @param FilesystemInterface $filesystem
     * @param StageInterface $stage
     *
     * @Automaton\Task
     * @Automaton\After(task="deploy")
     */
    public function update(RuntimeEnvironment $env, SystemInterface $system, FilesystemInterface $filesystem, StageInterface $stage)
    {
        $branch = $stage->get('branch', $env->get('branch'));
        $repository = $env->get('repository');

        $hash = explode("\t",$system->run("git ls-remote {$repository} {$branch}"));
        $path = $system->getTempDir().DIRECTORY_SEPARATOR.sha1($repository);

        if ( $filesystem->exists($path) ) {
            $filesystem->mkdir($path);
            $system->run("cd {$path} && git fetch -q origin && git fetch --tags -q origin && git reset -q --hard ${hash[0]} && git clean -q -d -x -f");
        } else {
            $system->run("git clone -q -b ${branch} ${repository} ${path} && cd ${path} && git checkout -q -b deploy ${hash[0]}");
        }
        $env->set('repository.local_path', $path);
    }
}
