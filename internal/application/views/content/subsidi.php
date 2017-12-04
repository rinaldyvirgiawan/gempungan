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

        <h1 class="page-title">Subsidi</h1>

        <?php   
        $limit = 15;
        $p = $p ? $p : 1;
        $position = ($p -1) * $limit;
        $this->db->_protect_identifiers=false;
        ?>

        <table class="table-global">
            <thead>
                <tr>
                    <th width="10">No</th>
                    <th>Nama</th>
                    <th width="100">SPP-LS</th>
                    <th width="100">Realisasi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $Qlist = $this->db->query("SELECT a.surat_id, a.uraian, b.nphd_id, a.sppls_status, a.hibah_status, a.rekap_status
                                        FROM surat_masuk a
                                        LEFT JOIN nphd b ON b.surat_id=a.surat_id
                                        WHERE a.jenis_bantuan='3' ORDER BY a.surat_id DESC LIMIT $position,$limit");                

                if($Qlist->num_rows){
                    $i = ($p*15)-14;

                    foreach($Qlist->result_object() as $list){
                        if($list->sppls_status==NULL && $_SESSION['internal']['role']==3 || $list->sppls_status==NULL && $_SESSION['internal']['role']==0) $sppls = '<a href="'.site_url('subsidi/sppls/'.$list->surat_id).'">PROSES</a>';
                        elseif($list->sppls_status==1 && $_SESSION['internal']['role']==4 || $list->sppls_status==1 && $_SESSION['internal']['role']==0) $sppls = '<a href="'.site_url('subsidi/sppls/'.$list->surat_id).'">PROSES</a>';
                        elseif($list->sppls_status==2 && $_SESSION['internal']['role']==5 || $list->sppls_status==2 && $_SESSION['internal']['role']==0) $sppls = '<a href="'.site_url('subsidi/sppls/'.$list->surat_id).'">PROSES</a>';
                        elseif($list->sppls_status==3 && $_SESSION['internal']['role']==6 || $list->sppls_status==3 && $_SESSION['internal']['role']==0) $sppls = '<a href="'.site_url('subsidi/sppls/'.$list->surat_id).'">PROSES</a>';
                        elseif($list->sppls_status==4) $sppls = '<a href="'.base_url('process/pdf/export/'.$this->ifunction->indonesian_date(date('d M Y')).' - SPP-LS/3/'.$list->surat_id).'">CETAK</a>';
                        elseif($list->sppls_status==NULL) $sppls = 'PROSES'; 
                        else $sppls = 'SELESAI';                       

                        echo '<tr>
                                <td>'.$i.'</td>
                                <td>'.$list->uraian.'</td>
                                <td style="text-align:center">'.$sppls.'</td>
                                <td style="text-align:center">PROSES</td>
                            </tr>';
                        $i++;
                    }
                }
                ?>
            </tbody>
        </table>   

        <?php
        $Qpaging = $this->db->query("SELECT uraian FROM surat_masuk WHERE jenis_bantuan='3'");

        $num_page = ceil($Qpaging->num_rows / $limit);
        if($Qpaging->num_rows > $limit){
            $this->ifunction->paging($p, site_url('subsidi').'/'.$tp.'/', $num_page, $Qpaging->num_rows, 'href', false);
        }
        ?>             
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
                <a href="<?php echo site_url('subsidi') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
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