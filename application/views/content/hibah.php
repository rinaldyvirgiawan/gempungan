<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<?php
switch($tp){

case 'edit':

$Qedit = $this->db->query("SELECT time_entry, name, address, judul, latar_belakang, maksud_tujuan, file FROM proposal WHERE id='$dx'"); $edit = $Qedit->result_object();
?>

<div role="main" class="content-main" style="margin:120px 0 50px">
    <div class="register-page wrapper-half">
        <h1 class="page-title page-title-border">Koreksi Hibah Bansos</h1>
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?> 
        <form class="form-global" method="post" onsubmit="return validasi(this)" action="<?php echo site_url('process/hibah/edit/'.$dx) ?>" enctype="multipart/form-data">
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="">Tanggal Kegiatan</label>
                    <div class="controls">
                        <input id="datepicker-tgl" type="text" name="tanggal" value="<?php echo date('Y-m-d', strtotime($edit[0]->time_entry)) ?>" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Nama (individu atau organisasi)</label>
                    <div class="controls">
                        <input type="text" name="name" value="<?php echo $edit[0]->name ?>" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Alamat</label>
                    <div class="controls">
                        <textarea name="address" required><?php echo $edit[0]->address ?></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Judul Kegiatan</label>
                    <div class="controls">
                        <input type="text" name="judul" value="<?php echo $edit[0]->judul ?>" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Latar Belakang</label>
                    <div class="controls">
                        <textarea name="latar" required><?php echo $edit[0]->latar_belakang ?></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Maksud dan Tujuan</label>
                    <div class="controls">
                        <textarea name="maksud" required><?php echo $edit[0]->maksud_tujuan ?></textarea>
                    </div>
                </div>
                <!-- <div class="control-group">
                    <label class="control-label" for="">Deskripsi Kegiatan</label>
                    <div class="controls">
                        <textarea name="kegiatan" required></textarea>
                    </div>
                </div> -->
                <div class="control-group">
                    <label class="control-label" for="">Proposal</label>
                    <div class="controls file">
                        <input type="file" name="proposal" accept="application/pdf">
                        <a class="info" target="_blank" href="<?php echo base_url('media/proposal/'.$edit[0]->file) ?>">Lihat Proposal</a>
                        <input type="hidden" name="old_proposal" value="<?php echo $edit[0]->file ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Dana</label>
                    <?php                    
                    $Qdana = $this->db->query("SELECT sequence, description, amount FROM proposal_dana WHERE proposal_id='$dx' ORDER BY sequence ASC");

                    foreach($Qdana->result_object() as $dana){
                        echo '<div class="controls file">
                                <label class="control-label" style="font-weight:normal"><input type="checkbox" name="del_dana[]" value="'.$dana->sequence.'"> Hapus</label>
                                <input type="text" name="deskripsi[]" value="'.$dana->description.'" placeholder="Deskripsi">
                                <input type="number" name="jumlah[]" value="'.$dana->amount.'" placeholder="Jumlah">
                                <input type="hidden" name="dana[]" value="'.$dana->amount.'">
                            </div>';
                    }
                    ?>
                    <a class="dana" href="#">Tambah Dana</a>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Foto</label>
                    <?php                    
                    $Qfoto = $this->db->query("SELECT `path`, sequence FROM proposal_photo WHERE proposal_id='$dx' AND is_nphd='0' ORDER BY sequence ASC");

                    foreach($Qfoto->result_object() as $foto){
                        echo '<div class="controls file">
                                <label class="control-label" style="font-weight:normal"><input type="checkbox" name="del_foto[]" value="'.$foto->sequence.'"> Hapus</label>
                                <input type="file" id="foto" name="foto[]">
                                <a class="info" target="_blank" href="'.base_url('media/proposal_foto/'.$foto->path).'">Lihat Foto</a>
                                <input type="hidden" name="old_foto[]" value="'.$foto->path.'">
                            </div>';
                    }
                    ?>
                    <a class="link" href="#">Tambah Foto</a>
                </div>
                <div class="control-actions clearfix">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['sabilulungan']['uid']; ?>">
                    <input type="hidden" name="role_id" value="<?php echo $_SESSION['sabilulungan']['role']; ?>">
                    <button class="btn-red btn-plain btn" type="submit">Koreksi</button>
                </div>
            </fieldset>
        </form>
    </div>
    <!-- wrapper-half -->
</div>
<!-- content-main -->

<?php
break;

default:
?>

<div role="main" class="content-main" style="margin:120px 0 50px">
    <div class="register-page wrapper-half">
        <h1 class="page-title page-title-border">Mendaftar Hibah Bansos</h1>
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?> 
		
		<script>

		function validasi(){

		   var fup = document.getElementById("foto").value;
		   var ext = fup.substring(fup.lastIndexOf('.')+1);
		   
		   var fup1 = document.getElementById("proposal").value;
		   var ext1 = fup1.substring(fup1.lastIndexOf('.')+1);
		  
		 
		   if(ext != "jpg" && ext != "png"){
			  alert("Format Foto Harus .jpg atau .png");
			  return false;
		   }else if(ext1 != "pdf"){
			  alert("Format Dokumen Harus .pdf");
			  return false;
		   }
		  
		};
		</script>
		
        <form class="form-global" method="post" action="<?php echo site_url('process/hibah/daftar') ?>" enctype="multipart/form-data" onsubmit="return validasi(this)">
            <fieldset>
                <?php if($_SESSION['sabilulungan']['role']!=6){ ?>
                <div class="control-group">
                    <label class="control-label" for="">Tanggal Kegiatan</label>
                    <div class="controls">
                        <input id="datepicker-tgl" type="text" name="tanggal" required>
                    </div>
                </div>
                <?php } ?>
                <div class="control-group">
                    <label class="control-label" for="">Nama (individu atau organisasi)</label>
                    <div class="controls">
                        <input type="text" name="name" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Alamat</label>
                    <div class="controls">
                        <textarea name="address" required></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Judul Kegiatan</label>
                    <div class="controls">
                        <input type="text" name="judul" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Latar Belakang</label>
                    <div class="controls">
                        <textarea name="latar" required></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Maksud dan Tujuan</label>
                    <div class="controls">
                        <textarea name="maksud" required></textarea>
                    </div>
                </div>
                <!-- <div class="control-group">
                    <label class="control-label" for="">Deskripsi Kegiatan</label>
                    <div class="controls">
                        <textarea name="kegiatan" required></textarea>
                    </div>
                </div> -->
                <div class="control-group">
                    <label class="control-label" for="">Proposal</label>
                    <div class="controls file">
                        <input type="file" id="proposal" name="proposal" accept="application/pdf" required>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Dana</label>
                    <div class="controls file">
                        <input type="text" name="deskripsi[]" placeholder="Deskripsi">
                        <input type="number" name="jumlah[]" placeholder="Jumlah">
                    </div>
                    <!--<a class="dana" href="#">Tambah Dana</a>-->
                </div>
                <div class="control-group">
                    <label class="control-label" for="">Foto</label>
                    <div class="controls file">
                        <input type="file" id="foto" name="foto[]">
                    </div>
                    <a class="link" href="#">Tambah Foto</a>
                </div>
                <div class="control-actions clearfix">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['sabilulungan']['uid']; ?>">
                    <input type="hidden" name="role_id" value="<?php echo $_SESSION['sabilulungan']['role']; ?>">
                    <button class="btn-red btn-plain btn" type="submit">Daftar</button>
                </div>
            </fieldset>
        </form>
    </div>
    <!-- wrapper-half -->
</div>
<!-- content-main -->

<?php
break;

}