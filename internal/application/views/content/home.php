<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<?php
switch($tp){

case 'index':
?>

<div class="primary">            
    <ul class="nav-project list-nostyle clearfix">
        <li class="active">
            <a class="btn" href="<?php echo site_url('index/add'); ?>">+ Tambah</a>
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
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $Qlist = $this->db->query("SELECT kepwal_id, nomor, tanggal FROM kepwal_parent ORDER BY kepwal_id DESC LIMIT $position,$limit");

                if($Qlist->num_rows){
                    foreach($Qlist->result_object() as $list){
                        echo '<tr>
                                <td><a href="'.site_url('child/index/'.$list->kepwal_id).'">'.$list->nomor.'</a></td>
                                <td>'.$this->ifunction->indonesian_date($list->tanggal).'</td>
                                <td style="text-align: center;"><a href="'.site_url('index/edit/'.$list->kepwal_id).'">Edit</a> | <a href="'.base_url('process/index/delete_kepwal/'.$list->kepwal_id).'" onclick="return confirm(\'Apakah Anda yakin akan menghapus Kepwal ini ?\');">Hapus</a></td>
                            </tr>';
                    }
                }
                ?>
            </tbody>
        </table>   

        <?php
        $Qpaging = $this->db->query("SELECT kepwal_id, nomor, tanggal FROM kepwal_parent");

        $num_page = ceil($Qpaging->num_rows / $limit);
        if($Qpaging->num_rows > $limit){
            $this->ifunction->paging($p, site_url('index').'/'.$tp.'/', $num_page, $Qpaging->num_rows, 'href', false);
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

        <form action="<?php echo site_url('process/index/add_kepwal') ?>" method="post" class="form-check form-global">
            <h1 class="page-title">Tambah Kepwal</h1>

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
            </div>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Tambah" />
                <a href="<?php echo site_url() ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>             
    </div>
    <!-- project-detail-wrapper -->
</div>
<!-- primary -->

<?php
break;

case 'edit':

$Qedit = $this->db->query("SELECT nomor, tanggal FROM kepwal_parent WHERE `kepwal_id`='$p'"); $edit = $Qedit->result_object();
?>

<div class="primary">
    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <form action="<?php echo site_url('process/index/edit_kepwal/'.$p) ?>" method="post" class="form-check form-global">
            <h1 class="page-title">Edit Kepwal</h1>

            <div class="col-wrapper clearfix">              
                <div class="control-group">
                    <label class="control-label" for="">Nomor</label>
                    <div class="controls">
                        <input type="text" name="nomor" value="<?php echo $edit[0]->nomor ?>" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="">Tanggal</label>
                    <div class="controls">
                        <input id="datepicker-tgl" type="text" name="tanggal" value="<?php echo $edit[0]->tanggal ?>" required>
                    </div>
                </div>
            </div>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Edit" />
                <a href="<?php echo site_url() ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
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