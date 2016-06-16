<?php
    

    namespace app\Repository;

    use app\Repository\Repository;

    /**
     * Class VendorRepository handles the queries for the vendor table
     * @package app\Repository
     */
    class VendorRepository extends Repository {

        protected $tableName = 'vendor';

        public function __construct() {
            parent::__construct($this->tableName);
        }

        /**
         * Loads dependencies of Vendor
         * @param $entityName
         * @param $vendor
         * @return mixed
         */
        public function loadDependencies($entityName, $vendor, array $dateRange = null) {
            $sql = 'SELECT * FROM ' . $entityName . ' where vendor_id = :vendor_id';

            if($dateRange)
                $sql .= ' AND special_date BETWEEN :start_date AND :end_date';

            $sth = $this->db->prepare($sql);
            $sth->bindParam(':vendor_id', $vendor['id']);

            if($dateRange) {
                $sth->bindParam(':start_date', $dateRange[0]);
                $sth->bindParam(':end_date', $dateRange[1]);
            }

            $sth->execute();
            return $sth->fetchAll(\PDO::FETCH_ASSOC);
        }
    }