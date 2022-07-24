<?php

namespace Legacy\ThePit\Providers;

use pocketmine\utils\Config;

final class JSONProvider extends BaseProvider // On ne va pas extends ça ?
{
    private string $name;
    private array $data;
    private string $path;

    public function __construct(string $name, string $path)
    {
        $this->name = $name;
        $this->path = $path;
        $file = new \SplFileInfo($path);
        if($file->getExtension() !== "json" && $file->getExtension() !== "js")
        {
            throw new \LogicException('Invalid extension "'.$file->getExtension().'", must be json or js in ' . $this);
        }
        $this->data = json_decode(file_get_contents($path));
    }

    public function getConfig(): Config
    {
        return new Config($this->path, Config::JSON);
    }

    public function getName(): string
    {
        return $this->name;
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
        if(isset($this->config[$base])){
            $base = $this->config[$base];
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