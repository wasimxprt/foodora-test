<?php

namespace app\Repository;

use app\Repository\Repository;

class VendorScheduleRepository extends Repository {

    protected $tableName = 'vendor_schedule';

    public function __construct() {
        parent::__construct($this->tableName);
    }

    /**
     * Checks if a VendorSchedule exists
     * @param $scheduleEntry
     * @return mixed
     */
    public function checkEntryExists($scheduleEntry) {
        $sth = $this->db->prepare("SELECT id FROM " . $this->tableName . " where id = :id_schedule");
        $sth->bindParam(':id_schedule', $scheduleEntry->getId());
        $sth->execute();

        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Updates a VendorSchedule
     * @param $scheduleEntry
     * @return mixed
     */
    public function updateScheduleEntry($scheduleEntry) {
        $sth = $this->db->prepare("update " . $this->tableName . " set all_day = :all_day, start_hour = :start_hour, stop_hour = :stop_hour where id = :id_schedule");
        $sth->bindParam(':all_day', $scheduleEntry->getAllday());
        $sth->bindParam(':start_hour', $scheduleEntry->getStartHour());
        $sth->bindParam(':stop_hour', $scheduleEntry->getStopHour());
        $sth->bindParam(':id_schedule', $scheduleEntry->getId());
        $sth->execute();

        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Inserts a VendorSchedule
     * @param $scheduleEntry
     * @return bool
     */
    public function insertScheduleEntry($scheduleEntry) {
        $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . " (id, vendor_id, weekday, all_day, start_hour, stop_hour) VALUES (:id, :vendor_id, :weekday, :all_day, :start_hour, :stop_hour)");
        $stmt->bindParam(':id', $scheduleEntry->getId() ? $scheduleEntry->getId() : null);
        $stmt->bindParam(':vendor_id', $scheduleEntry->getVendorId());
        $stmt->bindParam(':weekday', $scheduleEntry->getWeekday());
        $stmt->bindParam(':all_day', $scheduleEntry->getAllDay());
        $stmt->bindParam(':start_hour', $scheduleEntry->getStartHour());
        $stmt->bindParam(':stop_hour', $scheduleEntry->getStopHour());

        return $stmt->execute();
    }

    /**
     * Deletes a VendorSchedule
     * @param $scheduleEntry
     */
    public function deleteScheduleEntry($scheduleEntry) {
        $sth = $this->db->prepare("delete from " . $this->tableName . " where id = :id_schedule");
        $sth->bindParam(':id_schedule', $scheduleEntry->getId());

        return $sth->execute();
    }

    /**
     * Deletes a VendorSchedule from weekday
     * @param $scheduleEntry
     */
    public function deleteScheduleEntryFromWeekday($weekday) {
        $sth = $this->db->prepare("delete from " . $this->tableName . " where weekday = :weekday");
        $sth->bindParam(':weekday', $weekday);
        return $sth->execute();
    }
} 