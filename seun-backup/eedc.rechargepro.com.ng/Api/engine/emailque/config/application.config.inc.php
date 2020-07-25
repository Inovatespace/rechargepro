<?php
	define("FRONTEND_USER", "user"); // The user name for the frontend
	define("FRONTEND_PASSWORD", "password"); // The password for the frontend

	define("QUEUED_MESSAGES", 40); // Number of last queued messages to show in frontend
	define("LATEST_DELIVERED_MESSAGES", 10); // Number of last delivered messages to show in frontend
	define("LATEST_CANCELLED_MESSAGES", 10); // Number of last cancelled messages to show in frontend
	define("MAXIMUM_DELIVERY_TIMEOUT", 50); // Maximum seconds Emailqueue should spend sending queued emails everytime it's called. Keep this smaller than the amount of time between calls to the delivery script (e.g. If you're calling delivery every 60 seconds, keep this at something like 50 to avoid accumulating instances of Emailqueue eating resources)
	define("DELIVERY_INTERVAL", 10); // Hundredths of a second between each email send. Use this to be more friendly to SMTP servers, should be a balance between being friendly and the number of emails you need to send per seconds in order to keep your queue clean.
	define("MAX_DELIVERS_A_TIME", 1000); // Number of maximum messages to deliver every time delivery script is called. Anyway, this is better controlled with the MAXIMUM_DELIVERY_TIMEOUT configuration above.
	define("SENDING_RETRY_MAX_ATTEMPTS", 3); // Maximum number of attemps to send a message if error is found.
	define("PURGE_OLDER_THAN_DAYS", 5); // Purge messages older than this days from the database. Depending on the amount of emails you send, use this setting to keep your sent emails database to grow too big. It should be a balance between how big would you like to keep your sent emails history and how responsive would you like emailqueue to be. Smaller database will make emailqueue run faster when specially inserting new emails.
	
	define("SEND_METHOD", "sendmail"); // Set it to either "smtp" or "sendmail" to choose the method for delivering emails. If "smtp" is choosen, at least the SMTP_SERVER below must be set.
	define("SMTP_SERVER", "127.0.0.1"); // The IP of the SMTP server
	define("SMTP_PORT", 25); // The port of the SMTP server
	define("SMTP_IS_AUTHENTICATION", false); // True to use SMTP server Authentication
	define("SMTP_AUTHENTICATION_USERNAME", "");
	define("SMTP_AUTHENTICATION_PASSWORD", "");
	
	define("CHARSET", "utf-8"); //Used in Content-Type Email Header
	define("CONTENT_TRANSFER_ENCODING", "8bit"); //Content-Transfer-Encoding Email Header. May be 7bit, 8bit, quoted-printable or base64

	define("PHPMAILER_LANGUAGE", "en");
	
	define("DEFAULT_TIMEZONE", "UTC");

	define("LOGS_DIR", "logs"); // The directory to store logs
	define("LOGS_FILENAME_DATEFORMAT", "Y-m-d"); // The file name format for log files, as a parameter for the PHP date() function
	define("LOGS_DATA_DATEFORMAT", "Y-m-d H:i:s"); // The format of the date as stored inside log files, as a parameter for the PHP date() function

    define("IS_DEVEL_ENVIRONMENT", false); // When set to true, only emails addressed to emails into $devel_emails array are sent

    $devel_emails = array(
        "seuntech2k@yahoo.com"
    );
?>