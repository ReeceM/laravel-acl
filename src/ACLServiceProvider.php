<?php

namespace Junges\ACL;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Junges\ACL\Console\Commands\CreateGroup;
use Junges\ACL\Console\Commands\InstallCommand;
use Junges\ACL\Console\Commands\ShowPermissions;
use Junges\ACL\Console\Commands\UserPermissions;
use Junges\ACL\Console\Commands\CreatePermission;
use Facade\IgnitionContracts\SolutionProviderRepository;

class ACLServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param Dispatcher $events
     * @param Repository $config
     * @param Factory    $view
     *
     * @return void
     */
    public function boot(Dispatcher $events, Repository $config, Factory $view)
    {

        //Publishes migrations:
        $this->loadMigrations();

        //Publishes config
        $this->publishConfig();

        //Publishes views
        $this->loadViews();

        //Load commands
        $this->loadCommands();

        //Load translations
        $this->loadTranslations();

        //load solution providers
        $this->registerSolutionProviders();
    }

    /**
     * Load and publishes the views folder.
     */
    public function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'acl');
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/junges/acl'),
        ], 'acl-views');
    }

    /**
     * Load and publishes the configuration file.
     */
    public function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/acl.php' => config_path('acl.php'),
        ], 'acl-config');
    }

    /**
     * Register the package's commands.
     */
    public function loadCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreatePermission::class,
                ShowPermissions::class,
                CreateGroup::class,
                UserPermissions::class,
                InstallCommand::class,
            ]);
        }
    }

    /**
     * Register the package's migrations.
     */
    public function loadMigrations()
    {
        $customMigrations = config('acl.custom_migrations');
        if ($customMigrations) {
            $this->loadMigrationsFrom(database_path('migrations/vendor/junges/acl'));
        } else {
            $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        }
        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations/vendor/junges/acl'),
        ], 'acl-migrations');
    }

    /**
     * Register the package's migrations.
     */
    public function loadTranslations()
    {
        $translationsPath = __DIR__.'/resources/lang';
        $this->loadTranslationsFrom($translationsPath, 'acl');
        $this->publishes([
            $translationsPath => base_path('resources/lang/vendor/acl'),
        ], 'acl-translations');
    }

    /**
     * Register the solution providers for package.
     */
    public function registerSolutionProviders(): void
    {
        if (! $this->app->runningUnitTests()) {
            $this->app->make(SolutionProviderRepository::class)->registerSolutionProviders([
                \Junges\ACL\Solutions\Providers\MissingUsersTraitSolutionProvider::class,
                \Junges\ACL\Solutions\Providers\MissingGroupsTraitSolutionProvider::class,
                \Junges\ACL\Solutions\Providers\MissingPermissionsTraitSolutionProvider::class,
                \Junges\ACL\Solutions\Providers\MissingACLWildcardsTraitSolutionProvider::class,
                \Junges\ACL\Solutions\Providers\NotInstalledSolutionProvider::class,
                \Junges\ACL\Solutions\Providers\GroupDoesNotExistSolutionProvider::class,
                \Junges\ACL\Solutions\Providers\PermissionDoesNotExistSolutionProvider::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
