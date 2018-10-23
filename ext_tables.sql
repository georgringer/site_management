CREATE TABLE pages
(
  tx_site_management_feature   varchar(50) DEFAULT '' NOT NULL,
  tx_site_management_demo_tree tinyint(4) DEFAULT 0,
);

CREATE TABLE be_users
(
  tx_site_management_site varchar(50) DEFAULT '' NOT NULL,
);


CREATE TABLE be_groups
(
  tx_site_management_site varchar(50) DEFAULT '' NOT NULL,
);