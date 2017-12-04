-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2017 at 06:25 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sabilulungan`
--

-- --------------------------------------------------------

--
-- Table structure for table `checklist`
--

CREATE TABLE `checklist` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `sequence` int(10) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `type` enum('checkbox','radio','text') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `checklist`
--

INSERT INTO `checklist` (`id`, `role_id`, `sequence`, `name`, `type`) VALUES
(1, 5, 1, 'Proposal Hibah Bansos', 'checkbox'),
(2, 5, 2, 'Surat Keterangan Tanggung Jawab', 'checkbox'),
(3, 5, 3, 'Surat Keterangan Kesediaan Menyediakan Dana Pendamping', 'checkbox'),
(4, 5, 4, 'Gambar Rencana dan Konstruksi Bangunan', 'checkbox'),
(5, 5, 5, '1. Akta Notaris Pendirian Lembaga', 'checkbox'),
(6, 5, 6, '2. Surat Pernyataan Tanggung Jawab', 'checkbox'),
(7, 5, 7, '3. Nomor Pokok Wajib Pajak', 'checkbox'),
(8, 5, 8, '4. Surat Keterangan Domisili Lembaga dari Desa / Kelurahan Setempat', 'checkbox'),
(9, 5, 9, '5. Izin Operasional Tanda Daftar Lembaga dari Instansi yang Berwenang', 'checkbox'),
(10, 5, 10, '6. Bukti Kontrak Sesuai Gedung/Bangunan Bagi Lembaga yang Kantornya Menyewa', 'checkbox'),
(11, 5, 11, '7. Salinan Fotocopy KTP Atas Nama Ketua dan Sekretaris', 'checkbox'),
(12, 5, 12, '8. Salinan Rekening Bank yang Masih Aktif Atas Nama Lembaga dan/atau Pengurus Belanja Hibah', 'checkbox'),
(13, 5, 13, 'Keterangan', 'text'),
(14, 1, 1, 'Keterangan', 'text'),
(15, 3, 1, 'Ya', 'radio'),
(16, 3, 2, 'Tidak', 'radio'),
(17, 3, 3, 'Besar Rekomendasi Dana', 'text'),
(18, 3, 4, '1. Kesesuaian Harga Dalam Proposal dengan Standar Satuan Kerja', 'checkbox'),
(19, 3, 5, '2. Kesesuaian Kebutuhan Peralatan dan Bahan dalam Kegiatan', 'checkbox'),
(20, 3, 6, '3. Organisasi Tidak Fiktif', 'checkbox'),
(21, 3, 7, '4. Alamat Organisasi/Ketua Sesuai dengan Proposal', 'checkbox'),
(22, 3, 8, '5. Belum Pernah Menerima Satu Tahun Sebelumnya', 'checkbox'),
(23, 3, 9, '6. Verifikasi KTP', 'checkbox'),
(24, 3, 10, '7. Verifikasi Organisasi Berbadan Hukum', 'checkbox'),
(25, 3, 11, 'Keterangan', 'text'),
(26, 4, 1, 'Koreksi (Angka)', 'text'),
(27, 4, 2, 'Keterangan', 'text'),
(28, 2, 1, 'Nominal yang Direkomendasikan TAPD', 'text'),
(29, 2, 2, 'Keterangan', 'text'),
(30, 1, 2, 'Keterangan', 'text'),
(31, 4, 1, 'Kategori Hibah Bansos', 'radio');

-- --------------------------------------------------------

--
-- Table structure for table `cms`
--

CREATE TABLE `cms` (
  `page_id` varchar(25) NOT NULL,
  `sequence` int(10) UNSIGNED NOT NULL,
  `title` varchar(250) DEFAULT NULL,
  `content` text NOT NULL,
  `type` enum('1','2','3') NOT NULL COMMENT '1:image, 2:text, 3:file'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms`
--

