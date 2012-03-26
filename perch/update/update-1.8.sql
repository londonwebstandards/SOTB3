ALTER TABLE `__PREFIX__contentItems` ADD COLUMN `contentSearchable` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `contentOptions`;
ALTER TABLE `__PREFIX__contentItems` ADD COLUMN `contentOrder` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `contentNew`;
