
-- CREATE DATABASE rt_fasilitas_simple CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE rt_fasilitas_simple;

CREATE TABLE IF NOT EXISTS fasilitas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(120) NOT NULL,
  lokasi VARCHAR(160) DEFAULT NULL,
  stok INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS peminjaman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  warga_nama VARCHAR(120) NOT NULL,
  warga_hp VARCHAR(32) DEFAULT NULL,
  fasilitas_id INT NOT NULL,
  tanggal_pinjam DATE NOT NULL,
  tanggal_kembali DATE NOT NULL,
  CONSTRAINT fk_peminjaman_fasilitas FOREIGN KEY (fasilitas_id) REFERENCES fasilitas(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO fasilitas(nama,lokasi,stok) VALUES
('Kursi Plastik','Gudang RT',100),
('Tenda 3x3','Gudang RW',4),
('Sound System','Rumah Pak RT',1);

INSERT INTO peminjaman(warga_nama,warga_hp,fasilitas_id,tanggal_pinjam,tanggal_kembali) VALUES
('Budi','081234567890',1,'2025-11-01','2025-11-01'),
('Siti','082233445566',2,'2025-11-05','2025-11-06');
