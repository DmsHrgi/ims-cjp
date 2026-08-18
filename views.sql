-- Stand-in structure for view `view_aktif_kota`
-- (See below for the actual view)
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_bandwith`
-- (See below for the actual view)
--
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_barang`
-- (See below for the actual view)
--
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_batchjob`
-- (See below for the actual view)
--
CREATE TABLE `view_batchjob` (
`nomor_internet` varchar(50)
,`status_reg` varchar(5)
,`desc_registrasi` text
,`nik_penduduk` varchar(100)
,`id_perusahaan` varchar(100)
,`nama_pelanggan` varchar(200)
,`nama_perusahaan` varchar(255)
,`no_telp_perusahaan` varchar(30)
,`email_perusahaan` varchar(150)
,`nama_pic_teknis` varchar(200)
,`no_telp_pic_teknis` varchar(30)
,`email_pic_teknis` varchar(150)
,`nama_pic_keuangan` varchar(200)
,`no_telp_pic_keuangan` varchar(30)
,`email_pic_keuangan` varchar(150)
,`jenis_perusahaan` varchar(100)
,`tanggal_registrasi` date
,`nama_penduduk` text
,`nomor_hp` varchar(20)
,`nomor_hp_2` varchar(20)
,`email` varchar(100)
,`jenis_kelamin` int
,`tanggal_lahir` date
,`pic` text
,`rt_ktp` varchar(3)
,`rw_ktp` varchar(3)
,`alamat_ktp` text
,`alamat_perusahaan` text
,`alamat_k` mediumtext
,`alamat_perusahaan_lengkap` mediumtext
,`kode_wilayah_kelurahan_ktp` varchar(20)
,`nama_kelurahan` varchar(100)
,`nama_kecamatan` varchar(100)
,`nama_kota` varchar(100)
,`nama_provinsi` varchar(100)
,`kode_kategori_bandwith` varchar(50)
,`nama_kategori_bandwith` text
,`alias_nama_kategori` text
,`biaya_reg` varchar(15)
,`kode_bandwith` varchar(50)
,`nominal_bandwith` varchar(5)
,`harga_bandwith` varchar(15)
,`jenis_bangunan` varchar(50)
,`rt_pasang` varchar(3)
,`rw_pasang` varchar(3)
,`alamat_pasang` text
,`alamat_p` mediumtext
,`kode_wilayah_kelurahan_pasang` varchar(20)
,`loc_maps` text
,`nomor_bangunan` varchar(10)
,`lon_lat` text
,`nama_kelurahan_pasang` varchar(100)
,`nama_kecamatan_pasang` varchar(100)
,`nama_kota_pasang` varchar(100)
,`kode_wilayah_kota_pasang` varchar(5)
,`nama_provinsi_pasang` varchar(100)
,`note_request` varchar(50)
,`ppn` varchar(5)
,`ppn_nom` varchar(15)
,`potongan` varchar(15)
,`potongan_note` varchar(50)
,`last_month_billing` varchar(2)
,`last_year_billing` varchar(4)
,`periode_billing` int
,`jns_notif` varchar(2)
,`is_denda` varchar(2)
,`is_suspend` varchar(2)
,`count_suspend` varchar(2)
,`is_termin` varchar(2)
,`kode_instalasi` varchar(50)
,`verifikasi_date` date
,`verifikasi_note` text
,`survey_date_start` date
,`survey_time` varchar(50)
,`survey_team` text
,`survey_note` text
,`survey_date_finish` date
,`survey_note_finish` text
,`doc_survey` text
,`instalasi_date_start` date
,`instalasi_time` varchar(50)
,`instalasi_team` text
,`instalasi_note` text
,`instalasi_date_finish` date
,`instalasi_note_finish` text
,`doc_instalasi` text
,`aktivasi_date_start` date
,`aktivasi_time` varchar(50)
,`aktivasi_team` text
,`aktivasi_note` text
,`aktivasi_date_finish` date
,`aktivasi_note_finish` text
,`doc_aktivasi` text
,`doc_berlangganan` text
,`foto_rumah` text
,`foto_ktp` text
,`foto_peta` text
,`kode_pop` varchar(50)
,`nama_pop` text
,`desc_pop` text
,`ont_us` varchar(10)
,`ont_ps` varchar(10)
,`media_akses` varchar(100)
,`index_olt` varchar(100)
,`foto_po` varchar(255)
,`foto_bangunan` varchar(255)
,`detail_alamat_perusahaan` text
,`nomor_bangunan_perusahaan` varchar(50)
,`rt_perusahaan` varchar(5)
,`rw_perusahaan` varchar(5)
,`kode_wilayah_kelurahan_perusahaan` varchar(20)
,`lon_lat_perusahaan` varchar(100)
,`sharelock_perusahaan` varchar(500)
,`group_layanan` varchar(50)
,`nama_sales` varchar(50)
,`islock` varchar(1)
,`prorate` varchar(1)
,`hide` varchar(1)
,`user_create` varchar(20)
,`date_create` datetime
,`date_update` datetime
,`user_update` varchar(20)
,`desc_hide` text
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_billing_layanan`
-- (See below for the actual view)
--
CREATE TABLE `view_billing_layanan` (
`kode_billing_layanan` varchar(50)
,`nomor_internet` varchar(50)
,`kode_bandwith` varchar(50)
,`nominal_bandwith` varchar(5)
,`bulan_tagihan` varchar(10)
,`tahun_tagihan` varchar(4)
,`periode_tagihan` varchar(100)
,`potongan` varchar(15)
,`desc_potongan` varchar(50)
,`ppn` varchar(5)
,`tax` varchar(5)
,`voucher` varchar(20)
,`total_layanan` varchar(15)
,`notif_mail` varchar(2)
,`notif_wa` varchar(2)
,`status_bill_lay` varchar(5)
,`denda` varchar(2)
,`invoice_file` varchar(150)
,`payment_type` varchar(2)
,`no_rekening` varchar(100)
,`payment_post` text
,`payment_respond_post` json
,`payment_publish` datetime
,`payment_respond_process` json
,`payment_process` datetime
,`payment_respond_paid` json
,`payment_paid` datetime
,`amount_paid` varchar(15)
,`note_adjusment` varchar(100)
,`cashback` varchar(15)
,`payment_respond_cancel` json
,`payment_cancel` datetime
,`expiry` datetime
,`merchant_type` varchar(100)
,`date_create` datetime
,`user_create` varchar(20)
,`date_update` datetime
,`user_update` varchar(20)
,`hide` varchar(1)
,`islock` varchar(2)
,`re_publish` varchar(2)
,`desc_payment_type` text
,`nama_bank` text
,`desc_bill_lay` text
,`desc_hide` text
,`nama_pelanggan` varchar(200)
,`nik_penduduk` varchar(100)
,`nama_penduduk` text
,`nomor_hp` varchar(20)
,`email` varchar(100)
,`jenis_kelamin` int
,`pic` text
,`periode_billing` int
,`alamat_ktp` text
,`alamat_k` mediumtext
,`alamat_pasang` text
,`alamat_p` mediumtext
,`status_reg` varchar(5)
,`nama_kota_pasang` varchar(100)
,`desc_registrasi` text
,`aktivasi_date_finish` date
,`kode_wilayah_kelurahan_pasang` varchar(20)
,`harga_bandwith` varchar(15)
,`is_denda` varchar(2)
,`is_termin` varchar(2)
,`is_suspend` varchar(2)
,`count_suspend` varchar(2)
,`nama_kategori_bandwith` text
,`merchant_fee` varchar(10)
,`type` varchar(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_billing_reg`
-- (See below for the actual view)
--
CREATE TABLE `view_billing_reg` (
`kode_billing_registrasi` varchar(50)
,`nomor_internet` varchar(50)
,`kode_bandwith` varchar(50)
,`nominal_bandwith` varchar(5)
,`potongan` varchar(15)
,`desc_potongan` text
,`ppn` varchar(5)
,`tax` varchar(5)
,`voucher` varchar(20)
,`total_reg` varchar(15)
,`notif_mail` varchar(2)
,`notif_wa` varchar(2)
,`status_bill_reg` varchar(5)
,`payment_type` varchar(2)
,`no_rekening` varchar(100)
,`payment_post` text
,`payment_respond_post` json
,`payment_publish` datetime
,`payment_respond_process` json
,`payment_process` datetime
,`payment_respond_paid` json
,`payment_paid` datetime
,`amount_paid` varchar(15)
,`cashback` varchar(15)
,`payment_respond_cancel` json
,`payment_cancel` datetime
,`expiry` datetime
,`merchant_type` text
,`date_create` datetime
,`user_create` varchar(20)
,`date_update` datetime
,`user_update` varchar(20)
,`hide` varchar(1)
,`islock` varchar(2)
,`desc_payment_type` text
,`nama_bank` text
,`desc_bill_reg` text
,`desc_hide` text
,`status_reg` varchar(5)
,`desc_registrasi` text
,`nik_penduduk` varchar(100)
,`nama_pelanggan` varchar(200)
,`nama_penduduk` text
,`nomor_hp` varchar(20)
,`email` varchar(100)
,`jenis_kelamin` int
,`tanggal_lahir` date
,`pic` text
,`kode_kategori_bandwith` varchar(50)
,`nama_kategori_bandwith` text
,`alias_nama_kategori` text
,`biaya_reg` varchar(15)
,`harga_bandwith` varchar(15)
,`kode_wilayah_kelurahan_pasang` varchar(20)
,`jenis_bangunan` varchar(50)
,`alamat_pasang` text
,`alamat_p` mediumtext
,`periode_billing` int
,`jns_notif` varchar(2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_divisi_jabatan`
-- (See below for the actual view)
--
CREATE TABLE `view_divisi_jabatan` (
`kode_divisi` varchar(50)
,`nama_divisi` varchar(100)
,`desc_divisi` text
,`kode_jabatan` varchar(50)
,`nama_jabatan` varchar(100)
,`desc_jabatan` text
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_karyawan`
-- (See below for the actual view)
--
CREATE TABLE `view_karyawan` (
`kode_karyawan` varchar(50)
,`kantor` varchar(50)
,`nik` varchar(50)
,`nip` varchar(10)
,`nama_karyawan` varchar(100)
,`kode_jabatan` varchar(50)
,`nama_jabatan` varchar(100)
,`kode_divisi` varchar(50)
,`nama_divisi` varchar(100)
,`desc_jabatan` text
,`jenis_kelamin` varchar(2)
,`hp_karyawan` varchar(15)
,`kode_agama` varchar(50)
,`nama_agama` varchar(200)
,`email_karyawan` varchar(50)
,`email_msn` varchar(50)
,`tempat_lahir` varchar(100)
,`tanggal_lahir` date
,`tempat_pendidikan_terakhir` text
,`kode_pendidikan` varchar(50)
,`nama_pendidikan` varchar(200)
,`jmlh_tanggungan` varchar(5)
,`desc_tanggungan` varchar(100)
,`kode_golongan_darah` varchar(50)
,`nama_golongan_darah` varchar(20)
,`kode_wilayah_kelurahan` varchar(50)
,`nama_kelurahan` varchar(100)
,`kode_wilayah_kecamatan` varchar(8)
,`nama_kecamatan` varchar(100)
,`kode_wilayah_kota` varchar(5)
,`kota_kerja` varchar(6)
,`nama_kota` varchar(100)
,`kode_provinsi` char(2)
,`nama_provinsi` varchar(100)
,`ktp` text
,`cv` text
,`foto` text
,`ijazah_pendidikan_terakhir` text
,`alamat_asal` text
,`domisili` text
,`kode_status_kawin` varchar(50)
,`nama_status_kawin` varchar(200)
,`npwp` varchar(20)
,`bpjs` varchar(20)
,`bank_rek` varchar(20)
,`no_rek` text
,`tanggal_masuk` date
,`tanggal_keluar` date
,`status_rumah` varchar(20)
,`kendaraan` varchar(100)
,`status_kontrak` int
,`berat` varchar(5)
,`tinggi` varchar(5)
,`sim` varchar(10)
,`status_aktif` varchar(2)
,`date_create` datetime
,`user_create` varchar(20)
,`user_update` datetime
,`date_update` datetime
,`uid` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_pelanggan`
-- (See below for the actual view)
--
CREATE TABLE `view_pelanggan` (
`nik_penduduk` varchar(100)
,`id_perusahaan` varchar(100)
,`nama_perusahaan` varchar(255)
,`no_telp_perusahaan` varchar(30)
,`email_perusahaan` varchar(150)
,`nama_pic_teknis` varchar(200)
,`no_telp_pic_teknis` varchar(30)
,`email_pic_teknis` varchar(150)
,`nama_pic_keuangan` varchar(200)
,`no_telp_pic_keuangan` varchar(30)
,`email_pic_keuangan` varchar(150)
,`jenis_perusahaan` varchar(100)
,`tanggal_registrasi` date
,`nama_penduduk` text
,`jenis_kelamin` int
,`tanggal_lahir` date
,`pic` text
,`email` varchar(100)
,`nomor_hp` varchar(20)
,`nomor_hp_2` varchar(20)
,`kode_wilayah_kelurahan_ktp` varchar(20)
,`rt_ktp` varchar(3)
,`rw_ktp` varchar(3)
,`alamat_ktp` text
,`alamat_perusahaan` text
,`user_create` varchar(20)
,`date_create` datetime
,`date_update` datetime
,`user_update` varchar(20)
,`hide` varchar(1)
,`nama_kelurahan` varchar(100)
,`nama_kecamatan` varchar(100)
,`nama_kota` varchar(100)
,`nama_provinsi` varchar(100)
,`desc_hide` text
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_pengguna`
-- (See below for the actual view)
--
CREATE TABLE `view_pengguna` (
`kode_pengguna` varchar(50)
,`kode_karyawan` varchar(50)
,`nama_karyawan` varchar(100)
,`kode_level` varchar(50)
,`level` int
,`nama_level` varchar(100)
,`username` varchar(100)
,`password` varchar(100)
,`status_aktif` varchar(2)
,`as_sales` int
,`nip` varchar(10)
,`nama_jabatan` varchar(100)
,`kode_divisi` varchar(50)
,`nama_divisi` varchar(100)
,`foto` text
,`kota_kerja` varchar(6)
,`last_ip` varchar(50)
,`las_login` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_suspend`
-- (See below for the actual view)
--
CREATE TABLE `view_suspend` (
`kode_suspend` varchar(50)
,`nomor_internet` varchar(50)
,`status_reg` varchar(5)
,`desc_registrasi` text
,`nik_penduduk` varchar(100)
,`nama_pelanggan` varchar(200)
,`nama_penduduk` text
,`nomor_hp` varchar(20)
,`email` varchar(100)
,`jenis_kelamin` int
,`kode_wilayah_kota_pasang` varchar(5)
,`pic` text
,`nama_kategori_bandwith` text
,`nominal_bandwith` varchar(5)
,`jenis_bangunan` varchar(50)
,`alamat_p` mediumtext
,`harga_bandwith` varchar(15)
,`sales_create` varchar(20)
,`suspend_start` date
,`suspend_end` date
,`status_suspend` varchar(2)
,`desc_status_suspend` text
,`desc_suspend` text
,`date_create` datetime
,`user_create` varchar(50)
,`date_update` datetime
,`user_update` varchar(50)
,`hide` varchar(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_terminasi`
-- (See below for the actual view)
--
CREATE TABLE `view_terminasi` (
`kode_trx_terminasi` varchar(50)
,`nomor_internet` varchar(50)
,`note_termin` text
,`note_termin_cancel` text
,`status_terminasi` varchar(4)
,`date_collect_start` date
,`team_collect` text
,`time_collect_start` text
,`note_collect_start` text
,`date_collect_finish` date
,`note_collect_finish` varchar(255)
,`date_closing` date
,`doc_terminasi` text
,`collect_perangkat` varchar(2)
,`collect_payment` varchar(2)
,`date_create` datetime
,`date_update` datetime
,`user_create` varchar(50)
,`user_update` varchar(50)
,`hide` varchar(1)
,`kode_wilayah_kota_pasang` varchar(5)
,`nik_penduduk` varchar(100)
,`nama_pelanggan` varchar(200)
,`nama_penduduk` text
,`nomor_hp` varchar(20)
,`email` varchar(100)
,`jenis_kelamin` int
,`pic` text
,`alamat_p` mediumtext
,`jenis_bangunan` varchar(50)
,`nominal_bandwith` varchar(5)
,`nama_kategori_bandwith` text
,`sales_create` varchar(20)
,`harga_bandwith` varchar(15)
,`desc_terminasi` text
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_ubah_layanan`
-- (See below for the actual view)
--
CREATE TABLE `view_ubah_layanan` (
`kode_trx_ubah_layanan` varchar(50)
,`nama_pelanggan` varchar(200)
,`sales_create` varchar(20)
,`kode_wilayah_kota_pasang` varchar(5)
,`nomor_internet` varchar(50)
,`kode_bandwith_lama` varchar(50)
,`nama_kategori_bandwith_lama` text
,`alias_nama_kategori_lama` text
,`nominal_bandwith_lama` varchar(5)
,`kode_bandwith_baru` varchar(50)
,`nama_kategori_bandwith_baru` text
,`alias_nama_kategori_baru` text
,`nominal_bandwith_baru` varchar(5)
,`status_ubah_layanan` varchar(2)
,`desc_ubah_layanan` text
,`date_request` date
,`date_closing` date
,`doc_ubahlayanan` text
,`date_create` datetime
,`user_create` varchar(50)
,`date_update` datetime
,`user_update` varchar(50)
,`hide` varchar(1)
,`note_request` text
,`date_schedule` date
,`note_closing` text
,`note_schedule` text
,`date_cancel` date
,`note_cancel` text
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bot_chat`
--
ALTER TABLE `bot_chat`
  ADD PRIMARY KEY (`nomor_hp`,`nomor_internet`) USING BTREE;

--
-- Indexes for table `bot_chat_medianet`
--
ALTER TABLE `bot_chat_medianet`
  ADD PRIMARY KEY (`nomor_hp`,`nomor_internet`) USING BTREE;

--
-- Indexes for table `bot_question`
--
ALTER TABLE `bot_question`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `country_iso_code`
--
ALTER TABLE `country_iso_code`
  ADD PRIMARY KEY (`Criteria_ID`) USING BTREE;

--
-- Indexes for table `dummy_karyawan`
--
ALTER TABLE `dummy_karyawan`
  ADD PRIMARY KEY (`kode_karyawan`) USING BTREE,
  ADD UNIQUE KEY `kode_karyawan[dummy_karyawan]` (`kode_karyawan`) USING BTREE;

--
-- Indexes for table `error_mail`
--
ALTER TABLE `error_mail`
  ADD PRIMARY KEY (`kode_eror`) USING BTREE;

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_bandwith`
--
ALTER TABLE `m_bandwith`
  ADD PRIMARY KEY (`kode_bandwith`) USING BTREE,
  ADD KEY `kode_bandwith[m_bandwith]` (`kode_bandwith`) USING BTREE,
  ADD KEY `hide[m_bandwith]` (`hide`) USING BTREE,
  ADD KEY `fk_m_bandwith_m_bandwith_kategori_1` (`kode_kategori_bandwith`) USING BTREE,
  ADD KEY `disable[m_bandwith]` (`disable`) USING BTREE;

--
-- Indexes for table `m_bandwith_kategori`
--
ALTER TABLE `m_bandwith_kategori`
  ADD PRIMARY KEY (`kode_kategori_bandwith`) USING BTREE,
  ADD UNIQUE KEY `kode_kategori_bandwith` (`kode_kategori_bandwith`) USING BTREE,
  ADD KEY `hide[m_bandwith_kateogori]` (`hide`) USING BTREE;

--
-- Indexes for table `m_bangunan_layanan`
--
ALTER TABLE `m_bangunan_layanan`
  ADD PRIMARY KEY (`kode_bangunan`,`kode_kategori_bandwith`);

--
-- Indexes for table `m_bank`
--
ALTER TABLE `m_bank`
  ADD PRIMARY KEY (`no_rekening`) USING BTREE;

--
-- Indexes for table `m_barang`
--
ALTER TABLE `m_barang`
  ADD PRIMARY KEY (`kode_barang`) USING BTREE,
  ADD UNIQUE KEY `kode_barang[m_barang]` (`kode_barang`) USING BTREE,
  ADD KEY `fk_m_barang_m_status_hide_2` (`hide`) USING BTREE,
  ADD KEY `fk_m_barang_m_jns_barang_1` (`kode_jns_barang`) USING BTREE;

--
-- Indexes for table `m_gpon`
--
ALTER TABLE `m_gpon`
  ADD PRIMARY KEY (`kode_gpon`) USING BTREE;

--
-- Indexes for table `m_head`
--
ALTER TABLE `m_head`
  ADD PRIMARY KEY (`kode_h`) USING BTREE;

--
-- Indexes for table `m_item_billing`
--
ALTER TABLE `m_item_billing`
  ADD PRIMARY KEY (`kode_item`) USING BTREE;

--
-- Indexes for table `m_jns_bangunan`
--
ALTER TABLE `m_jns_bangunan`
  ADD PRIMARY KEY (`kode_bangunan`) USING BTREE,
  ADD UNIQUE KEY `kode_bangunan[m_jns_bangunan]_2` (`kode_bangunan`) USING BTREE,
  ADD KEY `fk_m_jns_bangunan_m_status_hide_3` (`hide`) USING BTREE;

--
-- Indexes for table `m_jns_barang`
--
ALTER TABLE `m_jns_barang`
  ADD PRIMARY KEY (`kode_jns_barang`) USING BTREE,
  ADD UNIQUE KEY `kode_jns_barang[m_jns_barang]` (`kode_jns_barang`) USING BTREE,
  ADD KEY `fk_m_jns_barang_m_status_hide_1` (`hide`) USING BTREE;

--
-- Indexes for table `m_kat_team`
--
ALTER TABLE `m_kat_team`
  ADD PRIMARY KEY (`kat_team`) USING BTREE,
  ADD UNIQUE KEY `kat_team[m_kat_team]` (`kat_team`) USING BTREE,
  ADD KEY `fk_m_kat_team_m_status_hide_1` (`hide`) USING BTREE;

--
-- Indexes for table `m_layanan_bangunan`
--
ALTER TABLE `m_layanan_bangunan`
  ADD PRIMARY KEY (`kode_layanan_bangunan`) USING BTREE,
  ADD UNIQUE KEY `kode_layanan_bangunan[m_layanan_bangunan]` (`kode_layanan_bangunan`) USING BTREE,
  ADD KEY `fk_m_layanan_bangunan_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_m_layanan_bangunan_m_jns_bangunan_1` (`kode_bangunan`) USING BTREE,
  ADD KEY `fk_m_layanan_bangunan_m_bandwith_kategori_1` (`kode_kategori_bandwith`) USING BTREE;

--
-- Indexes for table `m_media_akses`
--
ALTER TABLE `m_media_akses`
  ADD PRIMARY KEY (`kode_media_akses`) USING BTREE,
  ADD UNIQUE KEY `kode_media_akses[m_media_akses]` (`kode_media_akses`) USING BTREE,
  ADD KEY `fk_m_media_akses_m_status_hide_1` (`hide`) USING BTREE;

--
-- Indexes for table `m_mitra`
--
ALTER TABLE `m_mitra`
  ADD PRIMARY KEY (`mitra`) USING BTREE;

--
-- Indexes for table `m_odp`
--
ALTER TABLE `m_odp`
  ADD PRIMARY KEY (`kode_odp`) USING BTREE;

--
-- Indexes for table `m_olt`
--
ALTER TABLE `m_olt`
  ADD PRIMARY KEY (`kode_olt`) USING BTREE;

--
-- Indexes for table `m_payment_type`
--
ALTER TABLE `m_payment_type`
  ADD PRIMARY KEY (`payment_type`) USING BTREE,
  ADD KEY `payment_type[m_payment_type]` (`payment_type`) USING BTREE,
  ADD KEY `hide[m_payment_type]` (`hide`) USING BTREE;

--
-- Indexes for table `m_pelanggan`
--
ALTER TABLE `m_pelanggan`
  ADD PRIMARY KEY (`id_perusahaan`),
  ADD KEY `fk_m_pelanggan_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `kode_wilayah_kelurahan_ktp[m_pelanggan]` (`kode_wilayah_kelurahan_ktp`) USING BTREE;

--
-- Indexes for table `m_pelanggan_old`
--
ALTER TABLE `m_pelanggan_old`
  ADD PRIMARY KEY (`nik_penduduk`) USING BTREE,
  ADD UNIQUE KEY `nik_penduduk[m_pelanggan]` (`nik_penduduk`) USING BTREE,
  ADD KEY `fk_m_pelanggan_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `kode_wilayah_kelurahan_ktp[m_pelanggan]` (`kode_wilayah_kelurahan_ktp`) USING BTREE;

--
-- Indexes for table `m_periode`
--
ALTER TABLE `m_periode`
  ADD PRIMARY KEY (`kode_periode`) USING BTREE,
  ADD UNIQUE KEY `kode_periode[m_periode]` (`kode_periode`) USING BTREE,
  ADD KEY `fk_m_periode_m_status_hide_1` (`hide`) USING BTREE;

--
-- Indexes for table `m_periode_billing`
--
ALTER TABLE `m_periode_billing`
  ADD PRIMARY KEY (`periode_billing`) USING BTREE;

--
-- Indexes for table `m_pon`
--
ALTER TABLE `m_pon`
  ADD PRIMARY KEY (`kode_pon`) USING BTREE;

--
-- Indexes for table `m_pop`
--
ALTER TABLE `m_pop`
  ADD PRIMARY KEY (`kode_pop`) USING BTREE,
  ADD UNIQUE KEY `kode_pop[m_pop]` (`kode_pop`) USING BTREE,
  ADD KEY `fk_m_pop_m_status_hide_1` (`hide`) USING BTREE;

--
-- Indexes for table `m_redaksi`
--
ALTER TABLE `m_redaksi`
  ADD PRIMARY KEY (`kode_redaksi`) USING BTREE;

--
-- Indexes for table `m_status_bill_lay`
--
ALTER TABLE `m_status_bill_lay`
  ADD PRIMARY KEY (`status_bill_lay`) USING BTREE,
  ADD UNIQUE KEY `status_bill_lay[m_status_bill_lay]` (`status_bill_lay`) USING BTREE,
  ADD KEY `fk_m_status_bill_lay_m_status_hide_1` (`hide`) USING BTREE;

--
-- Indexes for table `m_status_bill_reg`
--
ALTER TABLE `m_status_bill_reg`
  ADD PRIMARY KEY (`status_bill_reg`) USING BTREE,
  ADD UNIQUE KEY `status_bill_reg[m_status_bill_reg]` (`status_bill_reg`) USING BTREE,
  ADD KEY `fk_m_status_bill_reg_m_status_hide_1` (`hide`) USING BTREE;

--
-- Indexes for table `m_status_hide`
--
ALTER TABLE `m_status_hide`
  ADD PRIMARY KEY (`hide`) USING BTREE,
  ADD UNIQUE KEY `hide[m_status_hide]` (`hide`) USING BTREE;

--
-- Indexes for table `m_status_instalasi_barang`
--
ALTER TABLE `m_status_instalasi_barang`
  ADD PRIMARY KEY (`status_instalasi_barang`) USING BTREE,
  ADD KEY `status_instalasi_barang[m_status_barang]` (`status_instalasi_barang`) USING BTREE,
  ADD KEY `fk_m_status_instalasi_barang2_m_status_hide_1` (`hide`) USING BTREE;

--
-- Indexes for table `m_status_midtrans`
--
ALTER TABLE `m_status_midtrans`
  ADD PRIMARY KEY (`code_midtrans`) USING BTREE;

--
-- Indexes for table `m_status_notif_wa`
--
ALTER TABLE `m_status_notif_wa`
  ADD PRIMARY KEY (`status_wa`) USING BTREE;

--
-- Indexes for table `m_status_perangkat`
--
ALTER TABLE `m_status_perangkat`
  ADD PRIMARY KEY (`status_perangkat`) USING BTREE,
  ADD UNIQUE KEY `status_perangkat[m_status_perangkat]` (`status_perangkat`) USING BTREE,
  ADD KEY `fk_m_status_perangkat_m_status_hide_1` (`hide`) USING BTREE;

--
-- Indexes for table `m_status_registrasi`
--
ALTER TABLE `m_status_registrasi`
  ADD PRIMARY KEY (`status_reg`) USING BTREE,
  ADD UNIQUE KEY `status_reg[m_status_register]` (`status_reg`) USING BTREE,
  ADD KEY `fk_m_status_registrasi_m_status_hide_1` (`hide`) USING BTREE;

--
-- Indexes for table `m_status_suspend`
--
ALTER TABLE `m_status_suspend`
  ADD PRIMARY KEY (`status_suspend`) USING BTREE,
  ADD UNIQUE KEY `status_suspend[m_status_suspend]` (`status_suspend`) USING BTREE,
  ADD KEY `fk_m_status_suspend_m_status_hide_2` (`hide`) USING BTREE;

--
-- Indexes for table `m_status_terminasi`
--
ALTER TABLE `m_status_terminasi`
  ADD PRIMARY KEY (`status_terminasi`) USING BTREE,
  ADD UNIQUE KEY `status_terminasi[m_status_terminasi]` (`status_terminasi`) USING BTREE,
  ADD KEY `fk_m_status_terminasi_m_status_hide_2` (`hide`) USING BTREE;

--
-- Indexes for table `m_status_ubahlayanan`
--
ALTER TABLE `m_status_ubahlayanan`
  ADD PRIMARY KEY (`status_ubah_layanan`) USING BTREE,
  ADD KEY `status_ubah_layanan[m_status_ubahlayanan]` (`status_ubah_layanan`) USING BTREE,
  ADD KEY `hide[m_status_ubahlayanan]` (`hide`) USING BTREE;

--
-- Indexes for table `m_time_job`
--
ALTER TABLE `m_time_job`
  ADD PRIMARY KEY (`kode_job`) USING BTREE,
  ADD UNIQUE KEY `kode_job[m_time_job]` (`kode_job`) USING BTREE,
  ADD KEY `fk_m_time_job_m_status_hide_1` (`hide`) USING BTREE;

--
-- Indexes for table `m_wago_api`
--
ALTER TABLE `m_wago_api`
  ADD PRIMARY KEY (`kode_api`) USING BTREE;

--
-- Indexes for table `m_wilayah`
--
ALTER TABLE `m_wilayah`
  ADD PRIMARY KEY (`kode_wilayah`) USING BTREE,
  ADD UNIQUE KEY `kode_wilayah_kelurahan` (`kode_wilayah_kelurahan`) USING BTREE,
  ADD KEY `kode_wilayah_kecamatan` (`kode_wilayah_kecamatan`) USING BTREE,
  ADD KEY `kode_wilayah_kota` (`kode_wilayah_kota`) USING BTREE,
  ADD KEY `kode_wilayah_provinsi` (`kode_wilayah_provinsi`) USING BTREE;

--
-- Indexes for table `m_wilayah_perangkat`
--
ALTER TABLE `m_wilayah_perangkat`
  ADD PRIMARY KEY (`kode_w`) USING BTREE;

--
-- Indexes for table `notif_wa`
--
ALTER TABLE `notif_wa`
  ADD PRIMARY KEY (`kode_wa`) USING BTREE;

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tb_m_agama`
--
ALTER TABLE `tb_m_agama`
  ADD PRIMARY KEY (`id_agama`) USING BTREE,
  ADD KEY `index_agama_penduduk` (`kode_agama`) USING BTREE;

--
-- Indexes for table `tb_m_divisi`
--
ALTER TABLE `tb_m_divisi`
  ADD PRIMARY KEY (`kode_divisi`) USING BTREE;

--
-- Indexes for table `tb_m_golongan_darah`
--
ALTER TABLE `tb_m_golongan_darah`
  ADD PRIMARY KEY (`id_golongan_darah`) USING BTREE,
  ADD KEY `index_golongan_darah_penduduk` (`kode_golongan_darah`) USING BTREE;

--
-- Indexes for table `tb_m_jabatan`
--
ALTER TABLE `tb_m_jabatan`
  ADD PRIMARY KEY (`kode_jabatan`) USING BTREE;

--
-- Indexes for table `tb_m_karyawan`
--
ALTER TABLE `tb_m_karyawan`
  ADD PRIMARY KEY (`kode_karyawan`) USING BTREE;

--
-- Indexes for table `tb_m_level_pengguna`
--
ALTER TABLE `tb_m_level_pengguna`
  ADD PRIMARY KEY (`kode_level`) USING BTREE;

--
-- Indexes for table `tb_m_menu`
--
ALTER TABLE `tb_m_menu`
  ADD PRIMARY KEY (`kode_menu`) USING BTREE;

--
-- Indexes for table `tb_m_pendidikan`
--
ALTER TABLE `tb_m_pendidikan`
  ADD PRIMARY KEY (`id_pendidikan`) USING BTREE,
  ADD KEY `index_pendidikan_penduduk` (`kode_pendidikan`) USING BTREE;

--
-- Indexes for table `tb_m_status_kawin`
--
ALTER TABLE `tb_m_status_kawin`
  ADD PRIMARY KEY (`id_status_kawin`) USING BTREE,
  ADD KEY `id_status_kawin` (`id_status_kawin`,`kode_status_kawin`) USING BTREE;

--
-- Indexes for table `tb_pengguna`
--
ALTER TABLE `tb_pengguna`
  ADD PRIMARY KEY (`kode_pengguna`) USING BTREE,
  ADD KEY `kode_karyawan` (`kode_karyawan`) USING BTREE;

--
-- Indexes for table `test_renotif`
--
ALTER TABLE `test_renotif`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `trx_batchjob_register`
--
ALTER TABLE `trx_batchjob_register`
  ADD PRIMARY KEY (`nomor_internet`) USING BTREE,
  ADD UNIQUE KEY `nomor_internet[trx_batchjob_register]` (`nomor_internet`) USING BTREE,
  ADD KEY `hide[trx_batchjob_register]` (`hide`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_m_pop_1` (`kode_pop`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_m_status_registrasi_1` (`status_reg`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_m_pelanggan_1` (`id_perusahaan`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_m_wilayah_2` (`kode_wilayah_kelurahan_pasang`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_m_bandwith_1` (`kode_bandwith`) USING BTREE,
  ADD KEY `nama_pelanggan[trx_bj_regis]` (`nama_pelanggan`) USING BTREE,
  ADD KEY `last_year_billing[trx_bj_reg]` (`last_year_billing`) USING BTREE,
  ADD KEY `last_month_billing[trx_bj_reg]` (`last_month_billing`) USING BTREE;

--
-- Indexes for table `trx_batchjob_register_log`
--
ALTER TABLE `trx_batchjob_register_log`
  ADD PRIMARY KEY (`kode_batchjob_register_log`) USING BTREE,
  ADD UNIQUE KEY `kode_batchjob_register_log[trx_batchjob_register]` (`kode_batchjob_register_log`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_log_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_log_trx_batchjob_register_1` (`nomor_internet`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_log_m_status_registrasi_1` (`status_reg`) USING BTREE;

--
-- Indexes for table `trx_batchjob_register_old`
--
ALTER TABLE `trx_batchjob_register_old`
  ADD PRIMARY KEY (`nomor_internet`) USING BTREE,
  ADD UNIQUE KEY `nomor_internet[trx_batchjob_register]` (`nomor_internet`) USING BTREE,
  ADD KEY `hide[trx_batchjob_register]` (`hide`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_m_pop_1` (`kode_pop`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_m_status_registrasi_1` (`status_reg`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_m_pelanggan_1` (`nik_penduduk`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_m_wilayah_2` (`kode_wilayah_kelurahan_pasang`) USING BTREE,
  ADD KEY `fk_trx_batchjob_register_m_bandwith_1` (`kode_bandwith`) USING BTREE,
  ADD KEY `nama_pelanggan[trx_bj_regis]` (`nama_pelanggan`) USING BTREE,
  ADD KEY `last_year_billing[trx_bj_reg]` (`last_year_billing`) USING BTREE,
  ADD KEY `last_month_billing[trx_bj_reg]` (`last_month_billing`) USING BTREE;

--
-- Indexes for table `trx_billing_layanan`
--
ALTER TABLE `trx_billing_layanan`
  ADD PRIMARY KEY (`kode_billing_layanan`) USING BTREE,
  ADD UNIQUE KEY `kode_billing_layanan[trx_billing_layanan]` (`kode_billing_layanan`) USING BTREE,
  ADD KEY `fk_trx_billing_layanan_m_status_bill_lay_1` (`status_bill_lay`) USING BTREE,
  ADD KEY `fk_trx_billing_layanan_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_billing_layanan_trx_batchjob_register_1` (`nomor_internet`) USING BTREE,
  ADD KEY `fk_trx_billing_layanan_m_bandwith_1` (`kode_bandwith`) USING BTREE;

--
-- Indexes for table `trx_billing_layanan_detail`
--
ALTER TABLE `trx_billing_layanan_detail`
  ADD PRIMARY KEY (`kode_billing_lay_detail`) USING BTREE,
  ADD UNIQUE KEY `kode_billing_lay_detail[trx_billing_layanan_detail]` (`kode_billing_lay_detail`) USING BTREE,
  ADD KEY `fk_trx_billing_layanan_detail_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_billing_layanan_detail_m_barang_1` (`kode_item`) USING BTREE,
  ADD KEY `fk_trx_billing_layanan_detail_trx_billing_layanan_1` (`kode_billing_layanan`) USING BTREE;

--
-- Indexes for table `trx_billing_layanan_log`
--
ALTER TABLE `trx_billing_layanan_log`
  ADD PRIMARY KEY (`kode_billing_lay_log`) USING BTREE,
  ADD UNIQUE KEY `kode_billing_lay_log[trx_billing_layanan_log]` (`kode_billing_lay_log`) USING BTREE,
  ADD KEY `fk_trx_billing_layanan_log_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_billing_layanan_log_trx_billing_layanan_1` (`kode_billing_layanan`) USING BTREE,
  ADD KEY `fk_trx_billing_layanan_log_m_status_bill_lay_1` (`status_bill_lay`) USING BTREE;

--
-- Indexes for table `trx_billing_registrasi`
--
ALTER TABLE `trx_billing_registrasi`
  ADD PRIMARY KEY (`kode_billing_registrasi`) USING BTREE,
  ADD UNIQUE KEY `kode_billing_registrasi[trx_billing_registrasi]` (`kode_billing_registrasi`) USING BTREE,
  ADD KEY `fk_trx_billing_registrasi_m_status_bill_reg_1` (`status_bill_reg`) USING BTREE,
  ADD KEY `fk_trx_billing_registrasi_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_billing_registrasi_trx_batchjob_register_1` (`nomor_internet`) USING BTREE,
  ADD KEY `fk_trx_billing_registrasi_m_bandwith_1` (`kode_bandwith`) USING BTREE;

--
-- Indexes for table `trx_billing_registrasi_detail`
--
ALTER TABLE `trx_billing_registrasi_detail`
  ADD PRIMARY KEY (`kode_billing_detail`) USING BTREE,
  ADD UNIQUE KEY `kode_billing_detail[trx_billing_registrasi_detail]` (`kode_billing_detail`) USING BTREE,
  ADD KEY `fk_trx_billing_registrasi_detail_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_billing_registrasi_detail_trx_billing_registrasi_1` (`kode_billing_registrasi`) USING BTREE,
  ADD KEY `fk_trx_billing_registrasi_detail_m_barang_1` (`kode_barang`) USING BTREE;

--
-- Indexes for table `trx_billing_registrasi_log`
--
ALTER TABLE `trx_billing_registrasi_log`
  ADD PRIMARY KEY (`kode_billing_regis_log`) USING BTREE,
  ADD UNIQUE KEY `kode_billing_regis_log[trx_billing_registrasi_log]` (`kode_billing_regis_log`) USING BTREE,
  ADD KEY `fk_trx_billing_registrasi_log_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_billing_registrasi_log_trx_billing_registrasi_1` (`kode_billing_registrasi`) USING BTREE,
  ADD KEY `fk_trx_billing_registrasi_log_m_status_bill_reg_1` (`status_bill_reg`) USING BTREE;

--
-- Indexes for table `trx_coverage_area`
--
ALTER TABLE `trx_coverage_area`
  ADD PRIMARY KEY (`id_message`) USING BTREE;

--
-- Indexes for table `trx_instalasi`
--
ALTER TABLE `trx_instalasi`
  ADD PRIMARY KEY (`kode_instalasi`) USING BTREE,
  ADD UNIQUE KEY `kode_instalasi[trx_instalasi]` (`kode_instalasi`) USING BTREE,
  ADD KEY `fk_trx_instalasi_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_instalasi_trx_batchjob_register_1` (`nomor_internet`) USING BTREE;

--
-- Indexes for table `trx_instalasi_barang`
--
ALTER TABLE `trx_instalasi_barang`
  ADD PRIMARY KEY (`kode_inst_barang`) USING BTREE,
  ADD UNIQUE KEY `kode_inst_barang[trx_instalasi_barang]` (`kode_inst_barang`) USING BTREE,
  ADD KEY `fk_trx_instalasi_barang_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_instalasi_barang_trx_batchjob_register_1` (`nomor_internet`) USING BTREE,
  ADD KEY `fk_trx_instalasi_barang_m_barang_1` (`kode_barang`) USING BTREE,
  ADD KEY `fk_trx_instalasi_barang_m_status_instalasi_barang2_1` (`status_instalasi_barang`) USING BTREE;

--
-- Indexes for table `trx_instalasi_team`
--
ALTER TABLE `trx_instalasi_team`
  ADD PRIMARY KEY (`kode_instalasi_team`) USING BTREE,
  ADD UNIQUE KEY `kode_instalasi_team[trx_instalasi_team]` (`kode_instalasi_team`) USING BTREE,
  ADD KEY `fk_trx_instalasi_team_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_instalasi_team_trx_batchjob_register_1` (`nomor_internet`) USING BTREE,
  ADD KEY `fk_trx_instalasi_team_m_kat_team_1` (`kat_team`) USING BTREE;

--
-- Indexes for table `trx_suspend`
--
ALTER TABLE `trx_suspend`
  ADD PRIMARY KEY (`kode_suspend`) USING BTREE,
  ADD UNIQUE KEY `kode_suspend[trx_suspend]` (`kode_suspend`) USING BTREE,
  ADD KEY `fk_trx_suspend_m_status_hide_2` (`hide`) USING BTREE,
  ADD KEY `fk_trx_suspend_m_status_suspend_1` (`status_suspend`) USING BTREE;

--
-- Indexes for table `trx_suspend_log`
--
ALTER TABLE `trx_suspend_log`
  ADD PRIMARY KEY (`kode_suspend_log`) USING BTREE,
  ADD KEY `kode_suspend[trx_suspend_log]` (`kode_suspend`) USING BTREE,
  ADD KEY `fk_trx_suspend_log_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_suspend_log_m_status_suspend_1` (`status_suspend`) USING BTREE;

--
-- Indexes for table `trx_terminasi`
--
ALTER TABLE `trx_terminasi`
  ADD PRIMARY KEY (`kode_trx_terminasi`) USING BTREE,
  ADD UNIQUE KEY `kode_trx_terminasi[trx_terminasi]` (`kode_trx_terminasi`) USING BTREE,
  ADD KEY `fk_trx_terminasi_m_status_hide_1` (`hide`) USING BTREE,
  ADD KEY `fk_trx_terminasi_trx_batchjob_register_1` (`nomor_internet`) USING BTREE,
  ADD KEY `fk_trx_terminasi_m_status_terminasi_1` (`status_terminasi`) USING BTREE;

--
-- Indexes for table `trx_terminasi_log`
--
ALTER TABLE `trx_terminasi_log`
  ADD PRIMARY KEY (`kode_log_terminasi`) USING BTREE,
  ADD UNIQUE KEY `kode_log_terminasi[trx_terminasi]` (`kode_log_terminasi`) USING BTREE,
  ADD KEY `fk_trx_terminasi_log_m_status_hide_2` (`hide`) USING BTREE,
  ADD KEY `fk_trx_terminasi_log_trx_terminasi_2` (`kode_trx_terminasi`) USING BTREE,
  ADD KEY `fk_trx_terminasi_log_m_status_terminasi_2` (`status_terminasi`) USING BTREE;

--
-- Indexes for table `trx_tiket_gangguan`
--
ALTER TABLE `trx_tiket_gangguan`
  ADD PRIMARY KEY (`tiket`) USING BTREE;

--
-- Indexes for table `trx_ubah_layanan`
--
ALTER TABLE `trx_ubah_layanan`
  ADD PRIMARY KEY (`kode_trx_ubah_layanan`) USING BTREE,
  ADD KEY `status_ubah_layanan[trx_ubah_layanan]` (`status_ubah_layanan`) USING BTREE,
  ADD KEY `nomor_internet[trx_ubah_layanan]` (`nomor_internet`) USING BTREE,
  ADD KEY `hide[trx_ubah_layanan]` (`hide`) USING BTREE,
  ADD KEY `fk_trx_ubah_layanan_m_bandwith_1` (`kode_bandwith_lama`) USING BTREE,
  ADD KEY `fk_trx_ubah_layanan_m_bandwith_2` (`kode_bandwith_baru`) USING BTREE;

--
-- Indexes for table `trx_ubah_layanan_log`
--
ALTER TABLE `trx_ubah_layanan_log`
  ADD PRIMARY KEY (`kode_ubah_layanan_log`) USING BTREE,
  ADD KEY `kode_trx_ubah_layananl` (`kode_trx_ubah_layanan`) USING BTREE,
  ADD KEY `fk_trx_ubah_layanan_log_m_status_hide_2` (`hide`) USING BTREE,
  ADD KEY `fk_trx_ubah_layanan_log_m_status_ubahlayanan_1` (`status_ubah_layanan`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `view_aktif_kota`
--
DROP TABLE IF EXISTS `view_aktif_kota`;

CREATE VIEW `view_aktif_kota`  AS SELECT `view_batchjob`.`nama_kota_pasang` AS `nama_kota`, substr(`view_batchjob`.`kode_wilayah_kelurahan_pasang`,1,5) AS `kode_kota` FROM `view_batchjob` GROUP BY `view_batchjob`.`nama_kota_pasang` ;

-- --------------------------------------------------------

--
-- Structure for view `view_bandwith`
--
DROP TABLE IF EXISTS `view_bandwith`;

CREATE VIEW `view_bandwith`  AS SELECT `kb`.`kode_kategori_bandwith` AS `kode_kategori_bandwith`, `kb`.`nama_kategori_bandwith` AS `nama_kategori_bandwith`, `kb`.`alias_nama_kategori` AS `alias_nama_kategori`, `kb`.`biaya_reg` AS `biaya_reg`, `kb`.`ppn_reg` AS `ppn_reg`, `kb`.`ppn_reg_nom` AS `ppn_reg_nom`, `kb`.`ppn_bill` AS `ppn_bill`, `kb`.`ppn_bill_nom` AS `ppn_bill_nom`, `kb`.`disable` AS `disable_kat`, `kb`.`hide` AS `hide_kat`, `bw`.`kode_bandwith` AS `kode_bandwith`, `bw`.`nominal_bandwith` AS `nominal_bandwith`, `bw`.`harga_bandwith` AS `harga_bandwith`, `bw`.`disable` AS `disable_band`, `bw`.`hide` AS `hide_band`, `h`.`desc_hide` AS `desc_hide` FROM ((`m_bandwith_kategori` `kb` left join `m_bandwith` `bw` on((`bw`.`kode_kategori_bandwith` = `kb`.`kode_kategori_bandwith`))) left join `m_status_hide` `h` on((`kb`.`hide` = `h`.`hide`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_barang`
--
DROP TABLE IF EXISTS `view_barang`;

CREATE VIEW `view_barang`  AS SELECT `b`.`kode_jns_barang` AS `kode_jns_barang`, `jb`.`nama_jns_barang` AS `nama_jns_barang`, `jb`.`satuan` AS `satuan`, `b`.`kode_barang` AS `kode_barang`, `b`.`nama_barang` AS `nama_barang`, `b`.`img` AS `img`, `b`.`tipe_barang` AS `tipe_barang`, `b`.`hitung_kelebihan` AS `hitung_kelebihan`, `b`.`biaya_kelebihan` AS `biaya_kelebihan`, `jb`.`get_back` AS `get_back`, `b`.`hide` AS `hide` FROM (`m_barang` `b` left join `m_jns_barang` `jb` on((`b`.`kode_jns_barang` = `jb`.`kode_jns_barang`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_batchjob`
--
DROP TABLE IF EXISTS `view_batchjob`;

CREATE VIEW `view_batchjob`  AS SELECT `br`.`nomor_internet` AS `nomor_internet`, `br`.`status_reg` AS `status_reg`, `sr`.`desc_registrasi` AS `desc_registrasi`, `br`.`id_perusahaan` AS `nik_penduduk`, `br`.`id_perusahaan` AS `id_perusahaan`, `br`.`nama_pelanggan` AS `nama_pelanggan`, `p`.`nama_perusahaan` AS `nama_perusahaan`, `p`.`no_telp_perusahaan` AS `no_telp_perusahaan`, `p`.`email_perusahaan` AS `email_perusahaan`, `p`.`nama_pic_teknis` AS `nama_pic_teknis`, `p`.`no_telp_pic_teknis` AS `no_telp_pic_teknis`, `p`.`email_pic_teknis` AS `email_pic_teknis`, `p`.`nama_pic_keuangan` AS `nama_pic_keuangan`, `p`.`no_telp_pic_keuangan` AS `no_telp_pic_keuangan`, `p`.`email_pic_keuangan` AS `email_pic_keuangan`, `p`.`jenis_perusahaan` AS `jenis_perusahaan`, `p`.`tanggal_registrasi` AS `tanggal_registrasi`, `p`.`nama_penduduk` AS `nama_penduduk`, `p`.`nomor_hp` AS `nomor_hp`, `p`.`nomor_hp_2` AS `nomor_hp_2`, `p`.`email` AS `email`, `p`.`jenis_kelamin` AS `jenis_kelamin`, `p`.`tanggal_lahir` AS `tanggal_lahir`, `p`.`pic` AS `pic`, `p`.`rt_ktp` AS `rt_ktp`, `p`.`rw_ktp` AS `rw_ktp`, `p`.`alamat_ktp` AS `alamat_ktp`, `p`.`alamat_ktp` AS `alamat_perusahaan`, concat(`p`.`alamat_ktp`,', RT',`p`.`rt_ktp`,'/RW',`p`.`rw_ktp`,', KEL. ',`p`.`nama_kelurahan`,', KEC. ',`p`.`nama_kecamatan`,', ',`p`.`nama_kota`,', ',`p`.`nama_provinsi`) AS `alamat_k`, concat(`p`.`alamat_ktp`,', RT',`p`.`rt_ktp`,'/RW',`p`.`rw_ktp`,', KEL. ',`p`.`nama_kelurahan`,', KEC. ',`p`.`nama_kecamatan`,', ',`p`.`nama_kota`,', ',`p`.`nama_provinsi`) AS `alamat_perusahaan_lengkap`, `p`.`kode_wilayah_kelurahan_ktp` AS `kode_wilayah_kelurahan_ktp`, `p`.`nama_kelurahan` AS `nama_kelurahan`, `p`.`nama_kecamatan` AS `nama_kecamatan`, `p`.`nama_kota` AS `nama_kota`, `p`.`nama_provinsi` AS `nama_provinsi`, `b`.`kode_kategori_bandwith` AS `kode_kategori_bandwith`, `b`.`nama_kategori_bandwith` AS `nama_kategori_bandwith`, `b`.`alias_nama_kategori` AS `alias_nama_kategori`, `b`.`biaya_reg` AS `biaya_reg`, `b`.`kode_bandwith` AS `kode_bandwith`, `b`.`nominal_bandwith` AS `nominal_bandwith`, `b`.`harga_bandwith` AS `harga_bandwith`, `br`.`jenis_bangunan` AS `jenis_bangunan`, `br`.`rt_pasang` AS `rt_pasang`, `br`.`rw_pasang` AS `rw_pasang`, `br`.`alamat_pasang` AS `alamat_pasang`, concat(`br`.`alamat_pasang`,' NO. ',`br`.`nomor_bangunan`,', RT',`br`.`rt_pasang`,'/RW',`br`.`rw_pasang`,', KEL. ',`w`.`nama_kelurahan`,', KEC. ',`w`.`nama_kecamatan`,', ',`w`.`nama_kota`,', ',`w`.`nama_provinsi`) AS `alamat_p`, `br`.`kode_wilayah_kelurahan_pasang` AS `kode_wilayah_kelurahan_pasang`, `br`.`loc_maps` AS `loc_maps`, `br`.`nomor_bangunan` AS `nomor_bangunan`, `br`.`lon_lat` AS `lon_lat`, `w`.`nama_kelurahan` AS `nama_kelurahan_pasang`, `w`.`nama_kecamatan` AS `nama_kecamatan_pasang`, `w`.`nama_kota` AS `nama_kota_pasang`, `w`.`kode_wilayah_kota` AS `kode_wilayah_kota_pasang`, `w`.`nama_provinsi` AS `nama_provinsi_pasang`, `br`.`note_request` AS `note_request`, `br`.`ppn` AS `ppn`, `br`.`ppn_nom` AS `ppn_nom`, `br`.`potongan` AS `potongan`, `br`.`potongan_note` AS `potongan_note`, `br`.`last_month_billing` AS `last_month_billing`, `br`.`last_year_billing` AS `last_year_billing`, `br`.`periode_billing` AS `periode_billing`, `br`.`jns_notif` AS `jns_notif`, `br`.`is_denda` AS `is_denda`, `br`.`is_suspend` AS `is_suspend`, `br`.`count_suspend` AS `count_suspend`, `br`.`is_termin` AS `is_termin`, `i`.`kode_instalasi` AS `kode_instalasi`, `i`.`verifikasi_date` AS `verifikasi_date`, `i`.`verifikasi_note` AS `verifikasi_note`, `i`.`survey_date_start` AS `survey_date_start`, `i`.`survey_time` AS `survey_time`, `i`.`survey_team` AS `survey_team`, `i`.`survey_note` AS `survey_note`, `i`.`survey_date_finish` AS `survey_date_finish`, `i`.`survey_note_finish` AS `survey_note_finish`, `i`.`doc_survey` AS `doc_survey`, `i`.`instalasi_date_start` AS `instalasi_date_start`, `i`.`instalasi_time` AS `instalasi_time`, `i`.`instalasi_team` AS `instalasi_team`, `i`.`instalasi_note` AS `instalasi_note`, `i`.`instalasi_date_finish` AS `instalasi_date_finish`, `i`.`instalasi_note_finish` AS `instalasi_note_finish`, `i`.`doc_instalasi` AS `doc_instalasi`, `i`.`aktivasi_date_start` AS `aktivasi_date_start`, `i`.`aktivasi_time` AS `aktivasi_time`, `i`.`aktivasi_team` AS `aktivasi_team`, `i`.`aktivasi_note` AS `aktivasi_note`, `i`.`aktivasi_date_finish` AS `aktivasi_date_finish`, `i`.`aktivasi_note_finish` AS `aktivasi_note_finish`, `i`.`doc_aktivasi` AS `doc_aktivasi`, `i`.`doc_berlangganan` AS `doc_berlangganan`, `i`.`foto_rumah` AS `foto_rumah`, `i`.`foto_ktp` AS `foto_ktp`, `i`.`foto_peta` AS `foto_peta`, `br`.`kode_pop` AS `kode_pop`, `po`.`nama_pop` AS `nama_pop`, `po`.`desc_pop` AS `desc_pop`, `br`.`ont_us` AS `ont_us`, `br`.`ont_ps` AS `ont_ps`, `br`.`media_akses` AS `media_akses`, `br`.`index_olt` AS `index_olt`, `br`.`foto_po` AS `foto_po`, `br`.`foto_bangunan` AS `foto_bangunan`, `br`.`detail_alamat_perusahaan` AS `detail_alamat_perusahaan`, `br`.`nomor_bangunan_perusahaan` AS `nomor_bangunan_perusahaan`, `br`.`rt_perusahaan` AS `rt_perusahaan`, `br`.`rw_perusahaan` AS `rw_perusahaan`, `br`.`kode_wilayah_kelurahan_perusahaan` AS `kode_wilayah_kelurahan_perusahaan`, `br`.`lon_lat_perusahaan` AS `lon_lat_perusahaan`, `br`.`sharelock_perusahaan` AS `sharelock_perusahaan`, `br`.`group_layanan` AS `group_layanan`, `br`.`nama_sales` AS `nama_sales`, `br`.`islock` AS `islock`, `br`.`prorate` AS `prorate`, `br`.`hide` AS `hide`, `br`.`user_create` AS `user_create`, `br`.`date_create` AS `date_create`, `br`.`date_update` AS `date_update`, `br`.`user_update` AS `user_update`, `h`.`desc_hide` AS `desc_hide` FROM (((((((`trx_batchjob_register` `br` left join `m_status_registrasi` `sr` on((`br`.`status_reg` = `sr`.`status_reg`))) left join `trx_instalasi` `i` on((`br`.`nomor_internet` = `i`.`nomor_internet`))) left join `view_bandwith` `b` on((`br`.`kode_bandwith` = `b`.`kode_bandwith`))) left join `m_wilayah` `w` on((`br`.`kode_wilayah_kelurahan_pasang` = `w`.`kode_wilayah_kelurahan`))) left join `view_pelanggan` `p` on((`br`.`id_perusahaan` = `p`.`id_perusahaan`))) left join `m_status_hide` `h` on((`br`.`hide` = `h`.`hide`))) left join `m_pop` `po` on((`po`.`kode_pop` = `br`.`kode_pop`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_billing_layanan`
--
DROP TABLE IF EXISTS `view_billing_layanan`;

CREATE VIEW `view_billing_layanan`  AS SELECT `bl`.`kode_billing_layanan` AS `kode_billing_layanan`, `bl`.`nomor_internet` AS `nomor_internet`, `bl`.`kode_bandwith` AS `kode_bandwith`, `bl`.`nominal_bandwith` AS `nominal_bandwith`, `bl`.`bulan_tagihan` AS `bulan_tagihan`, `bl`.`tahun_tagihan` AS `tahun_tagihan`, `bl`.`periode_tagihan` AS `periode_tagihan`, `bl`.`potongan` AS `potongan`, `bl`.`desc_potongan` AS `desc_potongan`, `bl`.`ppn` AS `ppn`, `bl`.`tax` AS `tax`, `bl`.`voucher` AS `voucher`, `bl`.`total_layanan` AS `total_layanan`, `bl`.`notif_mail` AS `notif_mail`, `bl`.`notif_wa` AS `notif_wa`, `bl`.`status_bill_lay` AS `status_bill_lay`, `bl`.`denda` AS `denda`, `bl`.`invoice_file` AS `invoice_file`, `bl`.`payment_type` AS `payment_type`, `bl`.`no_rekening` AS `no_rekening`, `bl`.`payment_post` AS `payment_post`, `bl`.`payment_respond_post` AS `payment_respond_post`, `bl`.`payment_publish` AS `payment_publish`, `bl`.`payment_respond_process` AS `payment_respond_process`, `bl`.`payment_process` AS `payment_process`, `bl`.`payment_respond_paid` AS `payment_respond_paid`, `bl`.`payment_paid` AS `payment_paid`, `bl`.`amount_paid` AS `amount_paid`, `bl`.`note_adjusment` AS `note_adjusment`, `bl`.`cashback` AS `cashback`, `bl`.`payment_respond_cancel` AS `payment_respond_cancel`, `bl`.`payment_cancel` AS `payment_cancel`, `bl`.`expiry` AS `expiry`, `bl`.`merchant_type` AS `merchant_type`, `bl`.`date_create` AS `date_create`, `bl`.`user_create` AS `user_create`, `bl`.`date_update` AS `date_update`, `bl`.`user_update` AS `user_update`, `bl`.`hide` AS `hide`, `bl`.`islock` AS `islock`, `bl`.`re_publish` AS `re_publish`, `pt`.`desc_payment_type` AS `desc_payment_type`, `mb`.`nama_bank` AS `nama_bank`, `sbl`.`desc_bill_lay` AS `desc_bill_lay`, `sh`.`desc_hide` AS `desc_hide`, `vb`.`nama_pelanggan` AS `nama_pelanggan`, `vb`.`nik_penduduk` AS `nik_penduduk`, `vb`.`nama_penduduk` AS `nama_penduduk`, `vb`.`nomor_hp` AS `nomor_hp`, `vb`.`email` AS `email`, `vb`.`jenis_kelamin` AS `jenis_kelamin`, `vb`.`pic` AS `pic`, `vb`.`periode_billing` AS `periode_billing`, `vb`.`alamat_ktp` AS `alamat_ktp`, `vb`.`alamat_k` AS `alamat_k`, `vb`.`alamat_pasang` AS `alamat_pasang`, `vb`.`alamat_p` AS `alamat_p`, `vb`.`status_reg` AS `status_reg`, `vb`.`nama_kota_pasang` AS `nama_kota_pasang`, `vb`.`desc_registrasi` AS `desc_registrasi`, `vb`.`aktivasi_date_finish` AS `aktivasi_date_finish`, `vb`.`kode_wilayah_kelurahan_pasang` AS `kode_wilayah_kelurahan_pasang`, `vb`.`harga_bandwith` AS `harga_bandwith`, `vb`.`is_denda` AS `is_denda`, `vb`.`is_termin` AS `is_termin`, `vb`.`is_suspend` AS `is_suspend`, `vb`.`count_suspend` AS `count_suspend`, `vb`.`nama_kategori_bandwith` AS `nama_kategori_bandwith`, `m`.`fee` AS `merchant_fee`, `m`.`type` AS `type` FROM ((((((`trx_billing_layanan` `bl` left join `view_batchjob` `vb` on((`bl`.`nomor_internet` = `vb`.`nomor_internet`))) left join `m_status_bill_lay` `sbl` on((`bl`.`status_bill_lay` = `sbl`.`status_bill_lay`))) left join `m_status_hide` `sh` on((`bl`.`hide` = `sh`.`hide`))) left join `m_payment_type` `pt` on((`bl`.`payment_type` = `pt`.`payment_type`))) left join `m_bank` `mb` on((`mb`.`no_rekening` = `bl`.`no_rekening`))) left join `m_midtrans` `m` on(((convert(`m`.`merchant_type` using utf8mb4) collate utf8mb4_unicode_ci) = `bl`.`merchant_type`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_billing_reg`
--
DROP TABLE IF EXISTS `view_billing_reg`;

CREATE VIEW `view_billing_reg`  AS SELECT `br`.`kode_billing_registrasi` AS `kode_billing_registrasi`, `br`.`nomor_internet` AS `nomor_internet`, `br`.`kode_bandwith` AS `kode_bandwith`, `br`.`nominal_bandwith` AS `nominal_bandwith`, `br`.`potongan` AS `potongan`, `br`.`desc_potongan` AS `desc_potongan`, `br`.`ppn` AS `ppn`, `br`.`tax` AS `tax`, `br`.`voucher` AS `voucher`, `br`.`total_reg` AS `total_reg`, `br`.`notif_mail` AS `notif_mail`, `br`.`notif_wa` AS `notif_wa`, `br`.`status_bill_reg` AS `status_bill_reg`, `br`.`payment_type` AS `payment_type`, `br`.`no_rekening` AS `no_rekening`, `br`.`payment_post` AS `payment_post`, `br`.`payment_respond_post` AS `payment_respond_post`, `br`.`payment_publish` AS `payment_publish`, `br`.`payment_respond_process` AS `payment_respond_process`, `br`.`payment_process` AS `payment_process`, `br`.`payment_respond_paid` AS `payment_respond_paid`, `br`.`payment_paid` AS `payment_paid`, `br`.`amount_paid` AS `amount_paid`, `br`.`cashback` AS `cashback`, `br`.`payment_respond_cancel` AS `payment_respond_cancel`, `br`.`payment_cancel` AS `payment_cancel`, `br`.`expiry` AS `expiry`, `br`.`merchant_type` AS `merchant_type`, `br`.`date_create` AS `date_create`, `br`.`user_create` AS `user_create`, `br`.`date_update` AS `date_update`, `br`.`user_update` AS `user_update`, `br`.`hide` AS `hide`, `br`.`islock` AS `islock`, `pt`.`desc_payment_type` AS `desc_payment_type`, `mb`.`nama_bank` AS `nama_bank`, `sb`.`desc_bill_reg` AS `desc_bill_reg`, `sh`.`desc_hide` AS `desc_hide`, `b`.`status_reg` AS `status_reg`, `b`.`desc_registrasi` AS `desc_registrasi`, `b`.`nik_penduduk` AS `nik_penduduk`, `b`.`nama_pelanggan` AS `nama_pelanggan`, `b`.`nama_penduduk` AS `nama_penduduk`, `b`.`nomor_hp` AS `nomor_hp`, `b`.`email` AS `email`, `b`.`jenis_kelamin` AS `jenis_kelamin`, `b`.`tanggal_lahir` AS `tanggal_lahir`, `b`.`pic` AS `pic`, `b`.`kode_kategori_bandwith` AS `kode_kategori_bandwith`, `b`.`nama_kategori_bandwith` AS `nama_kategori_bandwith`, `b`.`alias_nama_kategori` AS `alias_nama_kategori`, `b`.`biaya_reg` AS `biaya_reg`, `b`.`harga_bandwith` AS `harga_bandwith`, `b`.`kode_wilayah_kelurahan_pasang` AS `kode_wilayah_kelurahan_pasang`, `b`.`jenis_bangunan` AS `jenis_bangunan`, `b`.`alamat_pasang` AS `alamat_pasang`, `b`.`alamat_p` AS `alamat_p`, `b`.`periode_billing` AS `periode_billing`, `b`.`jns_notif` AS `jns_notif` FROM (((((`trx_billing_registrasi` `br` left join `view_batchjob` `b` on((`br`.`nomor_internet` = `b`.`nomor_internet`))) left join `m_status_bill_reg` `sb` on((`br`.`status_bill_reg` = `sb`.`status_bill_reg`))) left join `m_payment_type` `pt` on((`pt`.`payment_type` = `br`.`payment_type`))) left join `m_bank` `mb` on((`mb`.`no_rekening` = `br`.`no_rekening`))) left join `m_status_hide` `sh` on((`br`.`hide` = `sh`.`hide`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_divisi_jabatan`
--
DROP TABLE IF EXISTS `view_divisi_jabatan`;

CREATE VIEW `view_divisi_jabatan`  AS SELECT `d`.`kode_divisi` AS `kode_divisi`, `d`.`nama_divisi` AS `nama_divisi`, `d`.`desc_divisi` AS `desc_divisi`, `j`.`kode_jabatan` AS `kode_jabatan`, `j`.`nama_jabatan` AS `nama_jabatan`, `j`.`desc_jabatan` AS `desc_jabatan` FROM (`tb_m_jabatan` `j` left join `tb_m_divisi` `d` on((`j`.`kode_divisi` = `d`.`kode_divisi`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_karyawan`
--
DROP TABLE IF EXISTS `view_karyawan`;

CREATE VIEW `view_karyawan`  AS SELECT `k`.`kode_karyawan` AS `kode_karyawan`, `k`.`kantor` AS `kantor`, `k`.`nik` AS `nik`, `k`.`nip` AS `nip`, `k`.`nama_karyawan` AS `nama_karyawan`, `k`.`kode_jabatan` AS `kode_jabatan`, `jb`.`nama_jabatan` AS `nama_jabatan`, `jb`.`kode_divisi` AS `kode_divisi`, `jb`.`nama_divisi` AS `nama_divisi`, `jb`.`desc_jabatan` AS `desc_jabatan`, `k`.`jenis_kelamin` AS `jenis_kelamin`, `k`.`hp_karyawan` AS `hp_karyawan`, `k`.`kode_agama` AS `kode_agama`, `ag`.`nama_agama` AS `nama_agama`, `k`.`email_karyawan` AS `email_karyawan`, `k`.`email_msn` AS `email_msn`, `k`.`tempat_lahir` AS `tempat_lahir`, `k`.`tanggal_lahir` AS `tanggal_lahir`, `k`.`tempat_pendidikan_terakhir` AS `tempat_pendidikan_terakhir`, `k`.`kode_pendidikan` AS `kode_pendidikan`, `pd`.`nama_pendidikan` AS `nama_pendidikan`, `k`.`jmlh_tanggungan` AS `jmlh_tanggungan`, `k`.`desc_tanggungan` AS `desc_tanggungan`, `k`.`kode_golongan_darah` AS `kode_golongan_darah`, `gl`.`nama_golongan_darah` AS `nama_golongan_darah`, `k`.`kode_wilayah_kelurahan` AS `kode_wilayah_kelurahan`, `kl`.`nama_kelurahan` AS `nama_kelurahan`, `kl`.`kode_wilayah_kecamatan` AS `kode_wilayah_kecamatan`, `kl`.`nama_kecamatan` AS `nama_kecamatan`, `kl`.`kode_wilayah_kota` AS `kode_wilayah_kota`, `k`.`kota_kerja` AS `kota_kerja`, `kl`.`nama_kota` AS `nama_kota`, `kl`.`kode_provinsi` AS `kode_provinsi`, `kl`.`nama_provinsi` AS `nama_provinsi`, `k`.`ktp` AS `ktp`, `k`.`cv` AS `cv`, `k`.`foto` AS `foto`, `k`.`ijazah_pendidikan_terakhir` AS `ijazah_pendidikan_terakhir`, `k`.`alamat_asal` AS `alamat_asal`, `k`.`domisili` AS `domisili`, `k`.`kode_status_kawin` AS `kode_status_kawin`, `sk`.`nama_status_kawin` AS `nama_status_kawin`, `k`.`npwp` AS `npwp`, `k`.`bpjs` AS `bpjs`, `k`.`bank_rek` AS `bank_rek`, `k`.`no_rek` AS `no_rek`, `k`.`tanggal_masuk` AS `tanggal_masuk`, `k`.`tanggal_keluar` AS `tanggal_keluar`, `k`.`status_rumah` AS `status_rumah`, `k`.`kendaraan` AS `kendaraan`, `k`.`status_kontrak` AS `status_kontrak`, `k`.`berat` AS `berat`, `k`.`tinggi` AS `tinggi`, `k`.`sim` AS `sim`, `k`.`status_aktif` AS `status_aktif`, `k`.`date_create` AS `date_create`, `k`.`user_create` AS `user_create`, `k`.`user_update` AS `user_update`, `k`.`date_update` AS `date_update`, `k`.`uid` AS `uid` FROM ((((((`tb_m_karyawan` `k` left join `tb_m_agama` `ag` on((`k`.`kode_agama` = `ag`.`id_agama`))) left join `tb_m_golongan_darah` `gl` on((`k`.`kode_golongan_darah` = `gl`.`id_golongan_darah`))) left join `view_divisi_jabatan` `jb` on((`k`.`kode_jabatan` = `jb`.`kode_jabatan`))) left join `tb_m_pendidikan` `pd` on((`k`.`kode_pendidikan` = `pd`.`id_pendidikan`))) left join `tb_m_status_kawin` `sk` on((`k`.`kode_status_kawin` = `sk`.`id_status_kawin`))) left join `m_wilayah` `kl` on(((convert(`k`.`kode_wilayah_kelurahan` using utf8mb4) collate utf8mb4_unicode_ci) = `kl`.`kode_wilayah_kelurahan`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_pelanggan`
--
DROP TABLE IF EXISTS `view_pelanggan`;

CREATE VIEW `view_pelanggan`  AS SELECT `p`.`id_perusahaan` AS `nik_penduduk`, `p`.`id_perusahaan` AS `id_perusahaan`, `p`.`nama_perusahaan` AS `nama_perusahaan`, `p`.`no_telp_perusahaan` AS `no_telp_perusahaan`, `p`.`email_perusahaan` AS `email_perusahaan`, `p`.`nama_pic_teknis` AS `nama_pic_teknis`, `p`.`no_telp_pic_teknis` AS `no_telp_pic_teknis`, `p`.`email_pic_teknis` AS `email_pic_teknis`, `p`.`nama_pic_keuangan` AS `nama_pic_keuangan`, `p`.`no_telp_pic_keuangan` AS `no_telp_pic_keuangan`, `p`.`email_pic_keuangan` AS `email_pic_keuangan`, `p`.`jenis_perusahaan` AS `jenis_perusahaan`, `p`.`tanggal_registrasi` AS `tanggal_registrasi`, `p`.`nama_penduduk` AS `nama_penduduk`, `p`.`jenis_kelamin` AS `jenis_kelamin`, `p`.`tanggal_lahir` AS `tanggal_lahir`, `p`.`pic` AS `pic`, `p`.`email` AS `email`, `p`.`nomor_hp` AS `nomor_hp`, `p`.`nomor_hp_2` AS `nomor_hp_2`, `p`.`kode_wilayah_kelurahan_ktp` AS `kode_wilayah_kelurahan_ktp`, `p`.`rt_ktp` AS `rt_ktp`, `p`.`rw_ktp` AS `rw_ktp`, `p`.`alamat_ktp` AS `alamat_ktp`, `p`.`alamat_ktp` AS `alamat_perusahaan`, `p`.`user_create` AS `user_create`, `p`.`date_create` AS `date_create`, `p`.`date_update` AS `date_update`, `p`.`user_update` AS `user_update`, `p`.`hide` AS `hide`, `w`.`nama_kelurahan` AS `nama_kelurahan`, `w`.`nama_kecamatan` AS `nama_kecamatan`, `w`.`nama_kota` AS `nama_kota`, `w`.`nama_provinsi` AS `nama_provinsi`, `h`.`desc_hide` AS `desc_hide` FROM ((`m_pelanggan` `p` left join `m_wilayah` `w` on((`p`.`kode_wilayah_kelurahan_ktp` = `w`.`kode_wilayah_kelurahan`))) left join `m_status_hide` `h` on((`p`.`hide` = `h`.`hide`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_pengguna`
--
DROP TABLE IF EXISTS `view_pengguna`;

CREATE VIEW `view_pengguna`  AS SELECT `p`.`kode_pengguna` AS `kode_pengguna`, `p`.`kode_karyawan` AS `kode_karyawan`, `k`.`nama_karyawan` AS `nama_karyawan`, `p`.`kode_level` AS `kode_level`, `l`.`level` AS `level`, `l`.`nama_level` AS `nama_level`, `p`.`username` AS `username`, `p`.`password` AS `password`, `p`.`status_aktif` AS `status_aktif`, `p`.`as_sales` AS `as_sales`, `k`.`nip` AS `nip`, `k`.`nama_jabatan` AS `nama_jabatan`, `k`.`kode_divisi` AS `kode_divisi`, `k`.`nama_divisi` AS `nama_divisi`, `k`.`foto` AS `foto`, `k`.`kota_kerja` AS `kota_kerja`, `p`.`last_ip` AS `last_ip`, `p`.`las_login` AS `las_login` FROM ((`tb_pengguna` `p` left join `tb_m_level_pengguna` `l` on((`l`.`kode_level` = `p`.`kode_level`))) left join `view_karyawan` `k` on((`k`.`kode_karyawan` = `p`.`kode_karyawan`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_suspend`
--
DROP TABLE IF EXISTS `view_suspend`;

CREATE VIEW `view_suspend`  AS SELECT `s`.`kode_suspend` AS `kode_suspend`, `s`.`nomor_internet` AS `nomor_internet`, `b`.`status_reg` AS `status_reg`, `b`.`desc_registrasi` AS `desc_registrasi`, `b`.`nik_penduduk` AS `nik_penduduk`, `b`.`nama_pelanggan` AS `nama_pelanggan`, `b`.`nama_penduduk` AS `nama_penduduk`, `b`.`nomor_hp` AS `nomor_hp`, `b`.`email` AS `email`, `b`.`jenis_kelamin` AS `jenis_kelamin`, `b`.`kode_wilayah_kota_pasang` AS `kode_wilayah_kota_pasang`, `b`.`pic` AS `pic`, `b`.`nama_kategori_bandwith` AS `nama_kategori_bandwith`, `b`.`nominal_bandwith` AS `nominal_bandwith`, `b`.`jenis_bangunan` AS `jenis_bangunan`, `b`.`alamat_p` AS `alamat_p`, `b`.`harga_bandwith` AS `harga_bandwith`, `b`.`user_create` AS `sales_create`, `s`.`suspend_start` AS `suspend_start`, `s`.`suspend_end` AS `suspend_end`, `s`.`status_suspend` AS `status_suspend`, `ss`.`desc_status_suspend` AS `desc_status_suspend`, `s`.`desc_suspend` AS `desc_suspend`, `s`.`date_create` AS `date_create`, `s`.`user_create` AS `user_create`, `s`.`date_update` AS `date_update`, `s`.`user_update` AS `user_update`, `s`.`hide` AS `hide` FROM ((`trx_suspend` `s` left join `m_status_suspend` `ss` on((`s`.`status_suspend` = `ss`.`status_suspend`))) left join `view_batchjob` `b` on((`s`.`nomor_internet` = `b`.`nomor_internet`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_terminasi`
--
DROP TABLE IF EXISTS `view_terminasi`;

CREATE VIEW `view_terminasi`  AS SELECT `tr`.`kode_trx_terminasi` AS `kode_trx_terminasi`, `tr`.`nomor_internet` AS `nomor_internet`, `tr`.`note_termin` AS `note_termin`, `tr`.`note_termin_cancel` AS `note_termin_cancel`, `tr`.`status_terminasi` AS `status_terminasi`, `tr`.`date_collect_start` AS `date_collect_start`, `tr`.`team_collect` AS `team_collect`, `tr`.`time_collect_start` AS `time_collect_start`, `tr`.`note_collect_start` AS `note_collect_start`, `tr`.`date_collect_finish` AS `date_collect_finish`, `tr`.`note_collect_finish` AS `note_collect_finish`, `tr`.`date_closing` AS `date_closing`, `tr`.`doc_terminasi` AS `doc_terminasi`, `tr`.`collect_perangkat` AS `collect_perangkat`, `tr`.`collect_payment` AS `collect_payment`, `tr`.`date_create` AS `date_create`, `tr`.`date_update` AS `date_update`, `tr`.`user_create` AS `user_create`, `tr`.`user_update` AS `user_update`, `tr`.`hide` AS `hide`, `vb`.`kode_wilayah_kota_pasang` AS `kode_wilayah_kota_pasang`, `vb`.`nik_penduduk` AS `nik_penduduk`, `vb`.`nama_pelanggan` AS `nama_pelanggan`, `vb`.`nama_penduduk` AS `nama_penduduk`, `vb`.`nomor_hp` AS `nomor_hp`, `vb`.`email` AS `email`, `vb`.`jenis_kelamin` AS `jenis_kelamin`, `vb`.`pic` AS `pic`, `vb`.`alamat_p` AS `alamat_p`, `vb`.`jenis_bangunan` AS `jenis_bangunan`, `vb`.`nominal_bandwith` AS `nominal_bandwith`, `vb`.`nama_kategori_bandwith` AS `nama_kategori_bandwith`, `vb`.`user_create` AS `sales_create`, `vb`.`harga_bandwith` AS `harga_bandwith`, `st`.`desc_terminasi` AS `desc_terminasi` FROM ((`trx_terminasi` `tr` left join `view_batchjob` `vb` on((`tr`.`nomor_internet` = `vb`.`nomor_internet`))) left join `m_status_terminasi` `st` on((`tr`.`status_terminasi` = `st`.`status_terminasi`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_ubah_layanan`
--
DROP TABLE IF EXISTS `view_ubah_layanan`;

CREATE VIEW `view_ubah_layanan`  AS SELECT `tul`.`kode_trx_ubah_layanan` AS `kode_trx_ubah_layanan`, `bj`.`nama_pelanggan` AS `nama_pelanggan`, `bj`.`user_create` AS `sales_create`, `bj`.`kode_wilayah_kota_pasang` AS `kode_wilayah_kota_pasang`, `tul`.`nomor_internet` AS `nomor_internet`, `tul`.`kode_bandwith_lama` AS `kode_bandwith_lama`, `vb1`.`nama_kategori_bandwith` AS `nama_kategori_bandwith_lama`, `vb1`.`alias_nama_kategori` AS `alias_nama_kategori_lama`, `vb1`.`nominal_bandwith` AS `nominal_bandwith_lama`, `tul`.`kode_bandwith_baru` AS `kode_bandwith_baru`, `vb2`.`nama_kategori_bandwith` AS `nama_kategori_bandwith_baru`, `vb2`.`alias_nama_kategori` AS `alias_nama_kategori_baru`, `vb2`.`nominal_bandwith` AS `nominal_bandwith_baru`, `tul`.`status_ubah_layanan` AS `status_ubah_layanan`, `su`.`desc_ubah_layanan` AS `desc_ubah_layanan`, `tul`.`date_request` AS `date_request`, `tul`.`date_closing` AS `date_closing`, `tul`.`doc_ubahlayanan` AS `doc_ubahlayanan`, `tul`.`date_create` AS `date_create`, `tul`.`user_create` AS `user_create`, `tul`.`date_update` AS `date_update`, `tul`.`user_update` AS `user_update`, `tul`.`hide` AS `hide`, `tul`.`note_request` AS `note_request`, `tul`.`date_schedule` AS `date_schedule`, `tul`.`note_closing` AS `note_closing`, `tul`.`note_schedule` AS `note_schedule`, `tul`.`date_cancel` AS `date_cancel`, `tul`.`note_cancel` AS `note_cancel` FROM ((((`trx_ubah_layanan` `tul` left join `view_bandwith` `vb1` on(((convert(`tul`.`kode_bandwith_lama` using utf8mb4) collate utf8mb4_unicode_ci) = `vb1`.`kode_bandwith`))) left join `view_bandwith` `vb2` on(((convert(`tul`.`kode_bandwith_baru` using utf8mb4) collate utf8mb4_unicode_ci) = `vb2`.`kode_bandwith`))) left join `m_status_ubahlayanan` `su` on((`tul`.`status_ubah_layanan` = `su`.`status_ubah_layanan`))) left join `view_batchjob` `bj` on((`bj`.`nomor_internet` = `tul`.`nomor_internet`))) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `m_bandwith`
--
ALTER TABLE `m_bandwith`
  ADD CONSTRAINT `fk_m_bandwith_m_bandwith_kategori_1` FOREIGN KEY (`kode_kategori_bandwith`) REFERENCES `m_bandwith_kategori` (`kode_kategori_bandwith`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_m_bandwith_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `kode_kategori_bandwith_2` FOREIGN KEY (`kode_kategori_bandwith`) REFERENCES `m_bandwith_kategori` (`kode_kategori_bandwith`);

--
-- Constraints for table `m_bandwith_kategori`
--
ALTER TABLE `m_bandwith_kategori`
  ADD CONSTRAINT `fk_m_bandwith_kategori_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_barang`
--
ALTER TABLE `m_barang`
  ADD CONSTRAINT `fk_m_barang_m_jns_barang_1` FOREIGN KEY (`kode_jns_barang`) REFERENCES `m_jns_barang` (`kode_jns_barang`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_m_barang_m_status_hide_2` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_jns_bangunan`
--
ALTER TABLE `m_jns_bangunan`
  ADD CONSTRAINT `fk_m_jns_bangunan_m_status_hide_3` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_jns_barang`
--
ALTER TABLE `m_jns_barang`
  ADD CONSTRAINT `fk_m_jns_barang_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_kat_team`
--
ALTER TABLE `m_kat_team`
  ADD CONSTRAINT `fk_m_kat_team_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_layanan_bangunan`
--
ALTER TABLE `m_layanan_bangunan`
  ADD CONSTRAINT `fk_m_layanan_bangunan_m_bandwith_kategori_1` FOREIGN KEY (`kode_kategori_bandwith`) REFERENCES `m_bandwith_kategori` (`kode_kategori_bandwith`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_m_layanan_bangunan_m_jns_bangunan_1` FOREIGN KEY (`kode_bangunan`) REFERENCES `m_jns_bangunan` (`kode_bangunan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_m_layanan_bangunan_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_media_akses`
--
ALTER TABLE `m_media_akses`
  ADD CONSTRAINT `fk_m_media_akses_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_payment_type`
--
ALTER TABLE `m_payment_type`
  ADD CONSTRAINT `fk_hide_m_status_payment_2` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_pelanggan`
--
ALTER TABLE `m_pelanggan`
  ADD CONSTRAINT `fk_m_pelanggan_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_pelanggan_old`
--
ALTER TABLE `m_pelanggan_old`
  ADD CONSTRAINT `m_pelanggan_old_ibfk_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_periode`
--
ALTER TABLE `m_periode`
  ADD CONSTRAINT `fk_m_periode_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_pop`
--
ALTER TABLE `m_pop`
  ADD CONSTRAINT `fk_m_pop_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_status_bill_lay`
--
ALTER TABLE `m_status_bill_lay`
  ADD CONSTRAINT `m_status_bill_lay_ibfk_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_status_bill_reg`
--
ALTER TABLE `m_status_bill_reg`
  ADD CONSTRAINT `fk_m_status_bill_reg_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_status_instalasi_barang`
--
ALTER TABLE `m_status_instalasi_barang`
  ADD CONSTRAINT `fk_m_status_instalasi_barang2_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`);

--
-- Constraints for table `m_status_perangkat`
--
ALTER TABLE `m_status_perangkat`
  ADD CONSTRAINT `fk_m_status_perangkat_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_status_registrasi`
--
ALTER TABLE `m_status_registrasi`
  ADD CONSTRAINT `fk_m_status_registrasi_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_status_suspend`
--
ALTER TABLE `m_status_suspend`
  ADD CONSTRAINT `fk_m_status_suspend_m_status_hide_2` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`);

--
-- Constraints for table `m_status_terminasi`
--
ALTER TABLE `m_status_terminasi`
  ADD CONSTRAINT `fk_m_status_terminasi_m_status_hide_2` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_status_ubahlayanan`
--
ALTER TABLE `m_status_ubahlayanan`
  ADD CONSTRAINT `fk_m_status_ubahlayanan_m_status_hide_2` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `m_time_job`
--
ALTER TABLE `m_time_job`
  ADD CONSTRAINT `fk_m_time_job_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_batchjob_register`
--
ALTER TABLE `trx_batchjob_register`
  ADD CONSTRAINT `fk_trx_batchjob_register_m_bandwith_1` FOREIGN KEY (`kode_bandwith`) REFERENCES `m_bandwith` (`kode_bandwith`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_batchjob_register_m_pelanggan_1` FOREIGN KEY (`id_perusahaan`) REFERENCES `m_pelanggan` (`id_perusahaan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_batchjob_register_m_pop_1` FOREIGN KEY (`kode_pop`) REFERENCES `m_pop` (`kode_pop`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_batchjob_register_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_batchjob_register_m_status_registrasi_1` FOREIGN KEY (`status_reg`) REFERENCES `m_status_registrasi` (`status_reg`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_batchjob_register_m_wilayah_2` FOREIGN KEY (`kode_wilayah_kelurahan_pasang`) REFERENCES `m_wilayah` (`kode_wilayah_kelurahan`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_batchjob_register_log`
--
ALTER TABLE `trx_batchjob_register_log`
  ADD CONSTRAINT `fk_trx_batchjob_register_log_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_batchjob_register_log_m_status_registrasi_1` FOREIGN KEY (`status_reg`) REFERENCES `m_status_registrasi` (`status_reg`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_batchjob_register_log_trx_batchjob_register_1` FOREIGN KEY (`nomor_internet`) REFERENCES `trx_batchjob_register` (`nomor_internet`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_batchjob_register_old`
--
ALTER TABLE `trx_batchjob_register_old`
  ADD CONSTRAINT `trx_batchjob_register_old_ibfk_1` FOREIGN KEY (`kode_bandwith`) REFERENCES `m_bandwith` (`kode_bandwith`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `trx_batchjob_register_old_ibfk_3` FOREIGN KEY (`kode_pop`) REFERENCES `m_pop` (`kode_pop`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `trx_batchjob_register_old_ibfk_4` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `trx_batchjob_register_old_ibfk_5` FOREIGN KEY (`status_reg`) REFERENCES `m_status_registrasi` (`status_reg`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `trx_batchjob_register_old_ibfk_6` FOREIGN KEY (`kode_wilayah_kelurahan_pasang`) REFERENCES `m_wilayah` (`kode_wilayah_kelurahan`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_billing_layanan`
--
ALTER TABLE `trx_billing_layanan`
  ADD CONSTRAINT `trx_billing_layanan_ibfk_1` FOREIGN KEY (`kode_bandwith`) REFERENCES `m_bandwith` (`kode_bandwith`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `trx_billing_layanan_ibfk_2` FOREIGN KEY (`status_bill_lay`) REFERENCES `m_status_bill_lay` (`status_bill_lay`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `trx_billing_layanan_ibfk_3` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `trx_billing_layanan_ibfk_4` FOREIGN KEY (`nomor_internet`) REFERENCES `trx_batchjob_register` (`nomor_internet`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_billing_layanan_detail`
--
ALTER TABLE `trx_billing_layanan_detail`
  ADD CONSTRAINT `trx_billing_layanan_detail_ibfk_2` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `trx_billing_layanan_detail_ibfk_3` FOREIGN KEY (`kode_billing_layanan`) REFERENCES `trx_billing_layanan` (`kode_billing_layanan`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_billing_layanan_log`
--
ALTER TABLE `trx_billing_layanan_log`
  ADD CONSTRAINT `trx_billing_layanan_log_ibfk_1` FOREIGN KEY (`status_bill_lay`) REFERENCES `m_status_bill_lay` (`status_bill_lay`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `trx_billing_layanan_log_ibfk_2` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `trx_billing_layanan_log_ibfk_3` FOREIGN KEY (`kode_billing_layanan`) REFERENCES `trx_billing_layanan` (`kode_billing_layanan`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_billing_registrasi`
--
ALTER TABLE `trx_billing_registrasi`
  ADD CONSTRAINT `fk_trx_billing_registrasi_m_bandwith_1` FOREIGN KEY (`kode_bandwith`) REFERENCES `m_bandwith` (`kode_bandwith`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_billing_registrasi_m_status_bill_reg_1` FOREIGN KEY (`status_bill_reg`) REFERENCES `m_status_bill_reg` (`status_bill_reg`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_billing_registrasi_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_billing_registrasi_trx_batchjob_register_1` FOREIGN KEY (`nomor_internet`) REFERENCES `trx_batchjob_register` (`nomor_internet`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_billing_registrasi_detail`
--
ALTER TABLE `trx_billing_registrasi_detail`
  ADD CONSTRAINT `fk_trx_billing_registrasi_detail_m_barang_1` FOREIGN KEY (`kode_barang`) REFERENCES `m_barang` (`kode_barang`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_billing_registrasi_detail_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_billing_registrasi_detail_trx_billing_registrasi_1` FOREIGN KEY (`kode_billing_registrasi`) REFERENCES `trx_billing_registrasi` (`kode_billing_registrasi`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_billing_registrasi_log`
--
ALTER TABLE `trx_billing_registrasi_log`
  ADD CONSTRAINT `fk_trx_billing_registrasi_log_m_status_bill_reg_1` FOREIGN KEY (`status_bill_reg`) REFERENCES `m_status_bill_reg` (`status_bill_reg`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_billing_registrasi_log_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_billing_registrasi_log_trx_billing_registrasi_1` FOREIGN KEY (`kode_billing_registrasi`) REFERENCES `trx_billing_registrasi` (`kode_billing_registrasi`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_instalasi`
--
ALTER TABLE `trx_instalasi`
  ADD CONSTRAINT `fk_trx_instalasi_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_instalasi_trx_batchjob_register_1` FOREIGN KEY (`nomor_internet`) REFERENCES `trx_batchjob_register` (`nomor_internet`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_instalasi_barang`
--
ALTER TABLE `trx_instalasi_barang`
  ADD CONSTRAINT `fk_trx_instalasi_barang_m_barang_1` FOREIGN KEY (`kode_barang`) REFERENCES `m_barang` (`kode_barang`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_instalasi_barang_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_instalasi_barang_m_status_instalasi_barang2_1` FOREIGN KEY (`status_instalasi_barang`) REFERENCES `m_status_instalasi_barang` (`status_instalasi_barang`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_instalasi_barang_trx_batchjob_register_1` FOREIGN KEY (`nomor_internet`) REFERENCES `trx_batchjob_register` (`nomor_internet`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_suspend`
--
ALTER TABLE `trx_suspend`
  ADD CONSTRAINT `fk_trx_suspend_m_status_hide_2` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`),
  ADD CONSTRAINT `fk_trx_suspend_m_status_suspend_1` FOREIGN KEY (`status_suspend`) REFERENCES `m_status_suspend` (`status_suspend`);

--
-- Constraints for table `trx_suspend_log`
--
ALTER TABLE `trx_suspend_log`
  ADD CONSTRAINT `fk_trx_suspend_log_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`),
  ADD CONSTRAINT `fk_trx_suspend_log_m_status_suspend_1` FOREIGN KEY (`status_suspend`) REFERENCES `m_status_suspend` (`status_suspend`),
  ADD CONSTRAINT `fk_trx_suspend_log_trx_suspend_kode_suspend_2` FOREIGN KEY (`kode_suspend`) REFERENCES `trx_suspend` (`kode_suspend`);

--
-- Constraints for table `trx_terminasi`
--
ALTER TABLE `trx_terminasi`
  ADD CONSTRAINT `fk_trx_terminasi_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_terminasi_m_status_terminasi_1` FOREIGN KEY (`status_terminasi`) REFERENCES `m_status_terminasi` (`status_terminasi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_terminasi_trx_batchjob_register_1` FOREIGN KEY (`nomor_internet`) REFERENCES `trx_batchjob_register` (`nomor_internet`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_terminasi_log`
--
ALTER TABLE `trx_terminasi_log`
  ADD CONSTRAINT `fk_trx_terminasi_log_m_status_hide_2` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_terminasi_log_m_status_terminasi_2` FOREIGN KEY (`status_terminasi`) REFERENCES `m_status_terminasi` (`status_terminasi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_terminasi_log_trx_terminasi_2` FOREIGN KEY (`kode_trx_terminasi`) REFERENCES `trx_terminasi` (`kode_trx_terminasi`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trx_ubah_layanan`
--
ALTER TABLE `trx_ubah_layanan`
  ADD CONSTRAINT `fk_trx_ubah_layanan_m_bandwith_1` FOREIGN KEY (`kode_bandwith_lama`) REFERENCES `m_bandwith` (`kode_bandwith`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_ubah_layanan_m_bandwith_2` FOREIGN KEY (`kode_bandwith_baru`) REFERENCES `m_bandwith` (`kode_bandwith`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_ubah_layanan_m_status_hide_2` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_ubah_layanan_trx_batchjob_register_1` FOREIGN KEY (`nomor_internet`) REFERENCES `trx_batchjob_register` (`nomor_internet`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `status_ubah_layanan` FOREIGN KEY (`status_ubah_layanan`) REFERENCES `m_status_ubahlayanan` (`status_ubah_layanan`);

--
-- Constraints for table `trx_ubah_layanan_log`
--
ALTER TABLE `trx_ubah_layanan_log`
  ADD CONSTRAINT `fk_trx_ubah_layanan_log_m_status_hide_2` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_ubah_layanan_log_m_status_ubahlayanan_1` FOREIGN KEY (`status_ubah_layanan`) REFERENCES `m_status_ubahlayanan` (`status_ubah_layanan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_trx_ubah_layanan_log_trx_ubah_layanan_1` FOREIGN KEY (`kode_trx_ubah_layanan`) REFERENCES `trx_ubah_layanan` (`kode_trx_ubah_layanan`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
