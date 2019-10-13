<?php

namespace Junges\ACL\Solutions\Providers;

use Throwable;
use Illuminate\Support\Facades\Schema;
use Facade\IgnitionContracts\BaseSolution;
use Junges\ACL\Solutions\ACLNotInstalledSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class NotInstalledSolutionProvider implements HasSolutionsForThrowable
{
    /**
     * Can the exception be solved.
     * 
     * @param \Throwable $throwable
     * @return bool
     */
    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable->getMessage() === 'Class name must be a valid object or a string') {
            return false;
        }

        return !Schema::hasTable(config('acl.tables.permissions', 'permissions'));
    }

    /**
     * The solutions for the missing traits.
     * 
     * @param \Throwable $throwable
     * @return array
     */
    public function getSolutions(Throwable $throwable): array
    {
        return [
            new ACLNotInstalledSolution(),
            BaseSolution::create('You haven\'t installed laravel-acl fully')
                ->setSolutionDescription('You need to run `php artisan acl:install` and `php artisan migrate` to complete the install')
                ->setDocumentationLinks([
                    'Installation Docs' => 'https://mateusjunges.github.io/laravel-acl/guide/getting-started.html#install-using-acl-install-command',
                ]),
        ];
    }
}
