<?php

class Container {
    private array $services = [];

    // Function that registers a service.
    public function setService(string $name, callable $factory): void {
        $this->services[$name] = $factory;
    }

    // Function that obtain a service.
    public function getService(string $name) {
        if (!isset($this->services[$name])) {
            throw new Exception("Service {$name} not found.");
        }

        if (is_callable($this->services[$name])) {
            $this->services[$name] = $this->services[$name]($this);
        }

        return $this->services[$name];
    }
}
