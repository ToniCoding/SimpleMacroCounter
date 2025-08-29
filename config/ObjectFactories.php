<?php

/**
 * Simple dependency injection container.
 */
class Container {
    /**
     * @var array<string, mixed> Array of registered services or factories.
     */
    private array $services = [];

    /**
     * @var array<string, string> Cache of configuration credentials.
     */
    private array $credCache = [];

    /**
     * Registers a service in the container.
     *
     * @param string   $name    The service name.
     * @param callable $factory A callable that returns the service instance.
     */
    public function setService(string $name, callable $factory): void {
        $this->services[$name] = $factory;
    }

    /**
     * Retrieves a service from the container.
     *
     * If the service is a callable, it will be executed once and
     * replaced with its return value (singleton behavior).
     *
     * @param string $name The service name.
     *
     * @return mixed The service instance.
     *
     * @throws Exception If the service is not found.
     */
    public function getService(string $name) {
        if (!isset($this->services[$name])) {
            throw new Exception("Service {$name} not found.");
        }

        if (is_callable($this->services[$name])) {
            $this->services[$name] = $this->services[$name]($this);
        }

        return $this->services[$name];
    }

    /**
     * Loads and caches credentials from config.ini.
     *
     * @return array<string, string> The credentials array.
     */
    public function getCredConf(): array {
        if (empty($this->credCache)) {
            $this->credCache = parse_ini_file(BASE_PATH . "config.ini");
        }

        return $this->credCache;
    }
}
