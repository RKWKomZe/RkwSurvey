#
# Table structure for table 'tx_rkwsurvey_domain_model_survey'
#
CREATE TABLE tx_rkwsurvey_domain_model_survey (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	starttext text NOT NULL,
	endtext text NOT NULL,
	question int(11) unsigned DEFAULT '0' NOT NULL,
    topics int(11) unsigned DEFAULT '0' NOT NULL,
	admin varchar(255) DEFAULT '' NOT NULL,
	token int(11) unsigned DEFAULT '0' NOT NULL,
	access_restricted tinyint(4) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_rkwsurvey_domain_model_topic'
#
CREATE TABLE tx_rkwsurvey_domain_model_topic (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	survey int(11) unsigned DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	short_name varchar(45) DEFAULT '' NOT NULL,
	questions int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)

);


#
# Table structure for table 'tx_rkwsurvey_domain_model_question'
#
CREATE TABLE tx_rkwsurvey_domain_model_question (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	question varchar(255) DEFAULT '' NOT NULL,
	short_name varchar(45) DEFAULT '' NOT NULL,
	hint varchar(255) DEFAULT '' NOT NULL,
	required int(11) DEFAULT '0' NOT NULL,
	group_by int(11) DEFAULT '0' NOT NULL,
	benchmark int(11) DEFAULT '0' NOT NULL,
	benchmark_value double DEFAULT '0' NOT NULL,
	benchmark_weighting varchar(255) DEFAULT '' NOT NULL,
	answer_option text NOT NULL,
	survey int(11) unsigned DEFAULT '0',
	topic int(11) unsigned DEFAULT '0' NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	text_consent varchar(255) DEFAULT '' NOT NULL,
	text_rejection varchar(255) DEFAULT '' NOT NULL,
	scale_from_points int(11) DEFAULT '0' NOT NULL,
	scale_to_points int(11) DEFAULT '0' NOT NULL,
    scale_step int(11) DEFAULT '0' NOT NULL,

	do_action tinyint(4) unsigned DEFAULT '0' NOT NULL,
	do_action_if tinyint(4) unsigned DEFAULT '0' NOT NULL,
	do_action_jump tinyint(4) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_rkwsurvey_domain_model_surveyresult'
#
CREATE TABLE tx_rkwsurvey_domain_model_surveyresult (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	finished int(11) DEFAULT '0' NOT NULL,
	survey int(11) unsigned DEFAULT '0',
	question_result int(11) unsigned DEFAULT '0' NOT NULL,
	token int(11) unsigned DEFAULT '0',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for table 'tx_rkwsurvey_domain_model_questionresult'
#
CREATE TABLE tx_rkwsurvey_domain_model_questionresult (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	answer text NOT NULL,
	survey_result int(11) unsigned DEFAULT '0',
	question int(11) unsigned DEFAULT '0' NOT NULL,
	skipped tinyint(4) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,


	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for table 'tx_rkwsurvey_domain_model_token'
#
CREATE TABLE tx_rkwsurvey_domain_model_token (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name text NOT NULL,
	used int(11) unsigned DEFAULT '0',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for table 'tx_rkwsurvey_domain_model_question'
#
CREATE TABLE tx_rkwsurvey_domain_model_question (

	survey int(11) unsigned DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'tx_rkwsurvey_domain_model_token'
#
CREATE TABLE tx_rkwsurvey_domain_model_token (

	survey int(11) unsigned DEFAULT '0' NOT NULL,

);