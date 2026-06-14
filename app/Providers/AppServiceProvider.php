<?php

namespace App\Providers;

use App\Services\GitHubIssueReporter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GitHubIssueReporter::class, fn() => new GitHubIssueReporter());
        $this->app->alias(GitHubIssueReporter::class, 'gitHubReporter');
    }

    public function boot(): void
    {
        //
    }
}
