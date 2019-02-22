<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<?php
switch($tp){

case 'index':
?>

<div class="primary">
    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <h1 class="page-title">Hibah</h1>

        <?php   
        $limit = 15;
        $p = $p ? $p : 1;
        $position = ($p -1) * $limit;
        $this->db->_protect_identifiers=false;
        ?>

        <table class="table-global">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NPHD</th>
                    <th>SPP-LS</th>
                    <th>SPP-LS Hibah</th>
                    <th>Rekap</th>
                    <th>Realisasi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $Qlist = $this->db->query("SELECT a.surat_id, a.uraian, b.nphd_id, a.sppls_status, a.hibah_status, a.rekap_status
                                        FROM surat_masuk a
                                        LEFT JOIN nphd b ON b.surat_id=a.surat_id
                                        WHERE a.jenis_bantuan='1' ORDER BY a.surat_id DESC LIMIT $position,$limit");                

                if($Qlist->num_rows){
                    $i = ($p*15)-14;

                    foreach($Qlist->result_object() as $list){                        
                        if($list->sppls_status==NULL && $_SESSION['internal']['role']==3 || $list->sppls_status==NULL && $_SESSION['internal']['role']==0) $sppls = '<a href="'.site_url('hibah/sppls/'.$list->surat_id).'">PROSES</a>';
                        elseif($list->sppls_status==1 && $_SESSION['internal']['role']==4 || $list->sppls_status==1 && $_SESSION['internal']['role']==0) $sppls = '<a href="'.site_url('hibah/sppls/'.$list->surat_id).'">PROSES</a>';
                        elseif($list->sppls_status==2 && $_SESSION['internal']['role']==5 || $list->sppls_status==2 && $_SESSION['internal']['role']==0) $sppls = '<a href="'.site_url('hibah/sppls/'.$list->surat_id).'">PROSES</a>';
                        elseif($list->sppls_status==3 && $_SESSION['internal']['role']==6 || $list->sppls_status==3 && $_SESSION['internal']['role']==0) $sppls = '<a href="'.site_url('hibah/sppls/'.$list->surat_id).'">PROSES</a>';
                        elseif($list->sppls_status==4) $sppls = '<a href="'.base_url('process/pdf/export/'.$this->ifunction->indonesian_date(date('d M Y')).' - SPP-LS/3/'.$list->surat_id).'">CETAK</a>';
                        elseif($list->sppls_status==NULL) $sppls = 'PROSES'; 
                        else $sppls = 'SELESAI';  
                        
                        if($list->hibah_status==NULL && $_SESSION['internal']['role']==7 || $list->hibah_status==NULL && $_SESSION['internal']['role']==0) $hibah = '<a href="'.site_url('hibah/hibah/'.$list->surat_id).'">PROSES</a>';
                        elseif($list->hibah_status==1 && $_SESSION['internal']['role']==8 || $list->hibah_status==1 && $_SESSION['internal']['role']==0) $hibah = '<a href="'.site_url('hibah/hibah/'.$list->surat_id).'">PROSES</a>';
                        elseif($list->hibah_status==2) $hibah = '<a href="'.base_url('process/pdf/export/'.$this->ifunction->indonesian_date(date('d M Y')).' - SPP-LS Hibah/5/'.$list->surat_id).'">CETAK</a>'; 
                        elseif($list->hibah_status==NULL) $hibah = 'PROSES';
                        else $hibah = 'SELESAI';

                        if($list->rekap_status==NULL && $_SESSION['internal']['role']==0) $rekap = '<a href="'.site_url('hibah/rekap/'.$list->surat_id).'">PROSES</a>';
                        elseif($list->rekap_status==NULL) $rekap = 'PROSES';
                        else $rekap = '<a href="'.base_url('process/pdf/export/'.$this->ifunction->indonesian_date(date('d M Y')).' - Rekap/6/0/landscape').'">CETAK</a>';                       

                        echo '<tr>
                                <td>'.$i.'</td>
                                <td>'.$list->uraian.'</td>
                                <td style="text-align:center">'; if($list->nphd_id==NULL) echo '<a href="'.site_url('hibah/nphd/'.$list->surat_id).'">PROSES</a>'; else echo '<a href="'.base_url('process/pdf/export/'.$this->ifunction->indonesian_date(date('d M Y')).' - NPHD/2/'.$list->nphd_id).'">CETAK</a>'; echo '</td>
                                <td style="text-align:center">'.$sppls.'</td>
                                <td style="text-align:center">'.$hibah.'</td>
                                <td style="text-align:center">'.$rekap.'</td>
                                <td style="text-align:center">PROSES</td>
                            </tr>';
                        $i++;
                    }
                }
                ?>
            </tbody>
        </table>   

        <?php
        $Qpaging = $this->db->query("SELECT uraian FROM surat_masuk WHERE jenis_bantuan='1'");

        $num_page = ceil($Qpaging->num_rows / $limit);
        if($Qpaging->num_rows > $limit){
            $this->ifunction->paging($p, site_url('hibah').'/'.$tp.'/', $num_page, $Qpaging->num_rows, 'href', false);
        }
        ?>             
    </div>
    <!-- project-detail-wrapper -->
</div>
<!-- primary -->

<?php
break;

case 'nphd':
?>

<div class="primary">
    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <form action="<?php echo site_url('process/hibah/nphd/'.$p) ?>" enctype="multipart/form-data" method="post" class="form-check form-global">
            <h1 class="page-title">Tambah NPHD</h1>

            <div class="col-wrapper clearfix">
                <div class="control-group">
                    <label class="control-label" for="">Nomor</label>
                    <div class="controls">
                        <input type="text" name="nomor" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Tanggal</label>
                    <div class="controls">
                        <input id="datepicker-tgl" type="text" name="tanggal" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Nama</label>
                    <div class="controls">
                        <input type="text" name="nama" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">No. KTP</label>
                    <div class="controls">
                        <input type="text" name="ktp" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Jabatan</label>
                    <div class="controls">
                        <input type="text" name="jabatan" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Alamat</label>
                    <div class="controls">
                        <textarea name="alamat" required></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Atas Nama</label>
                    <div class="controls">
                        <input type="text" name="atas_nama" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Jumlah Hibah</label>
                    <div class="controls">
                        <input type="number" name="jumlah" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Kegunaan Hibah</label>
                    <div class="controls">
                        <textarea name="guna" required></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Tujuan Hibah</label>
                    <div class="controls">
                        <textarea name="tujuan" required></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Rekening Bank</label>
                    <div class="controls">
                        <input type="text" name="nama_rek" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">No Rekening</label>
                    <div class="controls">
                        <input type="text" name="no_rek" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Atas Nama Rekening</label>
                    <div class="controls">
                        <input type="text" name="atas_rek" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Uraian Kegiatan/Penggunaan</label>
                    <div class="controls file">
                        <input type="file" name="file" required> &nbsp <a style="font-size:12px;" href="<?php echo base_url('media/Template.xls') ?>">Download Template</a>
                    </div>
                </div>
            </div>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Tambah" />
                <a href="<?php echo site_url('hibah') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>             
    </div>
    <!-- project-detail-wrapper -->
</div>
<!-- primary -->

<?php
break;

case 'rekap':
?>

<div class="primary">
    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <form action="<?php echo site_url('process/hibah/rekap/'.$p) ?>" method="post" class="form-check form-global">
            <h1 class="page-title">Tambah Hibah</h1>

            <div class="col-wrapper clearfix">
                <div class="control-group">
                    <label class="control-label" for="">Tanggal SPP</label>
                    <div class="controls">
                        <input id="datepicker-tgl" type="text" name="tanggal" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Nomor SPP</label>
                    <div class="controls">
                        <input type="text" name="nomor" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Dari</label>
                    <div class="controls">
                        <input type="text" name="dari" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Alamat</label>
                    <div class="controls">
                        <textarea name="alamat" required></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Jabatan</label>
                    <div class="controls">
                        <input type="text" name="jabatan" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Uraian</label>
                    <div class="controls">
                        <textarea name="uraian" required></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Nilai</label>
                    <div class="controls">
                        <input type="number" name="nilai" required>
                    </div>
                </div>
            </div>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Tambah" />
                <a href="<?php echo site_url('hibah') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>             
    </div>
    <!-- project-detail-wrapper -->
</div>
<!-- primary -->

<?php
break;

case 'sppls':
?>

<div class="primary">
    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <form action="<?php echo site_url('process/hibah/sppls/'.$p) ?>" method="post" class="form-check form-global">
            <h1 class="page-title">Kartu Kendali Dokumen</h1>

            <?php if($_SESSION['internal']['role']==3 || $_SESSION['internal']['role']==0){ ?>
            <input type="hidden" name="activity[]" value="belanja_pegawai">
            <input type="hidden" name="status[]" value="1">

            <div class="col-wrapper clearfix">
                <div class="control-group">
                    <label class="control-label" for="">Nama Lembaga</label>
                    <div class="controls">
                        <input type="text" name="isi[]" required>
                    </div>
                </div>                

                <div class="control-group">
                    <label class="control-label" for="">Alamat</label>
                    <div class="controls">
                        <textarea name="isi[]" required></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">No. Rekening Bank</label>
                    <div class="controls">
                        <input type="text" name="isi[]" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Tanggal SPP</label>
                    <div class="controls">
                        <input type="text" id="datepicker-tgl" name="isi[]" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">No. SPP</label>
                    <div class="controls">
                        <label style="width: 5%;display: inline;">991/</label><input type="text" name="isi[]" style="width:95%;float:right" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Nilai</label>
                    <div class="controls">
                        <input type="number" name="isi[]" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Uraian</label>
                    <div class="controls">
                        <textarea name="isi[]" required></textarea>
                    </div>
                </div>
            </div>

            <ul class="category-list list-nostyle">
                <li>
                    <h3>Belanja Pegawai/Subsidi/Bantuan Keuangan/Belanja Tidak Terduga</h3>                    
                </li>
                <?php
                $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 4)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

                foreach($Qlist1->result_object() as $list1){
                    echo '<li>
                            <label class="checkbox">
                                <input type="checkbox" name="ceklis[]" value="'.$list1->sppls_id.'">
                                '.$list1->judul.'
                            </label>
                        </li>';
                }
                ?>
            </ul>

            <?php }if($_SESSION['internal']['role']==4 || $_SESSION['internal']['role']==0){ ?>
            <input type="hidden" name="activity[]" value="belanja_hibah">
            <input type="hidden" name="status[]" value="2">

            <ul class="category-list list-nostyle">
                <li>
                    <h3>Belanja Hibah</h3>                    
                </li>
                <?php
                $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 5)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

                foreach($Qlist1->result_object() as $list1){
                    echo '<li>
                            <label class="checkbox">
                                <input type="checkbox" name="ceklis[]" value="'.$list1->sppls_id.'">
                                '.$list1->judul.'
                            </label>
                        </li>';
                }
                ?>
            </ul>

            <?php }if($_SESSION['internal']['role']==5 || $_SESSION['internal']['role']==0){ ?>
            <input type="hidden" name="activity[]" value="pengujian">
            <input type="hidden" name="status[]" value="3">

            <ul class="category-list list-nostyle">
                <li>
                    <h3>Pengujian</h3>                    
                </li>
                <?php
                $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 6)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

                foreach($Qlist1->result_object() as $list1){
                    echo '<li>
                            <label class="checkbox">
                                <input type="checkbox" name="ceklis[]" value="'.$list1->sppls_id.'">
                                '.$list1->judul.'
                            </label>
                        </li>';
                }
                ?>
            </ul>

            <?php }if($_SESSION['internal']['role']==6 || $_SESSION['internal']['role']==0){ ?>
            <input type="hidden" name="activity[]" value="cetak_spp">
            <input type="hidden" name="status[]" value="4">

            <ul class="category-list list-nostyle">
                <li>
                    <h3>Cetak SPP</h3>                    
                </li>
                <?php
                $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 7)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

                foreach($Qlist1->result_object() as $list1){
                    echo '<li>
                            <label class="checkbox">
                                <input type="checkbox" name="ceklis[]" value="'.$list1->sppls_id.'">
                                '.$list1->judul.'
                            </label>
                        </li>';
                }
                ?>
            </ul>

            <?php } ?>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Tambah" />
                <a href="<?php echo site_url('hibah') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>             
    </div>
    <!-- project-detail-wrapper -->
