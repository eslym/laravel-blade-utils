<?php

namespace Eslym\BladeUtils\Providers;

use Eslym\BladeUtils\Facades\BladeUtils;
use Eslym\BladeUtils\Tools\BladeUtils as BladeUtilsImpl;
use Illuminate\Support\Str;
use Illuminate\View\Factory;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeUtilServiceProvider extends ServiceProvider
{
    public function boot(){
        $this->app->singleton(BladeUtilsImpl::class, function(){
            return new BladeUtilsImpl();
        });
        $this->app->alias(BladeUtilsImpl::class, 'blade-utils');

        Factory::macro('renderEachWithVars', function($vars, $view, $data, $iterator, $empty = 'raw|'){
            /** @var $self Factory */
            $self = $this;
            $result = '';
            if (count($data) > 0) {
                foreach ($data as $key => $value) {
                    $result .= $self->make(
                        $view, $vars, ['key' => $key, $iterator => $value]
                    )->render();
                }
            } else {
                $result = Str::startsWith($empty, 'raw|')
                    ? substr($empty, 4)
                    : $self->make($empty, $vars)->render();
            }
            return $result;
        });

        Blade::directive('json', BladeUtils::class.'::compileJson');

        Blade::directive('each', function ($expression){
            return '<?php echo $__env->renderEachWithVars(get_defined_vars(), '.$expression.') ?>';
        });

        Blade::directive('json', BladeUtils::class.'::compileJson');

        Blade::directive('css', BladeUtils::class.'::compileCss');

        Blade::directive('js', BladeUtils::class.'::compileJs');

        Blade::directive('img', BladeUtils::class.'::compileImg');

        Blade::directive('meta', function ($expression){
            return "<?php echo \\".BladeUtils::class."::buildMeta($expression); ?>";
        });

        Blade::directive('nameMeta', function ($expression){
            return "<?php echo \\".BladeUtils::class."::buildNameMeta($expression); ?>";
        });

        Blade::directive('propMeta', function ($expression){
            return "<?php echo \\".BladeUtils::class."::buildPropMeta($expression); ?>";
        });

        Blade::directive('itemMeta', function ($expression){
            return "<?php echo \\".BladeUtils::class."::buildItemMeta($expression); ?>";
        });
    }
}