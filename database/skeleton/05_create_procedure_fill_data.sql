CREATE PROCEDURE IF NOT EXISTS fill_data()
BEGIN

	DECLARE i INT DEFAULT 0;
	DECLARE first_of_sequence INT DEFAULT 100;
	DECLARE iteration_of_sequence INT DEFAULT 10;
	DECLARE amount_of_rows INT DEFAULT 0;
    DECLARE coeff_q INT DEFAULT 2;
    DECLARE percent_to_update INT DEFAULT 1;

   	SET foreign_key_checks = 0;
	TRUNCATE emails;
	TRUNCATE users;
	TRUNCATE emails_send_log;
	TRUNCATE commands_queue;
	SET foreign_key_checks = 1;

	#geometric sequence
	SET amount_of_rows = first_of_sequence + (coeff_q*(first_of_sequence*POW(coeff_q, iteration_of_sequence - 1)) - first_of_sequence)/(coeff_q -1);

	#generate first users package
	WHILE i < first_of_sequence DO
		INSERT INTO `users` (`uuid`,`username`,`validts`) VALUES (
			UUID()
			, CONCAT_WS('-', 'username', i)
			, 0
		);
		SET i = i + 1;
	END WHILE;

	SET i = 0;

	#insert users by packages
	WHILE i < iteration_of_sequence DO
		INSERT INTO `users` (`uuid`,`username`,`validts`)
		SELECT
			UUID()
			, CONCAT_WS('-', `username`, i)
			, 0
		FROM `users`;
		SET i = i + 1;
	END WHILE;

	#insert emails for everyone users
	INSERT INTO `emails` (`email`, `user_uuid`, `confirmed`, `checked`, `valid`, `is_last`)
	SELECT
		CONCAT('email', ROW_NUMBER() OVER (), '@', 'localhost.ru')
		, `uuid`
		, 0
		, 0
		, 0
		, 1
	FROM `users`;

	SET percent_to_update = amount_of_rows * 0.2;

	#set validts
	WITH `users_to_update` AS (
		SELECT `uuid`
		, FLOOR(RAND()*(5-1))*1 as `random_integer`
		FROM `users`
		ORDER BY RAND()
		LIMIT percent_to_update
	)
	UPDATE users, `users_to_update`
	SET validts = CASE
		WHEN random_integer = 0 THEN UNIX_TIMESTAMP() + 86400 #expires after 1 day
		WHEN random_integer = 1 THEN UNIX_TIMESTAMP() + 259200 #expires after 3 day
		WHEN random_integer = 2 THEN UNIX_TIMESTAMP() + 864000 #expires after 10 day
		ELSE UNIX_TIMESTAMP() - 864000 #expired
	END
	WHERE users.`uuid` = `users_to_update`.`uuid`;

	SET percent_to_update = amount_of_rows * 0.15;

	#set confirmed
    UPDATE emails
	SET confirmed = 1
	ORDER BY RAND()
	LIMIT percent_to_update;
END;