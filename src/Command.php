<?php
namespace Es;

/**
 * Command.php
 */
class Command
{
    private $name;

    private $data;

    private $config;

    private $validations;

    public function __construct($name, $args, $middlewareHandler, $config)
    {
        $this->name = $name;
        $this->data = $args;
        $this->config = $config;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getValidations()
    {
        return $this->validations;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function setValidations($validations)
    {
        $this->validations = $validations;
    }
}