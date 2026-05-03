CREATE TABLE `users` (
  `id` varchar(255) PRIMARY KEY,
  `name` varchar(255),
  `email` varchar(255) UNIQUE,
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `cash_in` (
  `id` varchar(255) PRIMARY KEY,
  `user_id` varchar(255),
  `title` varchar(255),
  `amount` decimal(12,2),
  `date` date,
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `cash_out` (
  `id` varchar(255) PRIMARY KEY,
  `user_id` varchar(255),
  `title` varchar(255),
  `amount` decimal(12,2),
  `date` date,
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `dena` (
  `id` varchar(255) PRIMARY KEY COMMENT 'Money the user owes to others (Owe)',
  `user_id` varchar(255),
  `name` varchar(255),
  `address` text,
  `mobile` varchar(255),
  `reason` text,
  `amount` decimal(12,2),
  `due_date` date,
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `paona` (
  `id` varchar(255) PRIMARY KEY COMMENT 'Money others owe to the user (Receivable)',
  `user_id` varchar(255),
  `name` varchar(255),
  `address` text,
  `mobile` varchar(255),
  `reason` text,
  `amount` decimal(12,2),
  `due_date` date,
  `created_at` timestamp DEFAULT (now())
);

ALTER TABLE `cash_in` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `cash_out` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `dena` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `paona` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
