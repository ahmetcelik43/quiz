CREATE TABLE IF NOT EXISTS roller (
 id int(11) NOT NULL AUTO_INCREMENT,
 rol varchar(50) NOT NULL,
 PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS uyeler (
 id int(11) NOT NULL AUTO_INCREMENT,
 kullaniciAdi varchar(50) NOT NULL,
 adSoyad varchar(50) NOT NULL,
 sifre varchar(255) NOT NULL,
 posta varchar(20),
 telefon varchar(20),
 rolID int(11),
 FOREIGN key (rolID) REFERENCES roller(id),
 PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;