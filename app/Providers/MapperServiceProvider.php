<?php

namespace App\Providers;

use App\Import\Mapper\AttributeMapper;
use App\Import\Mapper\CategoryMapper;
use App\Import\Mapper\PriceMapper;
use App\Import\Mapper\Rule\RuleMapper;
use App\ParseRules\ParserExpressionLanguageProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class MapperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CategoryMapper::class, function (Application $application, $argument) {
            return new CategoryMapper($argument[0], $argument[1]);
        });

        $this->app->singleton(AttributeMapper::class, function (Application $application, $argument) {
            return new AttributeMapper($argument[0], $argument[1]);
        });

        $this->app->singleton(RuleMapper::class, function (Application $application, $args) {
            $expressionLanguage = new ExpressionLanguage(null, [new ParserExpressionLanguageProvider()]);

            return new RuleMapper($expressionLanguage, ...$args);
        });

        $this->app->singleton(PriceMapper::class, function (Application $application, $args) {
            return new PriceMapper();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
