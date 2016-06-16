<?php

    require_once('config/conf.php');

    use app\Factory\Kernel;

    try {
        $factory = new Kernel($provider); // provider from the config/conf.php

        $vendorFixScheduleBackup = $factory->create('app\Service\VendorFixScheduleBackupService');

        $vendorFixScheduleBackupEntries = $vendorFixScheduleBackup->getAll();

        $isDone = $vendorFixScheduleBackup->rollbackSchedule($vendorFixScheduleBackupEntries);

        echo 'ROLLBACK DONE :-)';
    } catch (\Exception $e) {
        echo 'Problem occurred ' . $e->getMessage();
    }