INSERT INTO `cms` (`page_id`, `sequence`, `title`, `content`, `type`) VALUES
('home', 1, NULL, '6b2dca096e66bf4a8b2136c1ab8e73a4.jpg', '1'),
('home', 2, NULL, 'ad1af5d7a2961274e5b8d429b76dcca3.jpg', '1'),
('peraturan', 1, 'SOP Bendaharan Hibah dan Bantuan Sosial (Repaired)', '01.02 SOP_Bendaharan Hibah dan Bantuan Sosial (Repaired).pdf', '3'),
('peraturan', 2, 'SK PPK-PPKD 2016', 'e160fd3cb66cd95f6a36afe6ece53f0a.doc', '3'),
('peraturan', 3, 'PERMENDAGRI 32 TAHUN 2011', '1d68bd38635b51f1c738e6f88b5e5bf5.pdf', '3'),
('peraturan', 4, 'PERMENDAGRI 39 TAHUN 2012 PERUBAHAN ATAS PERATURAN MENTERI DALAM NEGERI NOMOR 32 TAHUN 2011 TENTANG PEDOMAN PEMBERIAN HIBAH DAN BANTUAN SOSIAL YANG BERSUMBER DARI ANGGARAN PENDAPATAN DAN BELANJA DAERAH', '5a3191e98ca3da46b4e36491ea7e6c86.pdf', '3'),
('peraturan', 5, 'PERWAL NO 891 TAHUN 2011 TENTANG HIBAH BANSOS', 'c2dfcba5ab0fa9417b9b1f569931af7e.pdf', '3'),
('peraturan', 6, 'PERWAL NO 836 THN 2012 PERUBAHAN I PERWAL 891-2011 HIBAH BANSOS', '5b396c4c46b2ac1516042f9df33fcdd5.pdf', '3'),
('peraturan', 7, 'PERWAL NO 777 THN 2013 PERUBAHAN II PERWAL 891-2011 HIBAH BANSOS', 'dd67e7bac65d24c9833b07ac980c3a9e.pdf', '3'),
('peraturan', 8, 'PERWAL NO. 825 THN 2013 PERUBAHAN III PERWAL 891-2011 HIBAH BANSOS-evdok', '50e1acf5536ff8d403d07bd796817c77.pdf', '3'),
('peraturan', 9, 'PERWAL NO. 825 THN 2013 PERUBAHAN III PERWAL 891-2011 HIBAH BANSOS LAMPIRAN', '6f6da3e750f3985cac1e715a4399bc42.pdf', '3'),
('peraturan', 10, 'PERWAL NO 1205 THN 2013 PERUBAHAN IV PERWAL 891-2011 HIBAH BANSOS', '5d6c162aeff5302cedf1ef43037c42a5.pdf', '3'),
('peraturan', 11, 'PERWAL NO. 309 THN 2014 PERUBAHAN V PERWAL 891-2011 HIBAH BANSOS', '22bc1773be445df6fabd5584d5415694.pdf', '3'),
('peraturan', 12, 'PERWAL NO. 691 THN 2014 PERUBAHAN V PERWAL 891-2011 HIBAH BANSOS', '7d83fc75dd4399e68bf4b2245367566b.pdf', '3'),
('peraturan', 13, 'Hibah Bansos Online', '766d8fd943c05b384e6718396589c3c0.docx', '3'),
('peraturan', 14, 'Peraturan Walikota Nomor 816 Tahun 2015', '790a40427091ac801345fd09747387c8.pdf', '3'),
('peraturan', 15, 'SURAT EDARAN LPJ 2015', '7d50d38defe05ad8ba5272788a11fa6c.docx', '3'),
('peraturan', 16, 'Surat Edaran Menteri Dalam Negeri Nomor 9004627SJ Tentang Penajaman Ketentuan Pasal 298 Ayat (5) Undang-Undang Nomor 23 Tahun 2014 Tentang Pemerintahan Daerah', '8597f9c4903623f66c0e90de1d74bf90.pdf', '3'),
('peraturan', 17, 'Surat Permberitahuan Pemohon', '06e124370babc1f2a3e09d267a50c9c5.docx', '3'),
('peraturan', 18, 'Surat Permberitahuan SKPD Terkait', '6225452be03b620f44f9712313c5ee06.docx', '3'),
('tentang', 1, NULL, '<p>Sabilulungan, atau yang memiliki arti ‘Gotong Royong’ digagas oleh Pemerintah Kota Bandung untuk memfasilitasi keterbukaan dalam perwujudan program bansos dan hibah melalui media online.</p>\r\n<p>Program Sabilulungan bertujuan agar jalannya dana bantuan yang diturunkan Pemerintah Kota Bandung untuk membiayai berbagai proyek sosial yang diinginkan masyarakat dapat dipertanggungjawabkan secara terbuka. Seluruh proses dalam Sabilulungan dapat terlihat dan diawasi oleh seluruh lapisan masyarakat</p>\r\n<p>Melalui Sabilulungan, seluruh masyarakat dan organisasi di kota Bandung dapat:</p>\r\n<ol><li><p>Mengirimkan proposal terkait hibah bansos dan memonitor bagaimana status proposal tersebut (apakah diterima, ditolak, sedang diverifikasi, dan sebagainya); serta</p>\r\n</li>\r\n<li><p>Ikut berpartisipasi dalam memonitor jalannya hibah bansos yang sudah disetujui oleh Pemerintah Kota Bandung sehingga dapat turut memberikan masukan dan saran terkait jalannya hibah bansos tersebut.</p>\r\n</li>\r\n</ol>\r\n<p>Ayo ajukan ide kreatif kalian tanpa rasa takut akan penyelewengan dana yang diturunkan. Kita semua bersama dapat menjadi yang ahli dalam pembangunan Kota Bandung, Karena berani transparansi itu JUARA!</p>\r\n<h2>APA YANG SABILULUNGAN WUJUDKAN</h2>\r\n<p>BANSOS atau Bantuan Sosial, yaitu program bantuan dana diberikan secara selektif oleh pemerintah untuk ide-ide kreatif yang diusulkan oleh seluruh masyarakat Kota Bandung khususnya, secara perseorangan atau kelompok. Bantuan Sosial, bersifat sementara, tidak terus-menerus, tidak mengikat dan tidak wajib.</p>\r\n<p>HIBAH, yaitu program bantuan dana berkelanjutan dan terikat yang diberikan oleh pemerintah untuk setiap pengajuan proyek kreatif dari Lembaga Sosial Masyarakat (Non-Government Organitation atau NGO).</p>\r\n<h2>TAHAPAN SABILULUNGAN</h2>\r\n<p>Setiap masyarakat atau organisasi di kota Bandung yang ingin mengajukan proposal hibah bansos melalui Sabilulungan cukup mendaftarkan melalui aplikasi dan mengirimkan kelengkapan dokumen secara langsung, setelah itu Pemerintah Kota Bandung akan memverifikasi. Tahapan verifikasi selengkapnya sebagai berikut:</p>\r\n<ol><li><p>Masyarakat mendaftarkan proposal hibah bansos secara online melalui aplikasi Sabilulungan</p>\r\n</li>\r\n<li><p>Masyarakat menyerahkan kelengkapan dokumen secara langsung kepada Pemerintah Kota Bandung</p>\r\n</li>\r\n<li><p>TU Pimpinan akan memverifikasi kelengkapan proposal dan dokumen pendukung proposal tersebut</p>\r\n</li>\r\n<li><p>Walikota/wakil walikota akan memverifikasi proposal tersebut</p>\r\n</li>\r\n<li><p>Tim pertimbangan akan memverifikasi proposal dan mendisposisi proposal kepada SKPD (Satuan Kerja Perangkat Daerah) terkait</p>\r\n</li>\r\n<li><p>SKPD terkait akan memeriksa proposal tersebut dan memberikan rekomendasi besaran dana yang diusulkan untuk diberikan</p>\r\n</li>\r\n<li><p>Tim pertimbangan akan memeriksa usulan dana dari SKPD dan juga memberikan rekomendasi besaran dana yang diusulkan untuk diberikan</p>\r\n</li>\r\n<li><p>TAPD (Tim Anggaran Pemerintah Daerah) akan memeriksa usulan dana dari SKPD dan tim pertimbangan untuk kemudian memberikan rekomendasi final dana yang akan diberikan. Selanjutnya, seluruh proposal yang diberikan rekomendasi dana akan dikumpulkan dalam dokumen Daftar Nominatif Calon Penerima Belanja Hibah (DNC PBH)</p>\r\n</li>\r\n<li><p>Walikota/wakil walikota akan memeriksa DNC PBH. Apabila disetujui maka proposal-proposal yang termasuk dalam DNC PBH tersebut siap berjalan</p>\r\n</li>\r\n</ol>\r\n', '2'),
('tentang', 2, NULL, '1ed40192f33a9a88fc2d166b11b079f6.jpg', '1'),
('tentang', 3, NULL, '32596594590ef13591cc4c4a403dd69b.jpg', '1');

