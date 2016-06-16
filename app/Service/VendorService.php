<?php
   

    namespace app\Service;

    use app\Traits\Utility;

    use app\Service\Service;

    /**
     * Class VendorService handles the business code for the Vendor Entity and its associations
     * @package app\Service
     */
    class VendorService extends Service {

        const VENDOR_SCHEDULE    = 'vendor_schedule';
        const VENDOR_SPECIAL_DAY = 'vendor_special_day';
        const VENDOR_SPECIAL_DAY_EVENT_TYPE_CLOSED = 'closed';

        const DATE_START = '2015-12-21';
        const DATE_END = '2015-12-27';

        use Utility;

        public function __construct(\app\Factory\Kernel $factory) {
            parent::__construct($factory);
            $this->repository = $this->factory->create('app\Repository\VendorRepository')->instance($this->factory->connection);
        }

        public function getAll() {
            return $this->getVendors($this->repository->findAll());
        }

        /**
         * Converting a Vendor query result in objects
         * @param $vendors
         * @return array
         */
        public function getVendors($vendors) {
            $vendorsResult = array();

            foreach ($vendors as $vendor) {
                $vendorTmp = $this->factory->create('app\Entity\Vendor');

                $vendorTmp->setSchedules($this->load(self::VENDOR_SCHEDULE, $vendor));
                $vendorTmp->setSpecialDays($this->load(self::VENDOR_SPECIAL_DAY, $vendor, array(self::DATE_START, self::DATE_END)));

                $vendorsResult[] = $this->createObject($vendorTmp, $vendor);
            }

            return $vendorsResult;
        }

        /**
         * Loads either VendorSpecialDays or VendorSchedule, creates objects from query result and returns an array of objects
         * @param $entityName
         * @param $vendor
         * @return array
         */
        public function load($entityName, $vendor, array $dateRange = null) {
            $entities   = $this->repository->loadDependencies($entityName, $vendor, $dateRange);
            $items = array();

            foreach ($entities as $obj) {
                $specialDayTmp  = $this->factory->create('app\Entity\\' . $this->camelCaseConvertion($entityName));
                $items[] = $this->createObject($specialDayTmp, $obj);
            }

            return $items;
        }

        /**
         * Goes through all the specials days and updates the schedule entry based on the weekday
         * @param $vendor
         * @return array
         */
        public function fixSchedule($vendor) {
            $vendorFixSchedulesBackup = array();

            $specialDays = $vendor->getSpecialDays();
            $vendorSchedules = $vendor->getSchedules();
            foreach ($specialDays as $specialDay) {
                list($vendorSchedules, $vendorFixSchedulesBackup) = $this->updateSchedule($vendor, $specialDay, $vendorFixSchedulesBackup, $vendorSchedules);
            }

            return array($vendorSchedules, $vendorFixSchedulesBackup);
        }

        /**
         * Updates the vendor schedule entries and "logs" the changes in the VendorFixScheduleBackup
         * @param $vendor
         * @param $specialDay
         * @param $vendorFixSchedulesBackup
         * @param $vendorSchedules
         * @return array Returns the new schedule and the vendorFixScheduleBackup
         */
        public function updateSchedule($vendor, $specialDay, $vendorFixSchedulesBackup, $vendorSchedules) {
            $vendorFixSchedulesBackupService = $this->factory->create('app\Service\VendorFixScheduleBackupService');
            $vendorScheduleService = $this->factory->create('app\Service\VendorScheduleService');
            $checkedWeekday = array();
            foreach ($vendorSchedules as $key => $scheduleEntry) {
                if ($scheduleEntry->getWeekday() == $this->getWeekdayFromDate($specialDay->getSpecialDate())) {
                    $vendorFixSchedulesBackup[] = $vendorFixSchedulesBackupService->createFixScheduledBackup($vendor->getId(), $scheduleEntry, $specialDay, $this->getWeekdayFromDate($specialDay->getSpecialDate()), false);

                    // There might be the same weekday multiple times in a schedule, in this case I edit the first one and remove the others
                    if(!in_array($scheduleEntry->getWeekday(), $checkedWeekday)) {
                        $this->editScheduleEntry($scheduleEntry, $specialDay);
                    } else {
                        unset($vendorSchedules[$key]);
                    }

                    $checkedWeekday[] = $scheduleEntry->getWeekDay();
                }
            }

            // If there's no schedule for a certain weekday of a special days
            if(empty($checkedWeekday)){
                $vendorSchedules[] = $vendorScheduleService->createSchedule($vendor->getId(), $specialDay, $this->getWeekdayFromDate($specialDay->getSpecialDate()));
                $vendorFixSchedulesBackup[] = $vendorFixSchedulesBackupService->createFixScheduledBackup($vendor->getId(), null, $specialDay, $this->getWeekdayFromDate($specialDay->getSpecialDate()), true);
            }

            return array($vendorSchedules, $vendorFixSchedulesBackup);
        }

        /**
         * Edits a schedule entry by reference
         * @param $schedule
         * @param $specialDay
         */
        public function editScheduleEntry(&$schedule, $specialDay) { // & in not mandatory but avoid misunderstandings
            $specialDay->getEventType() == self::VENDOR_SPECIAL_DAY_EVENT_TYPE_CLOSED ? $schedule->setStartHour(null) : $schedule->setStartHour($specialDay->getStartHour());
            $specialDay->getEventType() == self::VENDOR_SPECIAL_DAY_EVENT_TYPE_CLOSED ? $schedule->setStopHour(null) : $schedule->setStopHour($specialDay->getStopHour());
            $schedule->setAllday($specialDay->getAllDay());
        }
    }