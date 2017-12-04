<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<?php
switch($tp){

case 'index':
?>

<div class="primary">            
    <ul class="nav-project list-nostyle clearfix">
        <li class="active">
            <a class="btn" href="<?php echo site_url('surat/add'); ?>">+ Tambah</a>
        </li>
        <li>
            <a class="btn-orange btn-plain btn" href="<?php echo base_url('process/pdf/export/'.$this->ifunction->indonesian_date(date('d M Y')).' - Surat Masuk/4/'.$dx.'/landscape'); ?>">Cetak</a>
        </li>
    </ul>

    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <h1 class="page-title">Surat Masuk</h1>

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
                    <th>Tanggal Masuk Berkas/Disposisi</th>
                    <th>No Kartu Kendali</th>
                    <th>Uraian</th>
                    <th>Alamat</th>
                    <th>Jenis Bantuan</th>
                    <th>No Register NPHD</th>
                    <th>Tanggal  Register NPHD</th>
                    <th>Nilai Pengajuan</th>
                    <th>Tanggal dan No SPP</th>
                    <th>No Box</th>
                    <th>Tanggal dan No SP2D</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $Qlist = $this->db->query("SELECT * FROM surat_masuk ORDER BY surat_id ASC LIMIT $position,$limit");

                if($Qlist->num_rows){
                    $i = ($p*15)-14;

                    foreach($Qlist->result_object() as $list){
                        echo '<tr>
                                <td style="text-align: center;">'.$i.'</td>
                                <td style="text-align: center;">'.$this->ifunction->indonesian_date($list->tanggal_masuk).'</td>
                                <td>'.$list->no_kartu.'</td>
                                <td>'.$list->uraian.'</td>
                                <td>'.$list->alamat.'</td>
                                <td style="text-align: center;">'; if($list->jenis_bantuan==1) echo 'Hibah'; elseif($list->jenis_bantuan==2) echo 'Bansos'; elseif($list->jenis_bantuan==3) echo 'Subsidi'; elseif($list->jenis_bantuan==4) echo 'BPJS PNS'; elseif($list->jenis_bantuan==5) echo 'Bantuan Keuangan'; echo '</td>
                                <td>'.$list->no_nphd.'</td>
                                <td>'.$this->ifunction->indonesian_date($list->tanggal_nphd).'</td>
                                <td>'.number_format($list->nilai_pengajuan,0,",",".").'</td>
                                <td>'.$this->ifunction->indonesian_date($list->tanggal_spp).' & 991/'.$list->no_spp.'</td>
                                <td>'.$list->no_box.'</td>
                                <td>'.$this->ifunction->indonesian_date($list->tanggal_sp2d).' & '.$list->no_sp2d.'</td>
                                <td style="text-align: center;"><a href="'.site_url('surat/edit/'.$list->surat_id).'">Edit</a> | <a href="'.base_url('process/surat/delete/'.$list->surat_id).'" onclick="return confirm(\'Apakah Anda yakin akan menghapus Surat Masuk ini ?\');">Hapus</a></td>
                            </tr>';
                        $i++;
                    }
                }
                ?>
            </tbody>
        </table>   

        <?php
        $Qpaging = $this->db->query("SELECT surat_id FROM surat_masuk");

        $num_page = ceil($Qpaging->num_rows / $limit);
        if($Qpaging->num_rows > $limit){
            $this->ifunction->paging($p, site_url('surat').'/'.$tp.'/', $num_page, $Qpaging->num_rows, 'href', false);
        }
        ?>             
    </div>
    <!-- project-detail-wrapper -->
</div>
<!-- primary -->

<?php
break;

case 'add':
?>

<div class="primary">
    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <form action="<?php echo site_url('process/surat/add') ?>" method="post" class="form-check form-global">
            <h1 class="page-title">Tambah Surat Masuk</h1>

            <div class="col-wrapper clearfix">
                <div class="control-group">
                    <label class="control-label" for="">Tanggal Masuk Berkas/Disposisi</label>
                    <div class="controls">
                        <input id="datepicker-tgl" type="text" name="tgl_masuk" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">No Kartu Kendali</label>
                    <div class="controls">
                        <input type="text" name="no_kartu" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Uraian</label>
                    <div class="controls">
                        <textarea name="uraian" required></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Alamat</label>
                    <div class="controls">
                        <textarea name="alamat" required></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Jenis Bantuan</label>
                    <div class="controls">
                        <select name="jenis">
                            <option value="1">Hibah</option>
                            <option value="2">Bansos</option>
                            <option value="3">Subsidi</option>
                            <option value="4">BPJS PNS</option>
                            <option value="5">Bantuan Keuangan</option>
                        </select>
                    </div>
                </div>

                <!-- <div class="control-group">
                    <label class="control-label" for="">No Register NPHD</label>
                    <div class="controls">
                        <input type="text" name="no_nphd" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Tanggal Register NPHD</label>
                    <div class="controls">
                        <input id="datepicker-tgl1" type="text" name="tgl_nphd" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Nilai Pengajuan</label>
                    <div class="controls">
                        <input type="number" name="nilai_pengajuan" required>
                    </div>
                </div> -->
            </div>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Tambah" />
                <a href="<?php echo site_url('surat') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
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