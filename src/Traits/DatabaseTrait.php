<?php

namespace Legacy\ThePit\Traits;

trait DatabaseTrait
{
    protected mixed $data;

    protected function init(): void {
        $this->data = yaml_parse_file($this->path);
    }

    public function get(mixed $k, mixed $default = null): mixed
    {
        return $this->data[$k] ?? $default;
    }

    public function set(mixed $k, mixed $v): void
    {
        $this->data[$k] = $v;
    }

    public function getNested(mixed $k, mixed $default = null): mixed
    {
        if(isset($this->data[$k])){
            return $this->data[$k] ?? $default;
        }

        $vars = explode(".", $k);
        $base = array_shift($vars);
        if(isset($this->data[$base])){
            $base = $this->data[$base];
        }else{
            return $default; //''?
        }

        while(count($vars) > 0){
            $basek = array_shift($vars);
            if(is_array($base) && isset($base[$basek])){
                $base = $base[$basek];
            }else{
                return $default; //''?
            }
        }

        return $this->data[$k] = $base;
    }

    public function setNested(mixed $k, mixed $v)
    {
        $vars = explode(".", $k);
        $base = array_shift($vars);

        if(!isset($this->data[$base])){
            $this->data[$base] = [];
        }

        $base = &$this->data[$base];

        while(count($vars) > 0){
            $basek = array_shift($vars);
            if(!isset($base[$basek])){
                $base[$basek] = [];
            }
            $base = &$base[$basek];
        }

        $base = $v;
    }

    public function exist(mixed $k): bool
    {
        return isset($this->data[$k]);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function dump(): array {
        return $this->data;
    }
}