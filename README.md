## 概要

FC2の新着情報RSS (https://blog.fc2.com/newentry.rdf) からデータを取得し、画面で検索できるWebアプリケーションです。

### 技術詳細                                         
- PHPのフレームワークは使わずに開発
- テンプレートエンジンのBladeを使用
- レイヤードアーキテクチャ
- 環境変数管理やPHPUnitのためcomposerを一部利用

### 機能要件

#### RSS取得
- 5分ごとにFC2の新着情報RSSと記事URLからデータを取得し、MySQLに保存
- ２週間経過したデータは削除される 

保存するデータは  
記事のタイトル、記事の概要、URL、投稿日、ユーザー名、サーバー番号、エントリー番号 です。

ユーザー名、サーバー番号、エントリーNo.はFC2の記事URLのフォーマットを解析して取得しています。  
`http://(ユーザー名).blog(サーバー番号).fc2.com/blog-entry-(エントリーNo.).html`

また、重複データがあった場合は登録しない実装にしています。

#### 検索
- 下記に示す検索条件と表示内容を担保する検索機能の実装

日付、URL、ユーザー名は部分一致  
サーバー番号、エントリーNo.は完全一致 と解釈して実装しました。  
ただし、エントリーNo.は指定したNo.以上の値も検索するか否かをチェックボックスで指定できます。  
また、検索条件はクッキーに保存し、トップページに訪問すると検索フォームに条件が表示されます。
```
検索条件
日付、URL、ユーザー名、サーバー番号、エントリーNo.

表示内容
日付、URL、タイトル、description
```

#### 新着情報RSSの閲覧画面
- 新着順に表示
- ページャー

---

## 導入手順書

`2. 環境構築` の必須項目を上から順番に行ってください。

下記に示す動作環境が用意されている前提ですが  
必要に応じて、後述する `Amazon Linux 2 における PHP, MySQL, Apacheの導入` をご覧ください。

### 1. 動作環境
- PHP 7.3
- MySQL 5.7
- Apache 2.4
- Amazon Linux 2

### 2. 環境構築
Amazon Linux 2での導入を前提とします。

#### 2.1 git clone

サーバーのドキュメントルートに入り
こちらのファイルをプログラムを導入するサーバーでcloneします。  
ここでは`/var/www/html` をドキュメントルートとします。  
ディレクトリが存在しない場合は
```
$ sudo mkdir -p /var/www/html
```

http, sshはお好みで構いません。
```
# http
$ sudo git clone https://RyutaroFukuma@bitbucket.org/RyutaroFukuma/fc2-rss-getter.git

# ssh
$ sudo git clone git@bitbucket.org:RyutaroFukuma/fc2-rss-getter.git

```

gitがインストールされていない場合はyumを使ってインストールします。
```
$ sudo yum install git-all
```

#### 2.2 apacheの設定変更

アップロード先のディレクトリは/etc/httpd/conf/httpd.confにあるDocumentRootを指定するか  
DocumentRootをアップロード先のディレクトリに変更してください。  

Directory Indexをindex.html から index.phpに変更してください。

httpd.confを変更した場合はapacheの再起動も行ってください。
```
# apache再起動
$ sudo service httpd restart
```

権限はchmodコマンドを用いて、適切に設定してください。
```
sudo chmod 775 /var/www/html
```

#### 2.3 composerのインストール(必須)

composerがない場合はインストールが必要です。  
phpdotenvやsimplexml、PHPUnitを使用するために必要です。

```
$ php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ php -r "if (hash_file('sha384', 'composer-setup.php') === 'e0012edf3e80b6978849f5eff0d4b4e4c79ff1609dd1e613307e16318854d24ae64f26d17af3ef0bf7cfb710ca74755a') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$ sudo php composer-setup.php  --install-dir=/usr/local/bin --filename=composer
$ php -r "unlink('composer-setup.php');"
```

既にcomposerが入っている場合はディレクトリに入り、`composer install`してください。
```
$ cd fc2-rss-getter
$ composer install
```

#### 2.4 env作成(必須)

DBの接続情報を管理するため、envファイルを作成します。

中身はDB作成後に指定してください。  
DB_HOSTはホスト名、DB_USERはユーザー名、DB_PASSはパスワード、DB_NAMEは作成するDBの名前になります。
```
$ cp .env.copy .env
$ vim .env

# DB作成後、MySQL接続に必要な情報を記載
DB_HOST=""
DB_USER=""
DB_PASS=""
DB_NAME="rss_db"
```

#### 2.5 DB・テーブル作成

MySQLにログインできることが前提となります。
MySQLが入ってない場合は後述する` Amazon Linux 2 における PHP, MySQL, Apacheの導入` をご覧ください。

DB作成と必要なテーブルの作成
```
CREATE DATABASE IF NOT EXISTS rss_db;

USE rss_reader;

CREATE TABLE `rss_data` (
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
```

#### 2.6 cronの設定(必須)

Linuxにログインできることが前提となります。  
EC2のAmazon Linux 2で設定をしています。  

RSSデータの取得と削除のコマンドを設定します。
```
$ crontab -e

# crontabファイルと同じ内容を記載(ドキュメントルートによってパスが変わるので注意)
```

#### タイムゾーンを'Asia/Tokyo'に変更(任意)

記事の投稿日をdate関数を用いて変換しているため  
サーバーのタイムゾーンを気にする必要があります。  
設定がずれている場合は変更してください。

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
