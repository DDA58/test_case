CREATE PROCEDURE IF NOT EXISTS truncate_logs()
BEGIN
SET foreign_key_checks = 0;

TRUNCATE commands_queue;
TRUNCATE emails_send_log;

SET foreign_key_checks = 1;
END;