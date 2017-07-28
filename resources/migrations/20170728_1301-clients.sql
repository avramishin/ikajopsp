ALTER TABLE orders
  ADD COLUMN `client_id` VARCHAR(25) NULL
  AFTER `success_url`;
CREATE TABLE clients (
  `id`                 VARCHAR(25) NOT NULL,
  `client_key`         VARCHAR(100),
  `client_pass`        VARCHAR(100),
  `default_currency`   VARCHAR(3) DEFAULT 'USD',
  `default_channel_id` VARCHAR(50),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB;