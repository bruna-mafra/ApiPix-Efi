<?php

namespace Efi;

use Exception;

class Endpoints
{
    private $requester;
    private $endpoints;
    private $methods;
    private $options;
    private static $instance;

    /**
     * Constructor method.
     *
     * @param array $options The options for the API.
     * @param mixed $requester The requester for the API.
     */
    public function __construct(array $options, ?object $requester = null)
    {
        $this->requester = $requester;
        $this->options = $options;
        $this->endpoints = Config::get('APIs');
    }

    /**
     * Singleton instance method.
     *
     * @param array $options The options for the API.
     * @param mixed $requester The requester for the API.
     * @return Endpoints The singleton instance of the class.
     * @throws Exception When the credentials are not defined.
     */
    public static function getInstance($options = null, ?object $requester = null): Endpoints
    {
        if (!isset(self::$instance)) {
            if (!isset($options)) {
                throw new Exception('Credenciais Client_Id e Client_Secret não foram definidas corretamente');
            }
            self::$instance = new self($options, $requester);
        }
        return self::$instance;
    }

    /**
     * Magic method for calling non-existent methods.
     *
     * @param string $method The method name.
     * @param array $args The arguments for the method.
     * @return mixed The result of the method.
     * @throws Exception When the requested method does not exist.
     */
    public function __call(string $method, array $args)
    {
        $this->map($method);

        return $this->methods[$method](
            $args[0] ?? [],
            $args[1] ?? []
        );
    }

    /**
     * Magic static method for calling non-existent static methods.
     *
     * @param string $method The method name.
     * @param array $args The arguments for the method.
     * @return mixed The result of the method.
     * @throws Exception When the requested method does not exist.
     */
    public static function __callStatic(string $method, array $args)
    {
        if (method_exists('\\EfiPay\Utils', $method)) {
            return Utils::$method(
                $args[0] ?? null,
                $args[1] ?? null
            );
        }

        throw new Exception("Método '$method' solicitado inexistente");
    }

    /**
     * Maps the endpoint to its corresponding method.
     *
     * @param string $method The method name.
     * @return void
     * @throws Exception When the requested method does not exist.
     */
    private function map(string $method): void
    {
        $endpoints = $this->endpoints;
        if (!isset($endpoints['ENDPOINTS'])) {
            foreach (array_keys($endpoints) as $api) {
                if (array_column($endpoints[$api], $method)) {
                    $this->endpoints = $endpoints[$api];
                    $this->options['api'] = $api;
                    break;
                }
            }

            if (!isset($this->options['api'])) {
                throw new Exception("Método '$method' solicitado inexistente");
            }
        }

        $this->methods = array_map(function ($endpoint) {
            return function ($params = [], $body = []) use ($endpoint) {
                $route = $this->getRoute($endpoint, $params);
                $query = $this->getQueryString($params);
                $route .= $query;

                $this->options['url'] = $this->options['sandbox'] ? $this->endpoints['URL']['sandbox'] : $this->endpoints['URL']['production'];

                if ($this->options['url'] === null) {
                    throw new Exception('Os endpoints da API ' . $this->options['api'] . ' funcionam apenas em ambiente de produção');
                }

                $this->requester = $this->requester ?? new ApiRequest($this->options);

                return $this->requester->send($endpoint['method'], $route, $body);
            };
        }, $this->endpoints['ENDPOINTS']);
    }

    /**
     * Replace URL placeholders with their corresponding values in the route.
     *
     * @param array $endpoint The endpoint definition.
     * @param array $params The parameters to be replaced.
     * @return string The processed route.
     */
    private function getRoute(array $endpoint, array &$params): string
    {
        $route = $endpoint['route'];
        preg_match_all('/\:(\w+)/im', $route, $matches);
        $variables = $matches[1];

        foreach ($variables as $value) {
            if (isset($params[$value])) {
                $route = str_replace(':' . $value, $params[$value], $route);
                unset($params[$value]);
            }
        }

        return $route;
    }

    /**
     * Convert an array of parameters into a query string.
     *
     * @param array $params The parameters to be converted.
     * @return string The generated query string.
     */
    private function getQueryString(array $params): string
    {
        $query = '';

        foreach ($params as $key => $value) {
            $query .= ($query === '') ? '?' : '&';
            $query .= $key . '=';
            $query .= is_bool($value) ? ($value ? 'true' : 'false') : $value;
        }

        return $query;
    }
}
