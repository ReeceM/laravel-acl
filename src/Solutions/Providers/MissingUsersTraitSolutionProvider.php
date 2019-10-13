<?php

namespace Junges\ACL\Solutions\Providers;

use Throwable;
use Facade\IgnitionContracts\BaseSolution;
use ReflectionClass;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Junges\ACL\Solutions\AddMissingUsersTraitSolution;
use Junges\ACL\Traits\UsersTrait;

class MissingUsersTraitSolutionProvider implements HasSolutionsForThrowable
{
    /**
     * The class method is called on
     * 
     * @var string $class
     */
    private $class;

    /**
     * Can the exception be solved
     * 
     * @param \Throwable $throwable
     * @return bool
     */
    public function canSolve(Throwable $throwable): bool
    {
        $pattern = '/Call to undefined method ([^\s]+)/m';

        if (! preg_match($pattern, $throwable->getMessage(), $matches)) {
            return false;
        }
        $class = $matches[1];
        
        $this->class = $class;
        $method = explode("::", $class) ?? [];
        $method = explode(' ', end($method))[0] ?? '';
        $method = str_replace('()', '', $method);
        return (new ReflectionClass(UsersTrait::class))->hasMethod($method);
    }

    /**
     * The solutions for the missing traits
     * 
     * @param \Throwable $throwable
     * @return array
     */
    public function getSolutions(Throwable $throwable): array
    {
        $model = explode('::', $this->class)[0];
        
        return [
            new AddMissingUsersTraitSolution($this->class),
            BaseSolution::create('The UsersTrait is missing.')
                ->setSolutionDescription("You have to add the `UsersTrait` trait to your `{$model}` model to be able to access the acl methods")
                ->setDocumentationLinks([
                    'Usage' => 'https://mateusjunges.github.io/laravel-acl/guide/usage.html#usage',
                ]),
        ];
    }
}