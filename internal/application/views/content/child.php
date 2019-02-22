<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<?php
switch($tp){

case 'index':
?>

<div class="primary">            
    <ul class="nav-project list-nostyle clearfix">
        <li class="active">
            <a class="btn" href="<?php echo site_url('child/add/'.$dx); ?>">+ Tambah</a>
        </li>
        <li>
            <a class="btn-orange btn-plain btn" href="<?php echo base_url('process/pdf/export/'.$this->ifunction->indonesian_date(date('d M Y')).' - Kepwal/1/'.$dx); ?>">Cetak</a>
        </li>
        <li>
            <a class="btn-grey btn-plain btn" href="<?php echo site_url(); ?>">Kembali</a>
        </li>
    </ul>

    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <h1 class="page-title">Kepwal</h1>

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
                    <th>Nama/Alamat Penerima Hibah</th>
                    <th>Jumlah Besaran Dana Hibah</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $Qlist = $this->db->query("SELECT * FROM kepwal_child WHERE kepwal_id='$dx' ORDER BY child_id ASC LIMIT $position,$limit");

                if($Qlist->num_rows){
                    $i = ($p*15)-14; $bulan = array('Bulan', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

                    foreach($Qlist->result_object() as $list){
                        echo '<tr>
                                <td>'.$i.'</td>
                                <td>'.$list->nama.'; '.$list->alamat.'; Telp: '.$list->telepon.'; Ketua: '.$list->ketua.'</td>
                                <td>'.number_format($list->jumlah,0,",",".").',-</td>
                                <td style="text-align: center;"><a href="'.site_url('child/edit/'.$dx.'/'.$list->child_id).'">Edit</a> | <a href="'.base_url('process/index/delete_child/'.$dx.'/'.$list->child_id).'" onclick="return confirm(\'Apakah Anda yakin akan menghapus Kepwal ini ?\');">Hapus</a></td>
                            </tr>';
                        $i++;
                    }
                }
                ?>
            </tbody>
        </table>   

        <?php
        $Qpaging = $this->db->query("SELECT kepwal_id FROM kepwal_child WHERE kepwal_id='$dx'");

        $num_page = ceil($Qpaging->num_rows / $limit);
        if($Qpaging->num_rows > $limit){
            $this->ifunction->paging($p, site_url('child').'/'.$tp.'/', $num_page, $Qpaging->num_rows, 'href', false);
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

        <form action="<?php echo site_url('process/index/add_child/'.$dx) ?>" method="post" class="form-check form-global">
            <h1 class="page-title">Tambah Kepwal</h1>

            <div class="col-wrapper clearfix">
                <div class="control-group">
                    <label class="control-label" for="">Nama</label>
                    <div class="controls">
                        <input type="text" name="nama" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Alamat</label>
                    <div class="controls">
                        <textarea name="alamat" required></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Telepon</label>
                    <div class="controls">
                        <input type="text" name="telepon" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Ketua</label>
                    <div class="controls">
                        <input type="text" name="ketua" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Jumlah</label>
                    <div class="controls">
                        <input type="number" name="jumlah" required>
                    </div>
                </div>
            </div>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Tambah" />
                <a href="<?php echo site_url('child/index/'.$dx) ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>             
    </div>
    <!-- project-detail-wrapper -->
</div>
<!-- primary -->

<?php
break;

case 'edit':
$Qedit = $this->db->query("SELECT * FROM kepwal_child WHERE `child_id`='$p'"); $edit = $Qedit->result_object();
?>

<div class="primary">
    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <form action="<?php echo site_url('process/index/edit_child/'.$dx.'/'.$p) ?>" method="post" class="form-check form-global">
            <h1 class="page-title">Edit Kepwal</h1>

            <div class="col-wrapper clearfix">                             
                <div class="control-group">
                    <label class="control-label" for="">Nama</label>
                    <div class="controls">
                        <input type="text" name="nama" value="<?php echo $edit[0]->nama ?>" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Alamat</label>
                    <div class="controls">
                        <textarea name="alamat" required><?php echo $edit[0]->alamat ?></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Telepon</label>
                    <div class="controls">
                        <input type="text" name="telepon" value="<?php echo $edit[0]->telepon ?>" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Ketua</label>
                    <div class="controls">
                        <input type="text" name="ketua" value="<?php echo $edit[0]->ketua ?>" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Jumlah</label>
                    <div class="controls">
                        <input type="number" name="jumlah" value="<?php echo $edit[0]->jumlah ?>" required>
                    </div>
                </div>
            </div>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Edit" />
                <a href="<?php echo site_url('child/index/'.$dx) ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
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