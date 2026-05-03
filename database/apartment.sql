CREATE TABLE `users` (
  `id` varchar(255) PRIMARY KEY COMMENT 'Unique identifier for the user',
  `name` varchar(255) COMMENT 'Display name of the user',
  `email` varchar(255) UNIQUE COMMENT 'User email address',
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `transactions` (
  `id` varchar(255) PRIMARY KEY,
  `user_id` varchar(255) COMMENT 'Owner of the transaction',
  `title` varchar(255) COMMENT 'Description of the expense or income',
  `amount` decimal(12,2) COMMENT 'Transaction amount',
  `type` varchar(255) COMMENT 'in (Cash In) or out (Cash Out)',
  `date` date COMMENT 'Date of transaction',
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `dues` (
  `id` varchar(255) PRIMARY KEY,
  `user_id` varchar(255) COMMENT 'Owner of the due record',
  `name` varchar(255) COMMENT 'Person associated with the due',
  `address` text COMMENT 'Address of the person',
  `mobile` varchar(255) COMMENT 'Mobile number for contact',
  `reason` text COMMENT 'Purpose of the debt/receivable',
  `amount` decimal(12,2),
  `type` varchar(255) COMMENT 'owe (Dena) or receivable (Paoana)',
  `due_date` date COMMENT 'Expected payment date',
  `created_at` timestamp DEFAULT (now())
);

ALTER TABLE `transactions` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `dues` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
