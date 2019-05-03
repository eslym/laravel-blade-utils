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
        $this->app->bind(BladeUtilsImpl::class, function(){
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

        Blade::directive('json', function($expression){
            $args = BladeUtils::parseArguments($expression)->toArray();
            if(count($args) < 2){
                $args[]= '15';
            }
            if(count($args) < 3){
                $args[]= '512';
            }
            return "<?php echo json_encode($args[0], $args[1], $args[2]); ?>";
        });

        Blade::directive('each', function ($expression){
            return '<?php echo $__env->renderEachWithVars(get_defined_vars(), '.$expression.') ?>';
        });

        Blade::directive('iif', function ($expression){
            $args = BladeUtils::parseArguments($expression);
            if($args->count() == 2 && $args[1]->isSimple()){
                return "<?php if($args[0]): ?>".e($args[1]->val())."<?php endif; ?>";
            }
            if($args->count() == 3 && $args[1]->isSimple() && $args[2]->isSimple()){
                return "<?php if($args[0]): ?>".e($args[1]->val())."<?php else: ?>".e($args[2]->val())."<?php endif; ?>";
            }
            $arg3 = $args->count() >= 3 ? $args[2] : "''";
            return "<?php echo e(($args[0]) ? ($args[1]) : ($arg3)); ?>";
        });

        Blade::directive('css', function ($expression) {
            $args = BladeUtils::parseArguments($expression);
            if($args[0]->isSimple()){
                $href = e($args[0]->val());
            } else {
                $href = "<?php echo e($args[0]); ?>";
            }
            $link = "<link rel=\"stylesheet\" href=\"$href\"";
            if($args->count() > 1){
                if($args[1]->isSimple()){
                    $attr = e($args[1]->val());
                } else {
                    $attr = "<?php echo e($args[1]); ?>";
                }
                $link .= " integrity=\"$attr\"";
            }
            if($args->count() > 2){
                if($args[2]->isSimple()){
                    $attr = e($args[2]->val());
                } else {
                    $attr = "<?php echo e($args[2]); ?>";
                }
                $link .= " crossorigin=\"$attr\"";
            }
            return $link.'/>';
        });

        Blade::directive('js', function ($expression) {
            $args = BladeUtils::parseArguments($expression);
            if($args[0]->isSimple()){
                $src = e($args[0]->val());
            } else {
                $src = "<?php echo e($args[0]); ?>";
            }
            $script = "<script type=\"text/javascript\" language=\"js\" src=\"$src\"";
            if($args->count() > 1){
                if($args[1]->isSimple()){
                    $attr = e($args[1]->val());
                } else {
                    $attr = "<?php echo e($args[1]); ?>";
                }
                $script .= " integrity=\"$attr\"";
            }
            if($args->count() > 2){
                if($args[2]->isSimple()){
                    $attr = e($args[2]->val());
                } else {
                    $attr = "<?php echo e($args[2]); ?>";
                }
                $script .= " crossorigin=\"$attr\"";
            }
            return $script.'</script>';
        });

        Blade::directive('img', function ($expression) {
            $args = BladeUtils::parseArguments($expression);
            if($args[0]->isSimple()){
                $src = e($args[0]->val());
            } else {
                $src = "<?php echo e($args[0]); ?>";
            }
            $script = "<img src=\"$src\"";
            if($args->count() > 1){
                if($args[1]->isSimple()){
                    $attr = e($args[1]->val());
                } else {
                    $attr = "<?php echo e($args[1]); ?>";
                }
                $script .= " alt=\"$attr\"";
            }
            if($args->count() > 2){
                if($args[2]->isSimple()){
                    $attr = e($args[2]->val());
                } else {
                    $attr = "<?php echo e($args[2]); ?>";
                }
                $script .= " class=\"$attr\"";
            }
            return $script.'/>';
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