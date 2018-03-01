<?php

namespace Es;
/**
 * Class BaseClient
 */

class BaseClient
{
    private $config;

    private $manifest;

    protected $middlewareHandler;

    /**
     * BaseClient constructor.
     * @param $args
     */
    public function __construct(array $args)
    {
        list($service, $exceptionClass) = $this->parseClass();
        $args['service'] = $service;

        $this->middlewareHandler = new MiddlewareHandler();
        
        $this->manifest = $this->manifest($service);

        $this->config = $args;
        $this->config['end_point'] = $this->getEndpoint();

    }

    public function __call($name, $args)
    {
        $command = $this->getCommand($name, $args[0]);
        return $this->middlewareHandler->execute($command);
    }

    public function help()
    {
        $opDoc = '';
        foreach ($this->manifest['operations'] as $operation) {
            $opDoc .= "$operation[name]"." ($operation[nameZh])  Doc: $operation[doc] \n";
        }
        $opDoc .= "\n";
        echo $opDoc;
    }

    public function getMiddlewareHandler()
    {
        return $this->middlewareHandler;
    }
    
    private function parseClass()
    {
        $class = get_class($this);

        if ($class === __CLASS__) {
            return ['', ''];
        }

        $service = substr($class, strrpos($class, '\\') + 1, -6);

        return [
            strtolower($service),
            ''
        ];
    }

    public function getCommand($name, array $args = [])
    {
        // 判断command是否存在
        $name = ucfirst($name);
        if (!isset($this->manifest['operations'][$name])) {
            throw new \InvalidArgumentException("Operation not found: $name.");
        }

        $this->config = array_merge($this->config, $this->manifest['operations'][$name]);
        $command = new Command($name, $args, clone $this->middlewareHandler, $this->config);

        if (isset($this->manifest['validations'][$name])) {
            $command->setValidations($this->manifest['validations'][$name]);
        }

        return $command;
    }


    public function manifest($service)
    {
        $service = strtolower($service);
        $path = __DIR__ . "/../data/$service.json";
        $manifest = json_decode(file_get_contents($path), true);
        return $manifest;
    }

    /**
     * 根据
     * @param $service
     */
    public function getEndpoint()
    {
        if (!empty($this->manifest['metadata']['endpoint'])) {
            return $this->manifest['metadata']['endpoint'];
        }
        return '';
    }

}