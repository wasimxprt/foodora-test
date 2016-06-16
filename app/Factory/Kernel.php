<?php
    
    namespace app\Factory;

    /**
     * Class Kernel creates dynamically a given Class returning its instance with the Kernel injected in it in order to have the DB connection available everywhere
     *
     * @return Object
     * @package app\Factory
     */
    class Kernel {

        public $provider = null;
        public $connection = null;

        public function __construct(callable $provider) {
            $this->provider = $provider;
        }

        public function create($name) {
            if ($this->connection === null) {
                $this->connection = call_user_func($this->provider);
            }

            return new $name($this);
        }
    }