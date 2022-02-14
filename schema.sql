-- TODO: write raw SQL queries to create a DB for storing results

CREATE TABLE `batch` (
                         `mid` decimal(18,0) NOT NULL,
                         `batch_date` date NOT NULL,
                         `batch_ref_num` decimal(24,0) NOT NULL,
                         `import_id` varchar(32) NOT NULL DEFAULT '',
                         PRIMARY KEY (`mid`,`batch_date`,`batch_ref_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `import_history` (
                                  `id` varchar(32) NOT NULL DEFAULT '',
                                  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `merchant` (
                            `mid` decimal(18,0) NOT NULL,
                            `dba` varchar(100) NOT NULL DEFAULT '',
                            `import_id` varchar(32) NOT NULL DEFAULT '',
                            PRIMARY KEY (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `transaction` (
                               `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                               `mid` decimal(18,0) NOT NULL,
                               `batch_date` date NOT NULL,
                               `batch_ref_num` decimal(24,0) NOT NULL,
                               `trans_date` date NOT NULL,
                               `trans_type` varchar(20) NOT NULL DEFAULT '',
                               `trans_card_type` varchar(2) NOT NULL DEFAULT '',
                               `trans_card_num` varchar(20) NOT NULL DEFAULT '',
                               `trans_amount` decimal(10,2) NOT NULL,
                               `import_id` varchar(32) NOT NULL DEFAULT '',
                               PRIMARY KEY (`id`),
                               KEY `mid` (`mid`,`batch_date`,`batch_ref_num`),
                               KEY `dba` (`batch_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
