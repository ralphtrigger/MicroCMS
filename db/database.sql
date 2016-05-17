create database if not exists microcms character set utf8 collate utf8_unicode_ci;
use microcms;

grant all privileges on microcms.* to 'eclipse'@'localhost' identified by 'eclipse';
