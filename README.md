# Homework Revolution(作業革命)

## 環境架設

### LAMP Server

安裝 LAMP Server (Linux-Apache-MySQL-PHP Server)
```
sudo apt-get install tasksel
sudo apt-get update
sudo tasksel install lamp-server
```

安裝 PHPMyAdmin
```
sudo apt-get install phpmyadmin
```
安裝時會有一個針對伺服器自動裝設定檔的選項，記得先按空白鍵選取 apache2 再下一步

(DEBUG用)
在 `/etc/php5/apache2/php.ini` 內
將 `display_error = Off` 改成 `On`

啟用 Apache 的 SSL 功能
```
sudo a2enmod ssl
sudo a2ensite default-ssl
```

重啟 Apache Server
```
sudo service apache2 restart
```

### Freeradius

安裝 Freeradius
```
sudo apt-get install freeradius
```

安裝 Freeradius 的 MySQL 模組
```
sudo apt-get install freeradius-mysql
sudo service freeradius restart
```

### 修改 Ubuntu 中 localhost 的 hotsname 混淆問題
`sudo vim /etc/hosts` 把 `::1 localhost ip6-localhost ip6-loopback` 註解掉

### MySQL

在 MySQL 中新增 radius 資料庫
```
mysql -u root -p
CREATE DATABASE radius;
```


匯入 RADIUS 資料庫架構到 MySQL
```
sudo su
cd /etc/freeradius/sql/mysql/
mysql -u root -p radius < ippool.sql
mysql -u root -p radius < schema.sql
mysql -u root -p radius < nas.sql
mysql -u root -p radius < admin.sql
```
這邊會自動新增一個帳號 `radius/radpass` 給 radius server 登入用

取消 sql 的註解
`sudo vim /etc/freeradius/sites-enabled/default`
`authorize`, `accounting`, `session`, `post-auth` module 內

```
	# ...
	# ...
	# ...
	sql
```

設定
`sudo vim /etc/freeradius/radiusd.conf`
設定 radius server 聽取的埠 (認證埠1812，計量埠1813)
```
#  Port on which to listen.
#  Allowed values are:
#       integer port number (1812)
#       0 means "use /etc/services for the proper port"
port = 1812
```
```
#  This second "listen" section is for listening on the accounting
#  port, too.
#
listen {
        ipaddr = *
#       ipv6addr = ::
        port = 1813
	    type = acct
#       interface = eth0
#       clients = per_socket-clients
}
```

解除 `module` module 內的 include `sql.conf` 的註解
```
        #  Include another file that has the SQL-related configuration.
        #  This is another file only because it tends to be big.
        #
        $INCLUDE sql.conf
```

設定 Log 資訊
```
#  Log the full User-Name attribute, as it was found in the request.
#
# allowed values: {no, yes}
#
stripped_names = yes

#  Log authentication requests to the log file.
#
#  allowed values: {no, yes}
#
auth = yes

#  Log passwords with the authentication requests
#  auth_badpass  - logs password if it's rejected
#  auth_goodpass - logs password if it's correct
#
#  allowrd values: {no, yes}
#
auth_badpass = yes
auth_goodpass = no
```

設定 MySQL 資料
`sudo vim /etc/freeradius/sql.conf`

```
login = "radius"
password = "radpass"

# Database table configuration for everyt
radius db = "radius"
```

設定允許連到 RADIUS Server 的使用者

`sudo vim /etc/freeradius/clients.conf`

要包含 AP 的網路位址
```
client 192.168.182.0/24 {
	secret = testing123
}
```

匯入 admin.sql (Repository 內的)
```
mysql -u radius -p radius < admin.sql
```

測試 Freeradius
```
radtest ta tatest localhost 1 testing123
```

相關指令
```
sudo /etc/init.d/freeradius start 開啟Freeradius
sudo /etc/init.d/freeradius stop 停止Freeradius
sudo /etc/init.d/freeradius restart 重啟Freeradius
```

開啟Freeradius Debug模式
```
sudo /etc/init.d/freeradius stop
sudo freeradius -X
```

查看Freeradius Log 檔
```
sudo tail -n 30 /var/log/freeradius/radius.log
sudo tail -n 30 /var/log/freeradius/radius.log | grep Error
```

### Chillispot

啟動 DD-WRT 上的 Chillispot 服務
設定
Service -> Hostspot -> Chillispot

```
Chillispot:                        Enabled
Separate Wifi from the LAN Bridge: Enabled
Remote Network:                    192.168.182.0/24
Primary Radius Server IP/DNS:      192.168.182.2
Backup Radius Server IP/DNS:       192.168.182.2
DNS IP:                            8.8.8.8
Redirect URL:                      https://192.168.182.2/hotspotlogin.php
Shared Key:                        testing123
UAM Secret:                        wasa
UAM Allowed:                       192.168.182.1

Additional Chillispot Options:
    coaport = 3799
```

### Clone Repository

下載 repository，暴力將所有東西複製到 apache 目錄下(不安全)
```
git clone https://github.com/JamesQAQ/CNLAB.git
cd CNLAB/ex2/
sudo cp -r * /var/www/html/
```
