create table users (
	id INT primary key auto_increment,
	username VARCHAR(50),
	password VARCHAR(255),
	is_super_admin TINYINT(1) default 0
);

create table product (
 id INT primary key auto_increment,
 nama VARCHAR(100),
 harga INT 
);

create table stok (
 id INT primary key auto_increment,
 id_product INT,
 batch VARCHAR(100) NOT NULL UNIQUE,
 jumlah INT DEFAULT 0,
 tgl_masuk DATE DEFAULT (CURRENT_DATE),
 tgl_exp DATE,
 foreign key (id_product) references product (id)
);

create table transaksi (
	id INT primary key auto_increment,
	total_harga INT,
	tgl_pembelian TIMESTAMP default CURRENT_TIMESTAMP
);

create table detail_transaksi (
	id INT primary key auto_increment,
	id_transaksi INT,
	id_product INT,
	nama_product VARCHAR(100),
	harga_product INT,
	qty INT,
	foreign key (id_transaksi) references transaksi (id) on delete cascade ,
	foreign key (id_product) references product (id) on delete set  null
);
