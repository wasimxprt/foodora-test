<?php


namespace app\Repository;

use app\Repository\Repository;

class VendorFixScheduleBackupRepository extends Repository {

    protected $tableName = 'vendor_fix_schedule_backup';

    public function __construct() {
        parent::__construct($this->tableName);
    }

    /**
     * Inserts a VendorFixScheduleBackup
     *
     * @param $vendorFixSchedulesBackupEntry
     * @return bool
     */
    public function save($vendorFixSchedulesBackupEntry) {
        $stmt = $this->db->prepare("INSERT INTO vendor_fix_schedule_backup (vendor_id, schedule_id, all_day_old, start_hour_old, stop_hour_old, all_day_new, start_hour_new, stop_hour_new, new, weekday) VALUES (:vendor_id, :schedule_id, :all_day_old, :start_hour_old, :stop_hour_old, :all_day_new, :start_hour_new, :stop_hour_new, :new, :weekday)");
        $stmt->bindParam(':vendor_id', $vendorFixSchedulesBackupEntry->getVendorId());
        $stmt->bindParam(':schedule_id', $vendorFixSchedulesBackupEntry->getScheduleId());
        $stmt->bindParam(':all_day_old', $vendorFixSchedulesBackupEntry->getAlldayOld());
        $stmt->bindParam(':start_hour_old', $vendorFixSchedulesBackupEntry->getStartHourOld());
        $stmt->bindParam(':stop_hour_old', $vendorFixSchedulesBackupEntry->getStopHourOld());
        $stmt->bindParam(':all_day_new', $vendorFixSchedulesBackupEntry->getAlldayNew());
        $stmt->bindParam(':start_hour_new', $vendorFixSchedulesBackupEntry->getStartHourNew());
        $stmt->bindParam(':stop_hour_new', $vendorFixSchedulesBackupEntry->getStopHourNew());
        $stmt->bindParam(':new', $vendorFixSchedulesBackupEntry->getNew());
        $stmt->bindParam(':weekday', $vendorFixSchedulesBackupEntry->getWeekday());

        return $stmt->execute();
    }

	/**
	 * Creation of table vendor_fix_schedule_backup
	 * @return mixed
	 */
    public function createDbTable() {
        $stmt = $this->db->prepare("CREATE TABLE `vendor_fix_schedule_backup` (
	                                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	                                  `vendor_id` bigint(1) unsigned NOT NULL,
	                                  `schedule_id` int(10) unsigned DEFAULT NULL,
	                                  `all_day_old` tinyint(1) unsigned DEFAULT NULL,
	                                  `start_hour_old` time DEFAULT NULL,
	                                  `stop_hour_old` time DEFAULT NULL,
	                                  `all_day_new` tinyint(1) unsigned NOT NULL,
	                                  `start_hour_new` time DEFAULT NULL,
	                                  `stop_hour_new` time DEFAULT NULL,
	                                  `new` tinyint(1) unsigned NOT NULL,
	                                  `weekday` bigint(1) unsigned NOT NULL,
	                                  PRIMARY KEY (`id`)
	                                ) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=latin1;");

        return $stmt->execute();
    }
} 