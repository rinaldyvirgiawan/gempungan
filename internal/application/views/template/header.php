<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<header role="banner" class="header-main clearfix">
    <div class="wrapper">
        <a class="logo" href="<?php echo base_url() ?>">
            <img src="<?php echo base_url('static/img/logo.png') ?>" alt="" height="36" width="180">
        </a>
        <nav role="navigation" class="nav-main">
            <ul class="clearfix list-nostyle">                
                <?php
                // if(isset($_SESSION['internal'])){
                //     echo '<li>
                //         <a class="link-purple" href="'.site_url('peraturan').'">Kepwal</a>
                //     </li> 
                //     <li>
                //         <a class="link-green" href="'.site_url('lapor').'">Surat Masuk</a>
                //     </li>';                    
                // } 
                ?> 
            </ul>
        </nav>
        <!-- nav-main -->
        <div class="nav-user list-nostyle clearfix">
            <a class="logo-bandung" target="_blank" href="http://bandung.go.id/">
                <img src="<?php echo base_url('static/img/logo-bandung.png') ?>" alt="">
            </a>
            <?php
            if(isset($_SESSION['internal'])){
                echo '<a class="logo-bandung" href="'.site_url('logout').'" style="margin-top:3px;margin-right:10px" onclick="return confirm(\'Apakah Anda Yakin Akan Keluar ?\');" alt="Sign Out">
                    <img src="'.base_url('static/img/btn-user.png').'" alt="">
                </a>';
            }
            ?>
        </div>
        <!-- nav-user -->
    </div>
    <!-- wrapper -->
</header>
<!-- header-main -->