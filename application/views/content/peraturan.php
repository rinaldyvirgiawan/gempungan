<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<div role="main" class="content-main" style="margin:120px 0 50px">
    <div class="about-page wrapper">
        <h1 class="page-title page-title-border">Peraturan</h1>
        <div class="col-wrapper clearfix">
            <!-- <div id="pdf" style="height:600px">
                <p>It appears you don't have Adobe Reader or PDF support in this web browser. <a href="smedia/Hibah_Bansos_Online_Sabilulungan.pdf">Click here to download the PDF</a></p>
            </div> -->

            <!-- <p><strong>Penggunaan Media Online sabilulungan.bandung.go.id Untuk Transparansi dan Akuntabilitas Penyaluran Dana Hibah dan Bantuan Sosial di Pemerintah Kota Bandung</strong></p>
            <p>Ringkasan singkat</p>
            <p>Penyaluran bantuan hibah dan bantuan sosial (bansos) telah menjadi masalah hukum nasional di Indonesia. Telah banyak kasus hukum terjadi akibat penggunaan dan penyalurannya, baik pemerintahan di tingkat pusat (Kementrian / Lembaga) maupun di Pemerintah Daerah Baik Provinsi, Kabupaten / Kota di Indonesia.</p>
            <p>Bantuan sosial dan Hibah konon disalahgunakan dengan &lsquo;kreatif&rsquo; untuk politik pencitraan oleh kepala daerah/wakil, terutama Kepala Daerah Incumbent yang mencalon kembali dalam ajang pemilukada untuk periode ke dua. Bisa juga disalahgunakan untuk para tim sukses yang dianggap telah berjasa dan dalam menggolkan kepala daerah/wakil yang sedang menjabat.</p>
            <p>Berdasarkan hasil kajian Komisi Pemberantasan Korupsi yang disampaikan oleh Direktur Dikyanmas KPK, Dedie A Rachim pada tanggal 21-22 November 2011 di Pontianak.</p>

            <p style="text-align:center;margin:100px 0 0">
                <a target="_blank" style="background:#0C88CE;color:#FFF;text-decoration:none;padding:20px" href="media/Hibah_Bansos_Online_Sabilulungan.pdf">LIHAT PERATURAN LEBIH LENGKAP</a>
            </p> -->

            <style type="text/css">
            .list li{
                text-transform: uppercase; 
            }
            </style>

            <ul class="list">
                <?php
                $Qlist = $this->db->query("SELECT title, content FROM cms WHERE page_id='peraturan' ORDER BY sequence ASC");

                foreach($Qlist->result_object() as $list){
                    echo '<li><a target="_blank" href="'.base_url('media/peraturan/'.$list->content).'">'.$list->title.'</a></li>';
                }
                ?>
                <!-- ------
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/01.02 SOP_Bendaharan Hibah dan Bantuan Sosial (Repaired).pdf'); ?>">SOP Bendaharan Hibah dan Bantuan Sosial (Repaired)</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/01.03 SK_PPK-PPKD_2016.doc'); ?>">SK PPK-PPKD 2016</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/1. PERMENDAGRI 32 TAHUN 2011.pdf'); ?>">PERMENDAGRI 32 TAHUN 2011</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/1. PERMENDAGRI 39 TAHUN 2012 PERUBAHAN ATAS PERATURAN MENTERI DALAM NEGERI NOMOR 32 TAHUN 2011 TENTANG PEDOMAN PEMBERIAN HIBAH DAN BANTUAN SOSIAL YANG BERSUMBER DARI ANGGARAN PENDAPATAN DAN BELANJA DAERAH.pdf'); ?>">PERMENDAGRI 39 TAHUN 2012 PERUBAHAN ATAS PERATURAN MENTERI DALAM NEGERI NOMOR 32 TAHUN 2011 TENTANG PEDOMAN PEMBERIAN HIBAH DAN BANTUAN SOSIAL YANG BERSUMBER DARI ANGGARAN PENDAPATAN DAN BELANJA DAERAH</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/2. PERWAL NO 891 TAHUN 2011 TENTANG HIBAH BANSOS .pdf'); ?>">PERWAL NO 891 TAHUN 2011 TENTANG HIBAH BANSOS</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/3. PERWAL NO 836 THN 2012 PERUBAHAN I PERWAL 891-2011 HIBAH BANSOS_doc.pdf'); ?>">PERWAL NO 836 THN 2012 PERUBAHAN I PERWAL 891-2011 HIBAH BANSOS</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/4. PERWAL NO 777 THN 2013 PERUBAHAN II PERWAL 891-2011 HIBAH BANSOS.pdf'); ?>">PERWAL NO 777 THN 2013 PERUBAHAN II PERWAL 891-2011 HIBAH BANSOS</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/5. a. PERWAL NO. 825 THN 2013 PERUBAHAN III PERWAL 891-2011 HIBAH BANSOS-evdok.pdf'); ?>">PERWAL NO. 825 THN 2013 PERUBAHAN III PERWAL 891-2011 HIBAH BANSOS-evdok</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/5. b. PERWAL NO. 825 THN 2013 PERUBAHAN III PERWAL 891-2011 HIBAH BANSOS LAMPIRAN.pdf'); ?>">PERWAL NO. 825 THN 2013 PERUBAHAN III PERWAL 891-2011 HIBAH BANSOS LAMPIRAN</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/6. PERWAL NO 1205 THN 2013 PERUBAHAN IV PERWAL 891-2011 HIBAH BANSOS.pdf'); ?>">PERWAL NO 1205 THN 2013 PERUBAHAN IV PERWAL 891-2011 HIBAH BANSOS</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/7. PERWAL NO. 309 THN 2014 PERUBAHAN V PERWAL 891-2011 HIBAH BANSOS.pdf'); ?>">PERWAL NO. 309 THN 2014 PERUBAHAN V PERWAL 891-2011 HIBAH BANSOS</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/8. PERWAL NO. 691 THN 2014 PERUBAHAN V PERWAL 891-2011 HIBAH BANSOS.pdf'); ?>">PERWAL NO. 691 THN 2014 PERUBAHAN V PERWAL 891-2011 HIBAH BANSOS</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/Hibah Bansos Online.docx'); ?>">Hibah Bansos Online</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/Peraturan_Walikota_Nomor_816_Tahun_2015.pdf'); ?>">Peraturan Walikota Nomor 816 Tahun 2015</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/SURAT EDARAN LPJ 2015.docx'); ?>">SURAT EDARAN LPJ 2015</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/Surat Edaran Menteri Dalam Negeri Nomor 9004627SJ Tentang Penajaman Ketentuan Pasal 298 Ayat (5) Undang-Undang Nomor 23 Tahun 2014 Tentang Pemerintahan Daerah.pdf'); ?>">Surat Edaran Menteri Dalam Negeri Nomor 9004627SJ Tentang Penajaman Ketentuan Pasal 298 Ayat (5) Undang-Undang Nomor 23 Tahun 2014 Tentang Pemerintahan Daerah</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/Surat Permberitahuan pemohon.docx'); ?>">Surat Permberitahuan Pemohon</a></li>
                <li><a target="_blank" href="<?php echo base_url('media/peraturan/Surat Permberitahuan SKPD Terkait.docx'); ?>">Surat Permberitahuan SKPD Terkait</a></li> -->
            </ul>
        </div>
    </div>
</div>
<!-- content-main -->