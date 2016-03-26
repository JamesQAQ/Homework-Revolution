INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES ('user','Auth-Type',':=','CHAP');
INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES ('user','Service-Type',':=','Framed-User');
INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES ('user','Acct-Interim-Interval',':=','60');
INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES ('user','Session-Timeout',':=','3600');
INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES ('user','Idle-Timeout',':=','600');

INSERT INTO `radcheck` (`username`, `attribute`, `op`, `value`) VALUES ('ta', 'Cleartext-Password',':=','tatest');
INSERT INTO `radusergroup` (`username`, `groupname`) VALUES ('ta', 'user');

INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES ('admin','Auth-Type',':=','CHAP');
INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES ('admin','Service-Type',':=','Framed-User');
INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES ('admin','Acct-Interim-Interval',':=','60');

INSERT INTO `radcheck` (`username`, `attribute`, `op`, `value`) VALUES ('admin', 'Cleartext-Password',':=','admin');
INSERT INTO `radusergroup` (`username`, `groupname`) VALUES ('admin', 'admin');

CREATE TABLE IF NOT EXISTS `Sessions` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `SessionID` varchar(200) NOT NULL,
  `username` varchar(64) NOT NULL,
  `LoginTime` int(12) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;