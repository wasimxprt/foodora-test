<?php
    
    require_once('config/conf.php');

    use app\Factory\Kernel;

    use app\Exception\InvalidTableName;

    try {
        $factory = new Kernel($provider); // provider from the config/conf.php

        $vendorService = $factory->create('app\Service\VendorService');

        $vendors = $vendorService->getAll();

        $vendorFixSchedulesBackupService = $factory->create('app\Service\VendorFixScheduleBackupService');

        $vendorScheduleService = $factory->create('app\Service\VendorScheduleService');

        foreach($vendors as $vendor){
            list($vendorScheduleModified, $vendorFixSchedulesBackup) = $vendorService->fixSchedule($vendor);

            // items to temporarily remove from vendor_schedule table
            $removed = $vendorScheduleService->removedEntries($vendor->getSchedules(), $vendorScheduleModified);

            $vendorScheduleService->updateVendorSchedule($vendorScheduleModified, $removed);
            $vendorFixSchedulesBackupService->save($vendorFixSchedulesBackup);
        }

        echo 'Vendor Schedule has been fixed :-)';
    } catch (\Exception $e) {
        echo 'Problem occurred ' . $e->getMessage();
    }