</div>
<!-- primary -->

<?php
break;

case 'hibah':
?>

<div class="primary">
    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <form action="<?php echo site_url('process/hibah/hibah/'.$p) ?>" method="post" class="form-check form-global">
            <!-- <h1 class="page-title">Kartu Kendali Dokumen</h1> -->
            <h1 class="page-title">Pengujian Kelengkapan SPP-LS Khusus Belanja Hibah</h1>

            <?php if($_SESSION['internal']['role']==7 || $_SESSION['internal']['role']==0){ ?>
            <input type="hidden" name="activity[]" value="surat_pengantar">
            <input type="hidden" name="status[]" value="1">

            <!-- <ul class="category-list list-nostyle">
                <li>
                    <h3>Surat Pengantar SPP-LS</h3>                    
                </li>
            </ul> -->

            <div class="col-wrapper clearfix">
                <div class="control-group">
                    <label class="control-label" for="">Nama</label>
                    <div class="controls">
                        <input type="text" name="isi[]" required>
                    </div>
                </div>   

                <div class="control-group">
                    <label class="control-label" for="">Nomor</label>
                    <div class="controls">
                        <input type="text" name="isi[]" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Tanggal</label>
                    <div class="controls">
                        <input type="text" id="datepicker-tgl" name="isi[]" required>
                    </div>
                </div>             

                <div class="control-group">
                    <label class="control-label" for="">Uraian</label>
                    <div class="controls">
                        <textarea name="isi[]" required></textarea>
                    </div>
                </div>
            </div>

            <ul class="category-list list-nostyle">
                <?php
                $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 8)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

                foreach($Qlist1->result_object() as $list1){
                    echo '<li>
                            <label class="checkbox">
                                <input type="checkbox" name="ceklis[]" value="'.$list1->sppls_id.'">
                                '.$list1->judul.'
                            </label>
                        </li>';
                }
                ?>
            </ul>

            <?php }if($_SESSION['internal']['role']==8 || $_SESSION['internal']['role']==0){ ?>
            <input type="hidden" name="activity[]" value="verifikasi_spp">
            <input type="hidden" name="status[]" value="2">

            <ul class="category-list list-nostyle">
                <li>
                    <h3>Verifikasi SPP dan Penerbitan SPM</h3>                    
                </li>
                <?php
                $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 9)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

                foreach($Qlist1->result_object() as $list1){
                    echo '<li>
                            <label class="checkbox">
                                <input type="checkbox" name="ceklis[]" value="'.$list1->sppls_id.'">
                                '.$list1->judul.'
                            </label>
                        </li>';
                }
                ?>
            </ul>

            <?php } ?>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Tambah" />
                <a href="<?php echo site_url('hibah') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>             
    </div>
    <!-- project-detail-wrapper -->
