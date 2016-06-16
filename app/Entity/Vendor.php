<?php

    namespace app\Entity;

    /**
     * Class Vendor
     * @package app\Entity
     */
    class Vendor {

        private $id;
        private $name;

        private $schedules = array();
        private $specialDays = array();

        public function __construct() {

        }

        /**
         * @param mixed $id
         */
        public function setId($id) {
            $this->id = $id;
        }

        /**
         * @return mixed
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @param mixed $name
         */
        public function setName($name) {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @param array $schedule
         */
        public function setSchedules($schedule) {
            $this->schedules = $schedule;
        }

        /**
         * @return array
         */
        public function getSchedules() {
            return $this->schedules;
        }

        /**
         * @param array $specialDays
         */
        public function setSpecialDays($specialDays) {
            $this->specialDays = $specialDays;
        }

        /**
         * @return array
         */
        public function getSpecialDays() {
            return $this->specialDays;
        }
    }