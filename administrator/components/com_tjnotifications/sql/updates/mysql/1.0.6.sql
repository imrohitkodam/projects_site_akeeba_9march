----Change table Key;

-- ALTER TABLE `#__tj_notification_user_exclusions` DROP INDEX `client1`;
ALTER TABLE `#__tj_notification_user_exclusions` ADD KEY `client1` (`client`,`provider`(50),`key`);
    