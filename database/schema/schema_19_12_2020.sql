CREATE TABLE `feed_import_logs`
(
    `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
    `log_type`   enum ('DEBUG','INFO','NOTICE','WARNING','ERROR','CRITICAL','ALERT','EMERGENCY') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `message`    text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `feed_id`    int unsigned                                                                                                                     DEFAULT NULL,
    `created_at` timestamp       NULL                                                                                                             DEFAULT NULL,
    `updated_at` timestamp       NULL                                                                                                             DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `feed_import_logs_feed_id_foreign` (`feed_id`),
    CONSTRAINT `feed_import_logs_feed_id_foreign` FOREIGN KEY (`feed_id`) REFERENCES `feeds` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `feeds`
(
    `id`               int unsigned                                                  NOT NULL AUTO_INCREMENT,
    `created_at`       timestamp                                                     NULL DEFAULT NULL,
    `updated_at`       timestamp                                                     NULL DEFAULT NULL,
    `store_name`       varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `slug`             varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `run_at`           varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `feed_url`         text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NOT NULL,
    `last_import`      timestamp                                                     NULL DEFAULT NULL,
    `import_amount`    int unsigned                                                       DEFAULT NULL,
    `cpc`              double(8, 2)                                                       DEFAULT NULL,
    `is_active`        tinyint(1)                                                         DEFAULT NULL,
    `category_mapping` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `website_id`       int unsigned                                                       DEFAULT NULL,
    `feed_mapping`     json                                                               DEFAULT NULL,
    `clean_at`         text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `network_id`       bigint unsigned                                                    DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `feeds_website_id_foreign` (`website_id`),
    KEY `feeds_network_id_foreign` (`network_id`),
    CONSTRAINT `feeds_network_id_foreign` FOREIGN KEY (`network_id`) REFERENCES `networks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `feeds_website_id_foreign` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `field_mappings`
(
    `id`                bigint unsigned                                               NOT NULL AUTO_INCREMENT,
    `woocommerce_field` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `source_field`      varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `network_id`        bigint unsigned                                                    DEFAULT NULL,
    `created_at`        timestamp                                                     NULL DEFAULT NULL,
    `updated_at`        timestamp                                                     NULL DEFAULT NULL,
    `feed_id`           int unsigned                                                       DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `field_mappings_network_id_foreign` (`network_id`),
    KEY `field_mappings_feed_id_foreign` (`feed_id`),
    CONSTRAINT `field_mappings_feed_id_foreign` FOREIGN KEY (`feed_id`) REFERENCES `feeds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `field_mappings_network_id_foreign` FOREIGN KEY (`network_id`) REFERENCES `networks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `jobs`
(
    `id`           bigint unsigned                                               NOT NULL AUTO_INCREMENT,
    `queue`        varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `payload`      longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `attempts`     tinyint unsigned                                              NOT NULL,
    `reserved_at`  int unsigned DEFAULT NULL,
    `available_at` int unsigned                                                  NOT NULL,
    `created_at`   int unsigned                                                  NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `migrations`
(
    `id`        int unsigned                                                  NOT NULL AUTO_INCREMENT,
    `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `batch`     int                                                           NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `networks`
(
    `id`              bigint unsigned                                               NOT NULL AUTO_INCREMENT,
    `name`            varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `mapping`         json                                                               DEFAULT NULL,
    `fields`          json                                                               DEFAULT NULL,
    `sample_feed_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `created_at`      timestamp                                                     NULL DEFAULT NULL,
    `updated_at`      timestamp                                                     NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `password_resets`
(
    `email`      varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `token`      varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp                                                     NULL DEFAULT NULL,
    KEY `password_resets_email_index` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `rules`
(
    `id`          bigint unsigned                                           NOT NULL AUTO_INCREMENT,
    `syntax`      longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `raw_syntax`  longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `feed_id`     int unsigned                                                   DEFAULT NULL,
    `created_at`  timestamp                                                 NULL DEFAULT NULL,
    `updated_at`  timestamp                                                 NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `rules_feed_id_foreign` (`feed_id`),
    CONSTRAINT `rules_feed_id_foreign` FOREIGN KEY (`feed_id`) REFERENCES `feeds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `users`
(
    `id`             int unsigned                                                  NOT NULL AUTO_INCREMENT,
    `name`           varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `email`          varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `password`       varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `created_at`     timestamp                                                     NULL DEFAULT NULL,
    `updated_at`     timestamp                                                     NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `website_configurations`
(
    `id`         bigint unsigned                                                                 NOT NULL AUTO_INCREMENT,
    `locale`     enum ('nl_NL','en_US','de_DE') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nl_NL',
    `country`    varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                   NOT NULL,
    `created_at` timestamp                                                                       NULL     DEFAULT NULL,
    `updated_at` timestamp                                                                       NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `websites`
(
    `id`               int unsigned                                                  NOT NULL AUTO_INCREMENT,
    `name`             text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NOT NULL,
    `url`              varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `api_key`          longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `api_secret`       longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `api_version`      varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `status`           tinyint(1)                                                    NOT NULL DEFAULT '1',
    `created_at`       timestamp                                                     NULL     DEFAULT NULL,
    `updated_at`       timestamp                                                     NULL     DEFAULT NULL,
    `configuration_id` bigint unsigned                                                        DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `websites_configuration_id_foreign` (`configuration_id`),
    CONSTRAINT `websites_configuration_id_foreign` FOREIGN KEY (`configuration_id`) REFERENCES `website_configurations` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
