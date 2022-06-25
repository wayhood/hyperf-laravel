<?php
/**
 * Get __ide_helper.php subset containing the provided keys with values from the input data.
 *
 * @param  array|mixed  $keys
 * @return array
 */
Hyperf\HttpServer\Request::macro('only', function ($keys) {
    $results = [];

    $input = $this->all();

    $placeholder = new stdClass();

    foreach (is_array($keys) ? $keys : func_get_args() as $key) {
        $value = data_get($input, $key, $placeholder);

        if ($value !== $placeholder) {
            \Hyperf\Utils\Arr::set($results, $key, $value);
        }
    }

    return $results;
});

/**
 * Retrieve the input data from request, include query parameters, parsed body and json body.
 *
 * @param mixed $default
 */
Hyperf\HttpServer\Request::macro('get', function (string $key, $default = null) {
    return $this->input($key, $default);
});