</div>
<!-- primary -->

<?php
break;


case 'edit':

$Qedit = $this->db->query("SELECT * FROM surat_masuk WHERE `surat_id`='$p'"); $edit = $Qedit->result_object();
?>

<div class="primary">
    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <form action="<?php echo site_url('process/surat/edit/'.$p) ?>" method="post" class="form-check form-global">
            <h1 class="page-title">Edit Surat Masuk</h1>

            <div class="col-wrapper clearfix">
                <div class="control-group">
                    <label class="control-label" for="">Tanggal Masuk Berkas/Disposisi</label>
                    <div class="controls">
                        <input id="datepicker-tgl" type="text" name="tgl_masuk" value="<?php echo $edit[0]->tanggal_masuk ?>" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">No Kartu Kendali</label>
                    <div class="controls">
                        <input type="text" name="no_kartu" value="<?php echo $edit[0]->no_kartu ?>" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Uraian</label>
                    <div class="controls">
                        <textarea name="uraian" required><?php echo $edit[0]->uraian ?></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Alamat</label>
                    <div class="controls">
                        <textarea name="alamat" required><?php echo $edit[0]->alamat ?></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Jenis Bantuan</label>
                    <div class="controls">
                        <select name="jenis">
                            <option value="1" <?php if($edit[0]->jenis_bantuan==1) echo ' selected'; ?>>Hibah</option>
                            <option value="2" <?php if($edit[0]->jenis_bantuan==2) echo ' selected'; ?>>Bansos</option>
                            <option value="3" <?php if($edit[0]->jenis_bantuan==3) echo ' selected'; ?>>Subsidi</option>
                            <option value="4" <?php if($edit[0]->jenis_bantuan==4) echo ' selected'; ?>>BPJS PNS</option>
                            <option value="5" <?php if($edit[0]->jenis_bantuan==5) echo ' selected'; ?>>Bantuan Keuangan</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Edit" />
                <a href="<?php echo site_url('surat') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>             
    </div>
    <!-- project-detail-wrapper -->
</div>
<!-- primary -->

<?php
break;

}
?>

</div>
<!-- wrapper -->
</div>
<!-- content-main -->