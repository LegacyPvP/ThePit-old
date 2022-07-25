<?php

namespace Legacy\ThePit\Databases;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Traits\DatabaseTrait;
use LogicException;
use RuntimeException;
use SplFileInfo;

abstract class Database implements IDatabase
{
    use DatabaseTrait;

    public function __construct(protected string $name, protected string $path)
    {
        if(!file_exists($path))
        {
            throw new RuntimeException("The file does not exist => ".$this);
        }
        $file = new SplFileInfo($path);
        if(match ($file->getExtension()){
            "yml", "yaml", "json" => false,
            default => true,
        })
        {
            throw new LogicException('Invalid extension "'.$file->getExtension().'", must be a valid extension in ' . $this);
        }
        $this->init();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    #[Pure] public function __toString(): string
    {
        return "Provider name : ". $this->getName() . " | Provider path : " . $this->getPath();
    }
}