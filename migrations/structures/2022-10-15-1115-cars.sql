ALTER TABLE `vehicles`
    ADD `annotation` text COLLATE 'utf8mb4_general_ci' NOT NULL AFTER `name`,
    ADD `description` text COLLATE 'utf8mb4_general_ci' NOT NULL AFTER `annotation`,
    ADD `color` varchar(50) NULL AFTER `vat_deduction`,
    ADD `fuel` varchar(50) NULL AFTER `color`,
    ADD `param1` tinyint(1) NULL,
    ADD `param2` tinyint(1) NULL,
    ADD `param3` tinyint(1) NULL,
    ADD `param4` tinyint(1) NULL,
    ADD `param5` tinyint(1) NULL,
    ADD `param6` tinyint(1) NULL,
    ADD `param7` tinyint(1) NULL,
    ADD `param8` tinyint(1) NULL,
    ADD `param9` tinyint(1) NULL,
    ADD `param10` tinyint(1) NULL,
    ADD `param11` tinyint(1) NULL,
    ADD `param12` tinyint(1) NULL,
    ADD `param13` tinyint(1) NULL,
    ADD `param14` tinyint(1) NULL,
    ADD `param15` tinyint(1) NULL,
    ADD `param16` tinyint(1) NULL;

ALTER TABLE `vehicles`
    ADD `seo_name` varchar(255) COLLATE 'utf8mb4_general_ci' NOT NULL AFTER `name`;