-- --------------------------------------------------------

--
-- Table structure for table `flow`
--

CREATE TABLE `flow` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `sequence` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `flow`
--

INSERT INTO `flow` (`id`, `name`, `role_id`, `sequence`) VALUES
(1, 'Pemeriksaan Kelengkapan oleh Bagian TU', 5, 1),
(2, 'Pemeriksaan oleh Walikota', 1, 2),
(3, 'Klasifikasi sesuai SKPD oleh Tim Pertimbangan', 4, 3),
(4, 'Rekomendasi Dana oleh SKPD', 3, 4),
(5, 'Verifikasi Proposal oleh Tim Pertimbangan', 4, 5),
(6, 'Verifikasi Proposal oleh TAPD', 2, 6),
(7, 'Proyek Berjalan', 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `log_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `activity` varchar(25) NOT NULL,
  `id` varchar(25) DEFAULT NULL,
  `ip` varchar(25) NOT NULL,
  `time_entry` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`log_id`, `user_id`, `activity`, `id`, `ip`, `time_entry`) VALUES
(1, 1, 'login', NULL, '::1', '2017-09-13 04:24:31'),
(2, 1, 'logout', NULL, '::1', '2017-09-13 04:24:51');

-- --------------------------------------------------------

--
-- Table structure for table `proposal`
--

CREATE TABLE `proposal` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `user` varchar(256) DEFAULT NULL,
  `name` varchar(256) NOT NULL,
  `judul` varchar(256) NOT NULL,
  `latar_belakang` text NOT NULL,
  `maksud_tujuan` text NOT NULL,
  `address` varchar(512) NOT NULL,
  `file` varchar(64) DEFAULT NULL,
  `nphd` varchar(64) DEFAULT NULL,
  `foto` varchar(64) DEFAULT NULL,
  `type_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `skpd_id` int(10) UNSIGNED DEFAULT NULL,
  `time_entry` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_lpj` date DEFAULT NULL,
  `current_stat` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_approval`
--

CREATE TABLE `proposal_approval` (
  `proposal_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `flow_id` int(10) UNSIGNED DEFAULT NULL,
  `action` enum('1','2') NOT NULL COMMENT '1=Approve, 2=Reject',
  `time_entry` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_approval_history`
--

CREATE TABLE `proposal_approval_history` (
  `proposal_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `flow_id` int(10) UNSIGNED DEFAULT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `action` enum('1','2') NOT NULL COMMENT '1=Approve, 2=Reject',
  `time_entry` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_checklist`
--

CREATE TABLE `proposal_checklist` (
  `proposal_id` int(10) UNSIGNED NOT NULL,
  `checklist_id` int(10) UNSIGNED NOT NULL,
  `value` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_dana`
--

CREATE TABLE `proposal_dana` (
  `proposal_id` int(10) UNSIGNED NOT NULL,
  `sequence` int(10) UNSIGNED NOT NULL,
  `description` varchar(256) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `correction` decimal(18,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_lpj`
--

CREATE TABLE `proposal_lpj` (
  `proposal_id` int(10) UNSIGNED NOT NULL,
  `sequence` int(10) UNSIGNED NOT NULL,
  `path` varchar(64) NOT NULL,
  `type` enum('1','2') NOT NULL COMMENT '1:image, 2:video'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_photo`
--

CREATE TABLE `proposal_photo` (
  `proposal_id` int(10) UNSIGNED NOT NULL,
  `sequence` int(10) UNSIGNED NOT NULL,
  `path` varchar(64) NOT NULL,
  `is_nphd` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_type`
--

CREATE TABLE `proposal_type` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `proposal_type`
--

INSERT INTO `proposal_type` (`id`, `name`) VALUES
(1, 'Hibah'),
(2, 'Bansos');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'Walikota'),
(2, 'Tim Panitia Anggaran Daerah'),
(3, 'SKPD'),
(4, 'Tim Pertimbangan'),
(5, 'Tata Usaha'),
(6, 'Warga'),
(7, 'Administrator'),
(8, 'Operator'),
(9, 'Super Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `skpd`
--

CREATE TABLE `skpd` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `skpd`
--

INSERT INTO `skpd` (`id`, `name`) VALUES
(1, 'Perencanaan Pembangunan'),
(2, 'Lingkungan Hidup'),
(3, 'Pemberdayaan Perempuan dan Perlindungan Anak'),
(4, 'Kesatuan Bangsa dan Politik dalam Negeri'),
(5, 'Penanaman Modal'),
(6, 'Pendidikan'),
(7, 'Kesehatan'),
(8, 'Pekerjaan Umum Bidang Jalan dan Jemabatan'),
(9, 'Perumahan dan Urusan Penataan Ruang'),
(10, 'Perhubungan'),
(11, 'Kependudukan dan Catatn Sipil'),
(12, 'Sosial, Keagamaan/Peribadatan dan Pendidikan Agama'),
(13, 'Kesejahteraan Sosial'),
(14, 'Ketenagakerjaan'),
(15, 'Koperasi dan Usaha Kecil Menengah'),
(16, 'Kepemudaan dan Olah Raga Non Profesional'),
(17, 'Kebudayaan dan Adat Istiadat, Pariwisata dan Kesenian'),
(18, 'Komunikasi dan Informatika'),
(19, 'Pertanian'),
(20, 'Otonomi Daerah dan Pemerintahan Umum'),
(21, 'Perusahaan Daerah dan Perekonomian'),
(22, 'Kearsipan');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  `address` varchar(512) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `ktp` varchar(64) DEFAULT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_skpd` bit(1) NOT NULL DEFAULT b'0',
  `skpd_id` int(10) UNSIGNED DEFAULT NULL,
  `time_entry` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `address`, `phone`, `ktp`, `username`, `password`, `role_id`, `is_active`, `is_skpd`, `skpd_id`, `time_entry`) VALUES
(1, 'Ilham Muttaqien', 'mt.ilham@gmail.com', 'Banjaran', '089657312085', '1234567890', 'immuttaqien', '6079cca0923e90ff1eb3c45dd0391385', 6, 1, b'0', NULL, '2016-01-19 04:46:56'),
(2, 'Tata Usaha', 'tatausaha@gmail.com', 'Bandung', '081234567890', NULL, 'tatausaha', '6079cca0923e90ff1eb3c45dd0391385', 5, 1, b'0', NULL, '2016-01-19 04:46:56'),
(3, 'Tim Pertimbangan', 'pertimbangan@gmail.com', 'Bandung', '081234567890', NULL, 'pertimbangan', '6079cca0923e90ff1eb3c45dd0391385', 4, 1, b'0', NULL, '2016-01-24 19:38:52'),
(4, 'SKPD', 'skpd@gmail.com', 'Bandung', '081234567890', NULL, 'skpd', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'0', NULL, '2016-01-24 19:38:52'),
(5, 'Tim Panitia Anggaran Daerah', 'tapd@gmail.com', 'Bandung', '081234567890', NULL, 'tapd', '6079cca0923e90ff1eb3c45dd0391385', 2, 1, b'0', NULL, '2016-01-24 19:38:52'),
(6, 'Walikota', 'walikota@gmail.com', 'Bandung', '081234567890', NULL, 'walikota', '6079cca0923e90ff1eb3c45dd0391385', 1, 1, b'0', NULL, '2016-01-24 19:38:52'),
(7, 'Administrator', 'admin@gmail.com', 'Bandung', '081234567890', NULL, 'admin', '6079cca0923e90ff1eb3c45dd0391385', 7, 1, b'0', NULL, '2016-01-24 19:38:52'),
(8, 'Operator', 'operator@gmail.com', 'Bandung', '081234567890', NULL, 'operator', '6079cca0923e90ff1eb3c45dd0391385', 8, 1, b'0', NULL, '2016-01-24 19:38:52'),
(9, 'SKPD Perencanaan Pembangunan', NULL, 'Bandung', NULL, NULL, 'perencanaan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 1, '2016-01-24 19:38:52'),
(10, 'SKPD Lingkungan Hidup', NULL, 'Bandung', NULL, NULL, 'lingkungan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 2, '2016-01-24 19:38:52'),
(11, 'SKPD Pemberdayaan Perempuan dan Perlindungan Anak', NULL, 'Bandung', NULL, NULL, 'pemberdayaan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 3, '2016-01-24 19:38:52'),
(12, 'SKPD Kesatuan Bangsa dan Politik dalam Negeri', NULL, 'Bandung', NULL, NULL, 'kesatuan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 4, '2016-01-24 19:38:52'),
(13, 'SKPD Penanaman Modal', NULL, 'Bandung', NULL, NULL, 'penanaman', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 5, '2016-01-24 19:38:52'),
(14, 'SKPD Pendidikan', NULL, 'Bandung', NULL, NULL, 'pendidikan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 6, '2016-01-24 19:38:52'),
(15, 'SKPD Kesehatan', NULL, 'Bandung', NULL, NULL, 'kesehatan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 7, '2016-01-24 19:38:52'),
(16, 'SKPD Pekerjaan Umum Bidang Jalan dan Jemabatan', NULL, 'Bandung', NULL, NULL, 'pekerjaan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 8, '2016-01-24 19:38:52'),
(17, 'SKPD Perumahan dan Urusan Penataan Ruang', NULL, 'Bandung', NULL, NULL, 'perumahan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 9, '2016-01-24 19:38:52'),
(18, 'SKPD Perhubungan', NULL, 'Bandung', NULL, NULL, 'perhubungan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 10, '2016-01-24 19:38:52'),
(19, 'SKPD Kependudukan dan Catatn Sipil', NULL, 'Bandung', NULL, NULL, 'kependudukan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 11, '2016-01-24 19:38:52'),
(20, 'SKPD Sosial, Keagamaan/Peribadatan dan Pendidikan Agama', NULL, 'Bandung', NULL, NULL, 'sosial', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 12, '2016-01-24 19:38:52'),
(21, 'SKPD Kesejahteraan Sosial', NULL, 'Bandung', NULL, NULL, 'kesejahteraan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 13, '2016-01-24 19:38:52'),
(22, 'SKPD Ketenagakerjaan', NULL, 'Bandung', NULL, NULL, 'ketenagakerjaan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 14, '2016-01-24 19:38:52'),
(23, 'SKPD Koperasi dan Usaha Kecil Menengah', NULL, 'Bandung', NULL, NULL, 'koperasi', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 15, '2016-01-24 19:38:52'),
(24, 'SKPD Kepemudaan dan Olah Raga Non Profesional', NULL, 'Bandung', NULL, NULL, 'kepemudaan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 16, '2016-01-24 19:38:52'),
(25, 'SKPD Kebudayaan dan Adat Istiadat, Pariwisata dan Kesenian', NULL, 'Bandung', NULL, NULL, 'kebudayaan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 17, '2016-01-24 19:38:52'),
(26, 'SKPD Komunikasi dan Informatika', NULL, 'Bandung', NULL, NULL, 'komunikasi', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 18, '2016-01-24 19:38:52'),
(27, 'SKPD Pertanian', NULL, 'Bandung', NULL, NULL, 'pertanian', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 19, '2016-01-24 19:38:52'),
(28, 'SKPD Otonomi Daerah dan Pemerintahan Umum', NULL, 'Bandung', NULL, NULL, 'otonomi', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 20, '2016-01-24 19:38:52'),
(29, 'SKPD Perusahaan Daerah dan Perekonomian', NULL, 'Bandung', NULL, NULL, 'perusahaan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 21, '2016-01-24 19:38:52'),
(30, 'SKPD Kearsipan', NULL, 'Bandung', NULL, NULL, 'kearsipan', '6079cca0923e90ff1eb3c45dd0391385', 3, 1, b'1', 22, '2016-01-24 19:38:52'),
(31, 'Super Administrator', NULL, '', NULL, NULL, 'superadmin', '6079cca0923e90ff1eb3c45dd0391385', 9, 1, b'0', NULL, '2016-02-24 11:09:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checklist`
--
ALTER TABLE `checklist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `cms`
--
ALTER TABLE `cms`
  ADD UNIQUE KEY `page_id` (`page_id`,`sequence`);

--
-- Indexes for table `flow`
--
ALTER TABLE `flow`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sequence` (`sequence`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `proposal`
--
ALTER TABLE `proposal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `skpd_id` (`skpd_id`),
  ADD KEY `current_stat` (`current_stat`);

--
-- Indexes for table `proposal_approval`
--
ALTER TABLE `proposal_approval`
  ADD KEY `proposal_id` (`proposal_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `flow_id` (`flow_id`);

--
-- Indexes for table `proposal_approval_history`
--
ALTER TABLE `proposal_approval_history`
  ADD KEY `proposal_id` (`proposal_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `flow_id` (`flow_id`);

--
-- Indexes for table `proposal_checklist`
--
ALTER TABLE `proposal_checklist`
  ADD UNIQUE KEY `proposal_id` (`proposal_id`,`checklist_id`),
  ADD KEY `checklist_id` (`checklist_id`);

--
-- Indexes for table `proposal_dana`
--
ALTER TABLE `proposal_dana`
  ADD UNIQUE KEY `proposal_id` (`proposal_id`,`sequence`);

--
-- Indexes for table `proposal_lpj`
--
ALTER TABLE `proposal_lpj`
  ADD UNIQUE KEY `proposal_id` (`proposal_id`,`sequence`);

--
-- Indexes for table `proposal_photo`
--
ALTER TABLE `proposal_photo`
  ADD UNIQUE KEY `proposal_id` (`proposal_id`,`sequence`);

--
-- Indexes for table `proposal_type`
--
ALTER TABLE `proposal_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `skpd`
--
ALTER TABLE `skpd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `ktp` (`ktp`),
  ADD KEY `skpd_id` (`skpd_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checklist`
--
ALTER TABLE `checklist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `flow`
--
ALTER TABLE `flow`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `proposal`
--
ALTER TABLE `proposal`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `proposal_type`
--
ALTER TABLE `proposal_type`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `skpd`
--
ALTER TABLE `skpd`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `checklist`
--
ALTER TABLE `checklist`
  ADD CONSTRAINT `checklist_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `flow`
--
ALTER TABLE `flow`
  ADD CONSTRAINT `flow_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `proposal`
--
ALTER TABLE `proposal`
  ADD CONSTRAINT `proposal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `proposal_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `proposal_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `proposal_ibfk_3` FOREIGN KEY (`skpd_id`) REFERENCES `skpd` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `proposal_ibfk_4` FOREIGN KEY (`current_stat`) REFERENCES `flow` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `proposal_approval`
--
ALTER TABLE `proposal_approval`
  ADD CONSTRAINT `proposal_approval_ibfk_1` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `proposal_approval_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `proposal_approval_ibfk_3` FOREIGN KEY (`flow_id`) REFERENCES `flow` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proposal_approval_history`
--
ALTER TABLE `proposal_approval_history`
  ADD CONSTRAINT `proposal_approval_history_ibfk_1` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `proposal_approval_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `proposal_approval_history_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `proposal_approval_history_ibfk_4` FOREIGN KEY (`flow_id`) REFERENCES `flow` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proposal_checklist`
--
ALTER TABLE `proposal_checklist`
  ADD CONSTRAINT `proposal_checklist_ibfk_1` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `proposal_checklist_ibfk_2` FOREIGN KEY (`checklist_id`) REFERENCES `checklist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proposal_dana`
--
ALTER TABLE `proposal_dana`
  ADD CONSTRAINT `proposal_dana_ibfk_1` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proposal_lpj`
--
ALTER TABLE `proposal_lpj`
  ADD CONSTRAINT `proposal_lpj_ibfk_1` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proposal_photo`
--
ALTER TABLE `proposal_photo`
  ADD CONSTRAINT `proposal_photo_ibfk_1` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`skpd_id`) REFERENCES `skpd` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
