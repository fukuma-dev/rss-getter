## 概要

FC2の新着情報RSS (https://blog.fc2.com/newentry.rdf) からデータを取得し、画面で検索できるWebアプリケーションです。

### 要件

検索条件
- 日付、URL、ユーザー名、サーバー番号、エントリーNo.

表示内容
- 日付、URL、タイトル、descriptionです。

表示機能
- 新着順に表示
- ページャー

FC2のURLのフォーマット  
`http://(ユーザー名).blog(サーバー番号).fc2.com/blog-entry-(エントリーNo.).html`
                                                              
### 技術詳細
                                                   
- PHPのフレームワークは使わずに開発
- テンプレートエンジンのBladeを使用
- 環境変数の管理にcomposer経由でdotenvを利用


## 導入

### 動作環境
- PHP 7.3
- MySQL 5.7
- Apache 2.4
- Amazon Linux 2

### 環境構築

.env作成
```
$ cp .env.copy .env
$ vim .env

# DB作成後、必要な情報を記載
DB_HOST=""
DB_USER=""
DB_PASS=""
DB_NAME=""
```

DB・テーブル作成
```
CREATE DATABASE IF NOT EXISTS rss_db;

USE rss_db;

CREATE TABLE `fc2_rss_feed` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) DEFAULT NULL COMMENT '記事のタイトル',
  `description` varchar(255) DEFAULT NULL COMMENT '記事の概要',
  `url` varchar(255) DEFAULT NULL COMMENT 'URL',
  `post_datetime` datetime COMMENT '投稿日',
  `user_name` varchar(255) DEFAULT NULL COMMENT 'ユーザー名',
  `server_number` int DEFAULT NULL COMMENT 'サーバー番号',
  `entry_number` int DEFAULT NULL COMMENT 'エントリー番号',
  `create_date` datetime NOT NULL COMMENT '作成日時',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2729 DEFAULT CHARSET=utf8mb4 COMMENT='RSSデータ';
｝｝
```

simple-xmlのインストール
```
yum -y install --enablerepo=remi,epel,remi-php70 php php-devel php-intl php-mbstring php-pdo php-gd php-mysqlnd php-xml
```

composerのインストール
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e0012edf3e80b6978849f5eff0d4b4e4c79ff1609dd1e613307e16318854d24ae64f26d17af3ef0bf7cfb710ca74755a') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php composer-setup.php  --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
```

phpdotenv
```
composer require vlucas/phpdotenv
```


タイムゾーンを'Asia/Tokyo'に変更

php.ini
```
$ vim /etc/php.ini

[Date]
; Defines the default timezone used by the date functions
; http://php.net/date.timezone
;date.timezone = 'Asia/Tokyo'

$ sudo service httpd restart
```

システム
```
$ sudo vim /etc/sysconfig/clock

ZONE="Asia/Tokyo"
UTF=false

$ sudo ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime

$ sudo service crond restart
```

### Amazon Linux 2 における PHP, MySQL, Apacheの導入(必要に応じて)

#### PHP 7.3
```
# PHP 7.3がインストールできるか確認
$ amazon-linux-extras info php7.3

# インストール
$ sudo amazon-linux-extras install php7.3

# インストールができたか確認
$ amazon-linux-extras
→ 7.3 enabled　になっていればOK

$ php --version

PHP 7.3.17 (cli) (built: May  5 2020 19:30:20) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.3.17, Copyright (c) 1998-2018 Zend Technologies
```

#### MySQL 5.7
```
# MySQL 5.7 インストール
$ sudo yum install http://dev.mysql.com/get/mysql57-community-release-el7-7.noarch.rpm
$ sudo yum install --enablerepo=mysql57-community mysql-community-server

$ mysql --version
mysql  Ver 14.14 Distrib 5.7.30, for Linux (x86_64) using  EditLine wrapper

# 自動起動設定
$ sudo chkconfig mysqld on
情報:'systemctl enable mysqld.service'へ転送しています。

# 起動
$ sudo service mysqld start
Redirecting to /bin/systemctl start mysqld.service

# 5.7ではデフォルトのパスワードが与えられる
$ sudo cat /var/log/mysqld.log | grep "temporary password"
2020-05-30T15:45:22.929075Z 1 [Note] A temporary password is generated for root@localhost: `********`

# パスワード変更
$ set password for root@localhost=password('********');
```

#### Apache
```
$ sudo yum -y install httpd
$ sudo service httpd start

$ sudo vim /etc/httpd/conf/httpd.conf

# DirectoryIndex を index.html から index.php に変更

# 設定ファイルを変更したら再起動
$ sudo service httpd restart
```
