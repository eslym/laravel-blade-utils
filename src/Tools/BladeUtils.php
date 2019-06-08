<?php


namespace Eslym\BladeUtils\Tools;

class BladeUtils
{
    public function parseArguments(string $expressions): Arguments{
        return new Arguments($expressions);
    }

    public function compileJson($expression): string{
        $args = $this->parseArguments($expression);
        if($args->isAllSimple()){
            $args = array_map(function (Argument $arg){
                return $arg->val();
            }, iterator_to_array($args));
            if(count($args) < 2){
                $args[]= 15;
            }
            if(count($args) < 3){
                $args[]= 512;
            }
            return json_encode(...$args);
        }
        $args = $args->toArray();
        if(count($args) < 2){
            $args[]= '15';
        }
        if(count($args) < 3){
            $args[]= '512';
        }
        return "<?php echo json_encode($args[0], $args[1], $args[2]); ?>";
    }

    public function compileIif($expression): string{
        $args = $this->parseArguments($expression);
        if($args->count() == 2 && $args[1]->isSimple()){
            return "<?php if($args[0]): ?>".e($args[1]->val())."<?php endif; ?>";
        }
        if($args->count() == 3 && $args[1]->isSimple() && $args[2]->isSimple()){
            return "<?php if($args[0]): ?>".e($args[1]->val())."<?php else: ?>".e($args[2]->val())."<?php endif; ?>";
        }
        $arg3 = $args->count() >= 3 ? $args[2] : "''";
        return "<?php echo e(($args[0]) ? ($args[1]) : ($arg3)); ?>";
    }

    public function compileCss($expression): string{
        $args = $this->parseArguments($expression);
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
    }

    public function compileJs($expression):string{
        $args = $this->parseArguments($expression);
        if($args[0]->isSimple()){
            $src = e($args[0]->val());
        } else {
            $src = "<?php echo e($args[0]); ?>";
        }
        $script = "<script type=\"text/javascript\" lang=\"js\" src=\"$src\"";
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
        return $script.'></script>';
    }

    public function compileImg($expression) {
        $args = $this->parseArguments($expression);
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
    }

    public function compileMeta($expression): string{
        $args = $this->parseArguments($expression);
        if(!(($args[0]->isArray() || $args[0]->isSimple()) && $args[1]->isArray())){
            return "<?php echo \\". \Eslym\BladeUtils\Facades\BladeUtils::class."::buildMeta($args); ?>";
        }
        $attrs = [];
        if($args[0]->isSimple()){
            $attrs []= e($args[0]->val());
        } else {
            foreach ($args[0]->loopArray() as $value){
                $attrs []=  $value->isSimple() ?
                    e($value->val()) :
                    "<?php echo e($value); ?>";
            }
        }
        $result = '';
        foreach ($attrs as $attr){
            foreach ($args[1]->loopArray() as $key => $value){
                if($key instanceof Argument){
                    $key = $key->isSimple() ?
                        e($key->val()) :
                        "<?php echo e($key); ?>";
                } else {
                    $key = e($key);
                }
                $value = $value->isSimple() ?
                    e($value->val()) :
                    "<?php echo e($value); ?>";
                $result.= '<meta '.$attr.'="'.$key.'" content="'.$value.'">';
            }
        }
        return $result;
    }

    /**
     * @param string|array $attrs
     * @param array $meta
     * @return string
     */
    public function buildMeta($attrs, array $meta): string{
        if(is_string($attrs)){
            $attrs = [$attrs];
        }
        $result = '';
        foreach ($attrs as $attr){
            foreach ($meta as $key => $value){
                $result.= '<meta '.$attr.'="'.e($key).'" content="'.e($value).'">';
            }
        }
        return $result;
    }

    public function buildPropMeta(array $meta): string{
        return $this->buildMeta('property', $meta);
    }

    public function buildNameMeta(array $meta): string{
        return $this->buildMeta('name', $meta);
    }

    public function buildItemMeta(array $meta): string{
        return $this->buildMeta('itemprop', $meta);
    }
}