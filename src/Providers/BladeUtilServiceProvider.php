<?php

namespace Eslym\BladeUtils\Providers;

use Eslym\BladeUtils\Facades\BladeUtils;
use Eslym\BladeUtils\Tools\BladeUtils as BladeUtilsImpl;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Factory;
use Illuminate\Support\ServiceProvider;

class BladeUtilServiceProvider extends ServiceProvider
{
    public function boot(){
        $this->app->singleton(BladeUtilsImpl::class, function(){
            return new BladeUtilsImpl();
        });
        $this->app->alias(BladeUtilsImpl::class, 'blade-utils');

        $this->app->afterResolving('view', function (Factory $factory){
            $factory->macro('renderEachWithVars', function($vars, $view, $data, $iterator, $empty = 'raw|'){
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
        });

        $this->app->afterResolving('blade.compiler', function(BladeCompiler $compiler){
            $compiler->directive('json', BladeUtils::class.'::compileJson');

            $compiler->directive('each', function ($expression){
                return '<?php echo $__env->renderEachWithVars(get_defined_vars(), '.$expression.') ?>';
            });

            $compiler->directive('json', BladeUtils::class.'::compileJson');

            $compiler->directive('css', BladeUtils::class.'::compileCss');

            $compiler->directive('js', BladeUtils::class.'::compileJs');

            $compiler->directive('img', BladeUtils::class.'::compileImg');

            $compiler->directive('iif', BladeUtils::class.'::compileIif');

            $compiler->directive('meta', BladeUtils::class.'::compileMeta');

            $compiler->directive('nameMeta', function ($expression){
                return BladeUtils::compileMeta('"name",'.$expression);
            });

            $compiler->directive('propMeta', function ($expression){
                return BladeUtils::compileMeta('"property",'.$expression);
            });

            $compiler->directive('itemMeta', function ($expression){
                return BladeUtils::compileMeta('"itemprop",'.$expression);
            });
        });
    }
}