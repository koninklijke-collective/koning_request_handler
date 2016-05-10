#
# Table structure for table 'tx_koningrequesthandler_domain_model_request'
#
CREATE TABLE tx_koningrequesthandler_domain_model_request (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    identifier varchar(40) DEFAULT '' NOT NULL,
    running varchar(32) DEFAULT '' NOT NULL,
    target varchar(1024) DEFAULT '' NOT NULL,
    parameters text,
    persistent tinyint(4) DEFAULT '0',

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY running (running),
    KEY unique_hash (identifier)
);