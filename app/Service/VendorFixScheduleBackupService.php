<?php


namespace app\Service;

use app\Service\Service;

use app\Traits\Utility;

class VendorFixScheduleBackupService extends Service {

    use Utility;

    public function __construct(\app\Factory\Kernel $factory) {
        parent::__construct($factory);
        $this->repository = $this->factory->create('app\Repository\VendorFixScheduleBackupRepository')->instance($this->factory->connection);
    }

    /**
     * Gets all the VendorFixScheduleBackupEntries
     * @return array
     */
    public function getAll() {
        $vendorFixScheduleBackupEntryEntries = array();
        foreach($this->repository->findAll() as $vendorFixScheduleBackupEntry) {
            $vendorFixScheduleBackupEntryEntries[] = $this->createObject($this->factory->create('app\Entity\VendorFixScheduleBackup'), $vendorFixScheduleBackupEntry);
        }

        return $vendorFixScheduleBackupEntryEntries;
    }

    /**
     * Creates a VendorFixScheduleBackup object
     * @param $vendorId
     * @param $scheduleEntry
     * @param $specialDay
     * @param $new
     * @return mixed
     */
    public function createFixScheduledBackup($vendorId, $scheduleEntry, $specialDay, $weekday, $new) {
        $vendorFixScheduleBackup = $this->factory->create('app\Entity\VendorFixScheduleBackup');

        $vendorFixScheduleBackup->setVendorId($vendorId);
        $vendorFixScheduleBackup->setScheduleId($scheduleEntry ? $scheduleEntry->getId() : null);
        $vendorFixScheduleBackup->setAlldayOld($scheduleEntry ? $scheduleEntry->getAllday() : null);
        $vendorFixScheduleBackup->setStartHourOld($scheduleEntry ? $scheduleEntry->getStartHour() : null);
        $vendorFixScheduleBackup->setStopHourOld($scheduleEntry ? $scheduleEntry->getStopHour() : null);
        $vendorFixScheduleBackup->setAllDayNew($specialDay->getAllday());
        $vendorFixScheduleBackup->setStartHourNew($specialDay->getStartHour());
        $vendorFixScheduleBackup->setStopHourNew($specialDay->getStopHour());
        $vendorFixScheduleBackup->setNew($new ? 1 : 0);
        $vendorFixScheduleBackup->setWeekday($weekday);

        return $vendorFixScheduleBackup;
    }

    public function createDbTable() {
        if(!$this->repository->createDbTable())
            throw new \Exception('Unable to create vendor_fix_schedule_backup table');

        return true;
    }

    /**
     * Saves all the entries into the DB
     * @param $vendorFixSchedulesBackup
     */
    public function save($vendorFixSchedulesBackup){
        $this->createDbTable();

        foreach($vendorFixSchedulesBackup as $vendorFixSchedulesBackupEntry)
            $this->repository->save($vendorFixSchedulesBackupEntry);
    }

    /**
     * Rollback all the temporary changes to vendor_schedule table
     * In case there's an error on a record it rollbacks everything
     * @param $vendorFixScheduleBackupEntries
     */
    public function rollbackSchedule($vendorFixScheduleBackupEntries) {
        $vendorScheduleService = $this->factory->create('app\Service\VendorScheduleService');

	    try {
		    $this->repository->getConnection()->beginTransaction();

		    foreach($vendorFixScheduleBackupEntries as $vendorFixScheduleBackupEntry) {
				$this->rollbackVendorScheduleEntry($vendorScheduleService, $vendorFixScheduleBackupEntry);
		    }

		    $this->repository->getConnection()->commit();
	    } catch(PDOException $e) {
		    $this->repository->getConnection()->rollback();
		    throw new \Exception('failure during rollback schedule: no changes made');
	    }

        return true;
    }

	/**
	 * Rollsback a single entry
	 * @param $vendorScheduleService
	 * @param $vendorFixScheduleBackupEntry
	 */
	public function rollbackVendorScheduleEntry ($vendorScheduleService, $vendorFixScheduleBackupEntry) {
		if($vendorFixScheduleBackupEntry->getScheduleId()) {

			// check if exists schedule and proceed with either update or insert
			if($scheduleToUpdate = $vendorScheduleService->getById($this->factory->create('app\Entity\VendorSchedule'), $vendorFixScheduleBackupEntry->getScheduleId())) {

				$scheduleToUpdate->setAllday($vendorFixScheduleBackupEntry->getAllDayOld());
				$scheduleToUpdate->setStartHour($vendorFixScheduleBackupEntry->getStartHourOld());
				$scheduleToUpdate->setStopHour($vendorFixScheduleBackupEntry->getStopHourOld());

				$vendorScheduleService->updateVendorScheduleEntry($scheduleToUpdate);
			}else {
				$scheduleToInsert = $this->factory->create('app\Entity\VendorSchedule');

				$scheduleToInsert->setId($vendorFixScheduleBackupEntry->getScheduleId());
				$scheduleToInsert->setVendorId($vendorFixScheduleBackupEntry->getVendorId());
				$scheduleToInsert->setWeekday($vendorFixScheduleBackupEntry->getWeekday());
				$scheduleToInsert->setAllday($vendorFixScheduleBackupEntry->getAllDayOld());
				$scheduleToInsert->setStartHour($vendorFixScheduleBackupEntry->getStartHourOld());
				$scheduleToInsert->setStopHour($vendorFixScheduleBackupEntry->getStopHourOld());

				$vendorScheduleService->insertVendorScheduleEntry($scheduleToInsert);
			}
		} else {
			// remove schedule by weekday
			$vendorScheduleService->deleteVendorScheduleEntryFromWeekday($vendorFixScheduleBackupEntry->getWeekday());
		}
	}
} 