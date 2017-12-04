<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<?php
switch($tp){

case 'index':
?>

<div class="primary">            
    <!-- <ul class="nav-project list-nostyle clearfix">
        <li class="active">
            <a class="btn" href="<?php echo site_url('pejabat/add/'); ?>">+ Tambah</a>
        </li>
    </ul> -->

    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <h1 class="page-title">Pejabat</h1>

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
                    <th>Jabatan</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $Qlist = $this->db->query("SELECT pejabat_id, nama, jabatan FROM pejabat WHERE tipe!='0' ORDER BY pejabat_id ASC LIMIT $position,$limit");

                if($Qlist->num_rows){
                    $i = ($p*15)-14;

                    foreach($Qlist->result_object() as $list){
                        echo '<tr>
                                <td style="text-align: center;">'.$i.'</td>
                                <td>'.$list->nama.'</td>
                                <td>'.$list->jabatan.'</td>
                                <td style="text-align: center;"><a href="'.site_url('pejabat/edit/'.$list->pejabat_id).'">Edit</a></td>
                            </tr>';
                        $i++;
                        // | <a href="'.base_url('process/pejabat/delete/'.$list->pejabat_id).'" onclick="return confirm(\'Apakah Anda yakin akan menghapus Pejabat ini ?\');">Hapus</a>
                    }
                }
                ?>
            </tbody>
        </table>   

        <?php
        $Qpaging = $this->db->query("SELECT nama FROM pejabat WHERE tipe!='0'");

        $num_page = ceil($Qpaging->num_rows / $limit);
        if($Qpaging->num_rows > $limit){
            $this->ifunction->paging($p, site_url('pejabat').'/'.$tp.'/', $num_page, $Qpaging->num_rows, 'href', false);
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

        <form action="<?php echo site_url('process/pejabat/add/') ?>" method="post" class="form-check form-global">
            <h1 class="page-title">Tambah Kepwal</h1>

            <div class="col-wrapper clearfix">
                <div class="control-group">
                    <label class="control-label" for="">Nama</label>
                    <div class="controls">
                        <input type="text" name="nama" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">NIP</label>
                    <div class="controls">
                        <input type="text" name="nip">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Pangkat</label>
                    <div class="controls">
                        <input type="text" name="pangkat">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Jabatan</label>
                    <div class="controls">
                        <input type="text" name="jabatan">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Unit Kerja</label>
                    <div class="controls">
                        <input type="text" name="unit">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Username</label>
                    <div class="controls">
                        <input type="text" name="uname">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Password</label>
                    <div class="controls">
                        <input type="password" name="pswd">
                    </div>
                </div>
            </div>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Tambah" />
                <a href="<?php echo site_url('pejabat') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>             
    </div>
    <!-- project-detail-wrapper -->
</div>
<!-- primary -->

<?php
break;

case 'edit':
$Qedit = $this->db->query("SELECT * FROM pejabat WHERE `pejabat_id`='$p'"); $edit = $Qedit->result_object();
?>

<div class="primary">
    <div class="project-detail-wrapper">
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>

        <form action="<?php echo site_url('process/pejabat/edit/'.$p) ?>" method="post" class="form-check form-global">
            <h1 class="page-title">Edit Pejabat</h1>

            <div class="col-wrapper clearfix">
                <div class="control-group">
                    <label class="control-label" for="">Nama</label>
                    <div class="controls">
                        <input type="text" name="nama" value="<?php echo $edit[0]->nama ?>" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">NIP</label>
                    <div class="controls">
                        <input type="text" name="nip" value="<?php echo $edit[0]->nip ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Pangkat</label>
                    <div class="controls">
                        <input type="text" name="pangkat" value="<?php echo $edit[0]->pangkat ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Jabatan</label>
                    <div class="controls">
                        <input type="text" name="jabatan" value="<?php echo $edit[0]->jabatan ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Unit Kerja</label>
                    <div class="controls">
                        <input type="text" name="unit" value="<?php echo $edit[0]->unit_kerja ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Username</label>
                    <div class="controls">
                        <input type="text" name="uname" value="<?php echo $edit[0]->username ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Password</label>
                    <div class="controls">
                        <input type="password" name="pswd">
                    </div>
                </div>
            </div>

            <div class="control-actions">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Edit" />
                <a href="<?php echo site_url('pejabat') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
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