<?php


namespace app\Service;

use app\Service\Service;

class VendorScheduleService extends Service {

    public function __construct(\app\Factory\Kernel $factory) {
        parent::__construct($factory);
        $this->repository = $this->factory->create('app\Repository\VendorScheduleRepository')->instance($this->factory->connection);
    }

    /**
     * Creates a VendorSchedule object from given data
     * @param $vendorId
     * @param $specialDay
     * @param $weekday
     * @return mixed
     */
    public function createSchedule($vendorId, $specialDay, $weekday) {
        $vendorSchedule = $this->factory->create('app\Entity\VendorSchedule');

        $vendorSchedule->setVendorId($vendorId);
        $vendorSchedule->setWeekday($weekday);
        $vendorSchedule->setAllday($specialDay->getAllday());
        $vendorSchedule->setStartHour($specialDay->getStartHour());
        $vendorSchedule->setStopHour($specialDay->getStopHour());

        return $vendorSchedule;
    }

    /**
     * Updates the VendorSchedule calling the repository to save into DB
     * @param $newVendorSchedule
     * @param $removed
     */
    public function updateVendorSchedule($newVendorSchedule, $removed) {
	    try {
		    $this->repository->getConnection()->beginTransaction();

		    foreach($newVendorSchedule as $newScheduleEntry) {
			    // if entry exists in vendor_schedule proceed with update otherwise insert
			    $this->vendorScheduleEntryExists($newScheduleEntry) ? $this->updateVendorScheduleEntry($newScheduleEntry) : $this->insertVendorScheduleEntry($newScheduleEntry);
		    }

		    // delete remove vendor schedule entries
		    $this->deleteVendorScheduleEntry($removed);

		    $this->repository->getConnection()->commit();
	    } catch(PDOException $e) {
		    $this->repository->getConnection()->rollback();
		    throw new \Exception('failure during schedule update: no changes made');
	    }
    }

    /**
     * Searches removed entries from the new VendorSchedule referring to the original VendorSchedule
     * @param $vendorSchedule
     * @param $newSchedule
     * @return array
     */
    public function removedEntries($vendorSchedule, $newSchedule){
        $deletedEntries = array();
        foreach($vendorSchedule as $vendorScheduleEntry){
            if(!in_array($vendorScheduleEntry, $newSchedule))
                $deletedEntries[] = $vendorScheduleEntry;
        }

        return $deletedEntries;
    }

    /**
     * Checks whether a given VendorSchedule exists in the DB
     * @param $newScheduleEntry
     * @return mixed
     */
    public function vendorScheduleEntryExists($newScheduleEntry) {
        return $this->repository->checkEntryExists($newScheduleEntry);
    }

    /**
     * Updates a given VendorSchedule
     * @param $vendorScheduleEntry
     * @return mixed
     */
    public function updateVendorScheduleEntry($vendorScheduleEntry) {
        return $this->repository->updateScheduleEntry($vendorScheduleEntry);
    }

    /**
     * Inserts a given VendorSchedule
     * @param $vendorScheduleEntry
     * @return mixed
     */
    public function insertVendorScheduleEntry($vendorScheduleEntry) {
        return $this->repository->insertScheduleEntry($vendorScheduleEntry);
    }

    /**
     * Deletes a given VendorSchedule
     * @param $removed
     * @return mixed
     */
    private function deleteVendorScheduleEntry($removed) {
        foreach($removed as $entryToRemove) {
            $this->repository->deleteScheduleEntry($entryToRemove);
        }
    }

    /**
     * Deletes a VendorSchedule from the weekday
     * @param $weekday
     * @return mixed
     */
    public function deleteVendorScheduleEntryFromWeekday($weekday) {
        return $this->repository->deleteScheduleEntryFromWeekday($weekday);
    }
}