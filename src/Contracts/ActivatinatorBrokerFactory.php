<?php

namespace Codepunk\Activatinator\Contracts;

interface ActivatinatorBrokerFactory
{
    /**
     * Get a validation broker instance by name.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function broker($name = null);
}
