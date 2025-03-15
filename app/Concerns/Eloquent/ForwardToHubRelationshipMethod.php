<?php

declare(strict_types=1);

namespace App\Concerns\Eloquent;

trait ForwardToHubRelationshipMethod
{
    /**
     * @param  string  $method
     * @param  array<int,mixed>  $parameters
     */
    public function __call($method, $parameters)
    {
        if (! str_contains($method, '/')) {
            return parent::__call(method: $method, parameters: $parameters);
        }

        [$hub, $relationship] = explode('/', $method);

        if (! method_exists($this, $hub)) {
            throw new \BadMethodCallException(\sprintf('Hub method %s does not exist', $hub));
        }

        $object = \call_user_func(callback: [$this, $hub]);

        return $this->forwardCallTo(object: $object, method: $relationship, parameters: $parameters);
    }
}
