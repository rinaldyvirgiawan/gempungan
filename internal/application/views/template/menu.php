<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<div role="main" class="content-main" style="margin:120px 0 50px">
<div class="wrapper clearfix">
    <aside class="sidebar">
    <div class="widget-side">
        <h2><a href="<?php echo site_url(); ?>">Kepwal</a></h2>
    </div>
    <!-- widget-side -->
    <div class="widget-side">
        <h2><a href="<?php echo site_url('surat'); ?>">Surat Masuk</a></h2>
        <ul class="category-list list-nostyle">
            <li><a href="<?php echo site_url('hibah'); ?>">Hibah</a></li>   
            <li><a href="<?php echo site_url('bansos'); ?>">Bansos</a></li> 
            <li><a href="<?php echo site_url('subsidi'); ?>">Subsidi</a></li>
            <li><a href="<?php echo site_url('bpjs'); ?>">BPJS PNS</a></li>
            <li><a href="<?php echo site_url('bantuan'); ?>">Bantuan Keuangan</a></li>
        </ul>
    </div>
    <!-- widget-side -->
    <div class="widget-side nav-filter">
        <h2><a href="<?php echo site_url('pejabat'); ?>">Manajemen Aplikasi</a></h2>        
        <ul class="category-list list-nostyle">
            <li><a href="<?php echo site_url('pejabat'); ?>">Pejabat</a></li>   
            <li><a href="<?php echo site_url('pengguna'); ?>">Pengguna</a></li>
        </ul>
    </div>
</aside>
<!-- sidebar -->