<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<div role="main" class="content-main" style="margin:120px 0 50px">
    <div class="wrapper">
        <h1 class="page-title page-title-border">Pemeriksaan Proposal Hibah Bansos</h1>
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }            
        ?>
        <!-- Filter -->
        <form action="<?php echo site_url('report') ?>" method="post" class="form-check form-global">
        <div class="form-global">
            <div class="control-group">
                <label class="control-label control-label-inline" for="">Kategori: </label>
                <select name="kategori">
                <option value="all">Semua</option>
                <?php
                $Qkategori = $this->db->select("id, name")->from('proposal_type')->order_by('id', 'ASC')->get();

                foreach($Qkategori->result_object() as $kategori){
                    echo '<option value="'.$kategori->id.'">'.$kategori->name.'</option>';
                }
                ?>
                </select>
            </div>
            <div class="date-search clearfix">
                <p class="label">Periode Proposal</p>
                <div class="control-group">
                    <label class="control-label control-label-inline" for="">Dari: </label>
                    <input id="datepicker-from" type="text" name="dari" value="<?php echo date('Y'); ?>">
                </div>
                <div class="control-group">
                    <label class="control-label control-label-inline" for="">Sampai: </label>
                    <input id="datepicker-to" type="text" name="sampai" value="<?php echo date('Y'); ?>">
                </div>
                <div class="control-group">
                    <label class="control-label control-label-inline" for="">SKPD: </label>
                    <select name="skpd">
                        <option value="all">Semua SKPD</option>
                        <?php
                        $Qskpd = $this->db->query("SELECT * FROM skpd ORDER BY id ASC");

                        foreach($Qskpd->result_object() as $skpd){
                            echo '<option value="'.$skpd->id.'">'.$skpd->name.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="control-actions">
                    <input name="filter" class="btn-red btn-plain btn" type="submit" value="Filter">
                </div>
            </div>
        </div>
        </form>

        <!-- Search -->
        <div class="form-search-wrapper-alt">
            <form class="form-search form-search-large clearfix" action="<?php echo site_url('report') ?>" method="post">
                <input type="text" name="keyword" placeholder="Search Hibah Bansos">
                <button name="search" class="btn-ir" type="submit">Search</button>
            </form>
        </div>        

        <?php   
        $limit = 15;
        $p = $p ? $p : 1;
        $position = ($p -1) * $limit;
        $this->db->_protect_identifiers=false;
        ?>

        <table class="table-global">
        <?php
        if($_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9) echo '<thead>
            <tr>
                <th rowspan="2">Judul Kegiatan</th>
                <th colspan="3">Besaran Belanja Hibah (Rp)</th>
                <th colspan="9">Status</th>
                <th rowspan="2">Detail</th>
            </tr>
            <tr>
                <th>Permohonan</th>
                <th>Hasil Evaluasi</th>
                <th>Pertimbangan TAPD</th>
                <th>Pemeriksaan TU</th>
                <th>Pemeriksaan Bupati</th>
                <th>Pemeriksaan Tim Pertimbangan</th>
                <th>Pemeriksaan SKPD</th>
                <th>Verifikasi Tim Pertimbangan</th>
                <th>Verifikasi TAPD</th>
                <th>Penyetujuan Bupati</th>
                <th>NPHD</th>
                <th>LPJ</th>
            </tr>
        </thead>';
        else echo '<thead>
            <tr>
                <th rowspan="2">Judul Kegiatan</th>
                <th colspan="3">Besaran Belanja Hibah (Rp)</th>
                <th colspan="7">Status</th>
                <th rowspan="2">Detail</th>
            </tr>
            <tr>
                <th>Permohonan</th>
                <th>Hasil Evaluasi</th>
                <th>Pertimbangan TAPD</th>
                <th>Pemeriksaan TU</th>
                <th>Pemeriksaan Bupati</th>
                <th>Pemeriksaan Tim Pertimbangan</th>
                <th>Pemeriksaan SKPD</th>
                <th>Verifikasi Tim Pertimbangan</th>
                <th>Verifikasi TAPD</th>
                <th>Penyetujuan Bupati</th>
            </tr>
        </thead>';
        ?>        
        <tbody>
        <?php
        //Otomatis Approve Walikota
        $Qcheck = $this->db->query("SELECT a.id, b.time_entry FROM proposal a JOIN proposal_approval b ON b.proposal_id=a.id WHERE a.current_stat=1"); 

        if($Qcheck->num_rows){
            foreach($Qcheck->result_object() as $check){
                $date = date('Y-m-d', strtotime($check->time_entry));
                $new = strtotime('+2 day', strtotime($date)); $newdate = date('Y-m-d', $new);
                $today = date('Y-m-d');

                if($newdate < $today){
                    $this->db->update("proposal", array('current_stat' => 2), array('id' => $check->id));

                    $Qchecks = $this->db->select("user_id")->from('proposal_approval')->where('proposal_id', $check->id)->where('flow_id', 2)->get();
                    if($Qchecks->num_rows) $this->db->update("proposal_approval", array('user_id' => $_SESSION['sabilulungan']['uid'], 'action' => 1), array('proposal_id' => $check->id, 'flow_id' => 2));
                    else $this->db->insert("proposal_approval", array('proposal_id' => $check->id, 'user_id' => $_SESSION['sabilulungan']['uid'], 'flow_id' => 2, 'action' => 1));

                    $this->db->insert("proposal_approval_history", array('proposal_id' => $check->id, 'user_id' => $_SESSION['sabilulungan']['uid'], 'flow_id' => 2, 'role_id' => $_SESSION['sabilulungan']['role'], 'action' => 1)); 
                }
            }
        }

        if(isset($_POST['search'])){
            //Query Search
            $walikota = array(1,6); $pertimbangan = array(2,4);
            if($_SESSION['sabilulungan']['role']==5) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->like('judul', $_POST['keyword'])->where('current_stat', NULL)->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==1) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->like('judul', $_POST['keyword'])->where_in('current_stat', $walikota)->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==4) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->like('judul', $_POST['keyword'])->where_in('current_stat', $pertimbangan)->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==3) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->like('judul', $_POST['keyword'])->where('current_stat', 3)->where('skpd_id', $_SESSION['sabilulungan']['skpd'])->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==2) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->like('judul', $_POST['keyword'])->where('current_stat', 5)->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==7) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->like('judul', $_POST['keyword'])->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==9) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->like('judul', $_POST['keyword'])->order_by('id', 'DESC')->limit($limit, $position)->get();
        }elseif(isset($_POST['filter'])){
            $kategori = $_POST['kategori'];
            $dari = $_POST['dari'];
            $sampai = $_POST['sampai'];
            $skpd = $_POST['skpd'];            

            $where = ''; $stat = 'current_stat!=8';
			$oppo = array(1,6);
			$sony = array(2,4);
            $walikota = implode(',',$oppo); $pertimbangan = implode(',',$sony); $sskpd = $_SESSION['sabilulungan']['skpd'];
            if($_SESSION['sabilulungan']['role']==5) $stat .= " AND current_stat=NULL";
            elseif($_SESSION['sabilulungan']['role']==1) $stat .= " AND current_stat IN ($walikota)";
            elseif($_SESSION['sabilulungan']['role']==4) $stat .= " AND current_stat IN ($pertimbangan)";
            elseif($_SESSION['sabilulungan']['role']==3) $stat .= " AND current_stat=3 AND skpd_id=$sskpd";
            elseif($_SESSION['sabilulungan']['role']==2) $stat .= " AND current_stat=5";

            //kategori
            if($kategori && !$dari && !$sampai && !$skpd){
                if($kategori=='all') $where .= "$stat";
                else $where .= "WHERE type_id = $kategori AND $stat";
            }elseif($kategori && $dari && !$sampai && !$skpd){
                if($kategori=='all') $where .= "WHERE YEAR(time_entry) >= '$dari' AND $stat";
                else $where .= "WHERE type_id = $kategori AND YEAR(time_entry) >= '$dari' AND $stat";
            }elseif($kategori && !$dari && $sampai && !$skpd){
                if($kategori=='all') $where .= "WHERE YEAR(time_entry) <= '$sampai' AND $stat";
                else $where .= "WHERE type_id = $kategori AND YEAR(time_entry) <= '$sampai' AND $stat";
            }elseif($kategori && !$dari && !$sampai && $skpd){
                if($kategori=='all' AND $skpd=='all') $where .= "$stat";
                elseif($kategori!='all' AND $skpd=='all') $where .= "WHERE type_id = $kategori AND $stat";
                elseif($kategori=='all' AND $skpd!='all') $where .= "WHERE skpd_id = $skpd AND $stat";
                else $where .= "WHERE type_id = $kategori AND skpd_id = $skpd AND $stat";
            }                        

            //dari
            elseif(!$kategori && $dari && !$sampai && !$skpd) $where .= "WHERE YEAR(time_entry) >= '$dari' AND $stat";
            elseif(!$kategori && $dari && $sampai && !$skpd) $where .= "WHERE YEAR(time_entry) >= '$dari' AND YEAR(time_entry) <= '$sampai' AND $stat";
            elseif(!$kategori && $dari && !$sampai && $skpd){
                if($skpd=='all') $where .= "WHERE YEAR(time_entry) >= '$dari' AND $stat";
                else $where .= "WHERE YEAR(time_entry) >= '$dari' AND skpd_id = $skpd AND $stat";
            }

            //sampai
            elseif(!$kategori && !$dari && $sampai && !$skpd) $where .= "WHERE YEAR(time_entry) <= '$sampai' AND $stat";
            elseif(!$kategori && !$dari && $sampai && $skpd){
                if($skpd=='all') $where .= "WHERE YEAR(time_entry) <= '$sampai' AND $stat";
                else $where .= "WHERE YEAR(time_entry) <= '$sampai' AND skpd_id = $skpd AND $stat";
            }

            //skpd
            elseif(!$kategori && !$dari && !$sampai && $skpd){
                if($skpd=='all') $where .= "$stat";
                else $where .= "WHERE skpd_id = $skpd AND $stat";
            }

            //mixed
            elseif($kategori && $dari && $sampai && !$skpd){
                if($kategori=='all') $where .= "WHERE YEAR(time_entry) >= '$dari' AND YEAR(time_entry) <= '$sampai' AND $stat";
                else $where .= "WHERE type_id = $kategori AND YEAR(time_entry) >= '$dari' AND YEAR(time_entry) <= '$sampai' AND $stat";
            }elseif(!$kategori && $dari && $sampai && $skpd){
                if($skpd=='all') $where .= "WHERE YEAR(time_entry) >= '$dari' AND YEAR(time_entry) <= '$sampai' AND $stat";
                else $where .= "WHERE skpd_id = $skpd AND YEAR(time_entry) >= '$dari' AND YEAR(time_entry) <= '$sampai' AND $stat";
            }elseif($kategori && $dari && !$sampai && $skpd){
                if($kategori=='all') $where .= "WHERE YEAR(time_entry) >= '$dari' AND skpd_id = $skpd AND $stat";
                else $where .= "WHERE type_id = $kategori AND YEAR(time_entry) >= '$dari' AND skpd_id = $skpd AND $stat";
            }elseif($kategori && !$dari && $sampai && $skpd){
                if($kategori=='all') $where .= "WHERE YEAR(time_entry) <= '$sampai' AND skpd_id = $skpd AND $stat";
                else $where .= "WHERE type_id = $kategori AND YEAR(time_entry) <= '$sampai' AND skpd_id = $skpd AND $stat";
            }elseif($kategori && $dari && $sampai && $skpd){
                if($kategori=='all' && $skpd=='all') $where .= "WHERE YEAR(time_entry) >= '$dari' AND YEAR(time_entry) <= '$sampai' AND $stat";
                elseif($kategori!='all' && $skpd=='all') $where .= "WHERE type_id = $kategori AND YEAR(time_entry) >= '$dari' AND YEAR(time_entry) <= '$sampai' AND $stat";
                elseif($kategori=='all' && $skpd!='all') $where .= "WHERE YEAR(time_entry) >= '$dari' AND YEAR(time_entry) <= '$sampai' AND skpd_id = $skpd AND $stat";
                else $where .= "WHERE type_id = $kategori AND YEAR(time_entry) >= '$dari' AND YEAR(time_entry) <= '$sampai' AND skpd_id = $skpd AND $stat";
            }

            $Qlist = $this->db->query("SELECT id, name, address, judul, current_stat, nphd, tanggal_lpj FROM proposal $where ORDER BY id DESC LIMIT $position,$limit");           
        }else{
            //Query List
            $walikota = array(1,6); $pertimbangan = array(2,4);
            if($_SESSION['sabilulungan']['role']==5) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->where('current_stat', NULL)->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==1) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->where_in('current_stat', $walikota)->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==4) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->where_in('current_stat', $pertimbangan)->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==3) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->where('current_stat', 3)->where('skpd_id', $_SESSION['sabilulungan']['skpd'])->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==2) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->where('current_stat', 5)->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==7) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->order_by('id', 'DESC')->limit($limit, $position)->get();
            elseif($_SESSION['sabilulungan']['role']==9) $Qlist = $this->db->select("id, name, address, judul, current_stat, nphd, tanggal_lpj")->from('proposal')->order_by('id', 'DESC')->limit($limit, $position)->get();
        }

        if($Qlist->num_rows){
            foreach($Qlist->result_object() as $list){
                $Qmohon = $this->db->query("SELECT SUM(amount) AS mohon FROM proposal_dana WHERE `proposal_id`='$list->id'"); 
                $mohon = $Qmohon->result_object();  

                $Qbesar = $this->db->query("SELECT value FROM proposal_checklist WHERE `proposal_id`='$list->id' AND checklist_id IN (26,28)"); $besar = $Qbesar->result_object();

                echo '<tr>
                        <td>'; if($_SESSION['sabilulungan']['role']==9) echo '<a href="'.site_url('hibah/edit/'.$list->id).'">'.$list->judul.'</a>'; else echo '<a href="'.site_url('detail/'.$list->id).'">'.$list->judul.'</a>'; echo '</td>
                        <td>Rp. '.number_format($mohon[0]->mohon,0,",",".").',-</td>
                        <td>'; if(isset($besar[0]->value)) echo 'Rp. '.number_format($besar[0]->value,0,",",".").',-'; else echo '-'; echo '</td>
                        <td>'; if(isset($besar[1]->value)) echo 'Rp. '.number_format($besar[1]->value,0,",",".").',-'; else echo '-'; echo '</td>';

                        if($list->current_stat==0 && $_SESSION['sabilulungan']['role']==5 || $list->current_stat==0 && $_SESSION['sabilulungan']['role']==7 || $list->current_stat==0 && $_SESSION['sabilulungan']['role']==9){
                            echo '<td style="text-align:center"><a href="'.site_url('tatausaha/periksa/'.$list->id).'">PROSES</a></td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            if($_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9){
                                if($_SESSION['sabilulungan']['role']==9){
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a href="'.site_url('admin/edit/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a href="'.site_url('admin/view/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }else{
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }
                            }
                            if($_SESSION['sabilulungan']['role']==9) echo '<td style="text-align:center"><a href="'.site_url('detil/edit/'.$list->id).'">EDIT</a></td>';
                            else echo '<td style="text-align:center"><a href="'.site_url('detil/proposal/'.$list->id).'">LIHAT</a></td>';
                        }

                        elseif($list->current_stat==1 && $_SESSION['sabilulungan']['role']==1 || $list->current_stat==1 && $_SESSION['sabilulungan']['role']==7 || $list->current_stat==1 && $_SESSION['sabilulungan']['role']==9){
                            if($_SESSION['sabilulungan']['role']==9){
                                echo '<td style="text-align:center"><a href="'.site_url('tatausaha/edit/'.$list->id).'">EDIT</a></td>';
                            }else{
                                $Qstat = $this->db->query("SELECT action FROM proposal_approval WHERE `proposal_id`='$list->id'");

                                foreach($Qstat->result_object() as $stat){
                                    if($stat->action==1) $status = '<a style="color:#00923f;cursor:text">DISETUJUI</a>'; elseif($stat->action==2) $status = '<a style="color:#F00;cursor:text">DITOLAK</a>';

                                    echo '<td style="text-align:center">'.$status.'</td>';
                                }
                            }
                            echo '<td style="text-align:center"><a href="'.site_url('walikota/periksa/'.$list->id).'">PROSES</a></td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            if($_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9){
                                if($_SESSION['sabilulungan']['role']==9){
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a href="'.site_url('admin/edit/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a href="'.site_url('admin/view/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }else{
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }
                            }
                            if($_SESSION['sabilulungan']['role']==9) echo '<td style="text-align:center"><a href="'.site_url('detil/edit/'.$list->id).'">EDIT</a></td>';
                            else echo '<td style="text-align:center"><a href="'.site_url('detil/proposal/'.$list->id).'">LIHAT</a></td>';
                        }

                        elseif($list->current_stat==2 && $_SESSION['sabilulungan']['role']==4 || $list->current_stat==2 && $_SESSION['sabilulungan']['role']==7 || $list->current_stat==2 && $_SESSION['sabilulungan']['role']==9){
                            if($_SESSION['sabilulungan']['role']==9){
                                echo '<td style="text-align:center"><a href="'.site_url('tatausaha/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('walikota/edit/'.$list->id).'">EDIT</a></td>';
                            }else{
                                $Qstat = $this->db->query("SELECT action FROM proposal_approval WHERE `proposal_id`='$list->id'");

                                foreach($Qstat->result_object() as $stat){
                                    if($stat->action==1) $status = '<a style="color:#00923f;cursor:text">DISETUJUI</a>'; elseif($stat->action==2) $status = '<a style="color:#F00;cursor:text">DITOLAK</a>';

                                    echo '<td style="text-align:center">'.$status.'</td>';
                                }
                            }
                            echo '<td style="text-align:center"><a href="'.site_url('pertimbangan/periksa/'.$list->id).'">PROSES</a></td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            if($_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9){
                                if($_SESSION['sabilulungan']['role']==9){
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a href="'.site_url('admin/edit/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a href="'.site_url('admin/view/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }else{
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }
                            }
                            if($_SESSION['sabilulungan']['role']==9) echo '<td style="text-align:center"><a href="'.site_url('detil/edit/'.$list->id).'">EDIT</a></td>';
                            else echo '<td style="text-align:center"><a href="'.site_url('detil/proposal/'.$list->id).'">LIHAT</a></td>';
                        }

                        elseif($list->current_stat==3 && $_SESSION['sabilulungan']['role']==3 || $list->current_stat==3 && $_SESSION['sabilulungan']['role']==7 || $list->current_stat==3 && $_SESSION['sabilulungan']['role']==9){
                            if($_SESSION['sabilulungan']['role']==9){
                                echo '<td style="text-align:center"><a href="'.site_url('tatausaha/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('walikota/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('pertimbangan/edit/'.$list->id).'">EDIT</a></td>';
                            }else{
                                $Qstat = $this->db->query("SELECT action FROM proposal_approval WHERE `proposal_id`='$list->id'");

                                foreach($Qstat->result_object() as $stat){
                                    if($stat->action==1) $status = '<a style="color:#00923f;cursor:text">DISETUJUI</a>'; elseif($stat->action==2) $status = '<a style="color:#F00;cursor:text">DITOLAK</a>';

                                    echo '<td style="text-align:center">'.$status.'</td>';
                                }
                            }
                            echo '<td style="text-align:center"><a href="'.site_url('skpd/periksa/'.$list->id).'">PROSES</a></td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            if($_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9){
                                if($_SESSION['sabilulungan']['role']==9){
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a href="'.site_url('admin/edit/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a href="'.site_url('admin/view/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }else{
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }
                            }
                            if($_SESSION['sabilulungan']['role']==9) echo '<td style="text-align:center"><a href="'.site_url('detil/edit/'.$list->id).'">EDIT</a></td>';
                            else echo '<td style="text-align:center"><a href="'.site_url('detil/proposal/'.$list->id).'">LIHAT</a></td>';
                        }

                        elseif($list->current_stat==4 && $_SESSION['sabilulungan']['role']==4 || $list->current_stat==4 && $_SESSION['sabilulungan']['role']==7 || $list->current_stat==4 && $_SESSION['sabilulungan']['role']==9){
                            if($_SESSION['sabilulungan']['role']==9){
                                echo '<td style="text-align:center"><a href="'.site_url('tatausaha/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('walikota/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('pertimbangan/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('skpd/edit/'.$list->id).'">EDIT</a></td>';
                            }else{
                                $Qstat = $this->db->query("SELECT action FROM proposal_approval WHERE `proposal_id`='$list->id'");

                                foreach($Qstat->result_object() as $stat){
                                    if($stat->action==1) $status = '<a style="color:#00923f;cursor:text">DISETUJUI</a>'; elseif($stat->action==2) $status = '<a style="color:#F00;cursor:text">DITOLAK</a>';

                                    echo '<td style="text-align:center">'.$status.'</td>';
                                }
                            }
                            echo '<td style="text-align:center"><a href="'.site_url('pertimbangan/verifikasi/'.$list->id).'">PROSES</a></td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            if($_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9){
                                if($_SESSION['sabilulungan']['role']==9){
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a href="'.site_url('admin/edit/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a href="'.site_url('admin/view/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }else{
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }
                            }
                            if($_SESSION['sabilulungan']['role']==9) echo '<td style="text-align:center"><a href="'.site_url('detil/edit/'.$list->id).'">EDIT</a></td>';
                            else echo '<td style="text-align:center"><a href="'.site_url('detil/proposal/'.$list->id).'">LIHAT</a></td>';
                        }

                        elseif($list->current_stat==5 && $_SESSION['sabilulungan']['role']==2 || $list->current_stat==5 && $_SESSION['sabilulungan']['role']==7 || $list->current_stat==5 && $_SESSION['sabilulungan']['role']==9){
                            if($_SESSION['sabilulungan']['role']==9){
                                echo '<td style="text-align:center"><a href="'.site_url('tatausaha/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('walikota/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('pertimbangan/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('skpd/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('pertimbangan/view/'.$list->id).'">EDIT</a></td>';
                            }else{
                                $Qstat = $this->db->query("SELECT action FROM proposal_approval WHERE `proposal_id`='$list->id'");

                                foreach($Qstat->result_object() as $stat){
                                    if($stat->action==1) $status = '<a style="color:#00923f;cursor:text">DISETUJUI</a>'; elseif($stat->action==2) $status = '<a style="color:#F00;cursor:text">DITOLAK</a>';

                                    echo '<td style="text-align:center">'.$status.'</td>';
                                }
                            }
                            echo '<td style="text-align:center"><a href="'.site_url('tapd/verifikasi/'.$list->id).'">PROSES</a></td>';
                            echo '<td style="text-align:center">PROSES</td>';
                            if($_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9){
                                if($_SESSION['sabilulungan']['role']==9){
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a href="'.site_url('admin/edit/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a href="'.site_url('admin/view/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }else{
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }
                            }
                            if($_SESSION['sabilulungan']['role']==9) echo '<td style="text-align:center"><a href="'.site_url('detil/edit/'.$list->id).'">EDIT</a></td>';
                            else echo '<td style="text-align:center"><a href="'.site_url('detil/proposal/'.$list->id).'">LIHAT</a></td>';
                        }

                        elseif($list->current_stat==6 && $_SESSION['sabilulungan']['role']==1 || $list->current_stat==6 && $_SESSION['sabilulungan']['role']==7 || $list->current_stat==6 && $_SESSION['sabilulungan']['role']==9){
                            if($_SESSION['sabilulungan']['role']==9){
                                echo '<td style="text-align:center"><a href="'.site_url('tatausaha/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('walikota/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('pertimbangan/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('skpd/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('pertimbangan/view/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('tapd/edit/'.$list->id).'">EDIT</a></td>';
                            }else{
                                $Qstat = $this->db->query("SELECT action FROM proposal_approval WHERE `proposal_id`='$list->id'");

                                foreach($Qstat->result_object() as $stat){
                                    if($stat->action==1) $status = '<a style="color:#00923f;cursor:text">DISETUJUI</a>'; elseif($stat->action==2) $status = '<a style="color:#F00;cursor:text">DITOLAK</a>';

                                    echo '<td style="text-align:center">'.$status.'</td>';
                                }
                            }
                            echo '<td style="text-align:center"><a href="'.site_url('walikota/setuju/'.$list->id).'">PROSES</a></td>';
                            if($_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9){
                                if($_SESSION['sabilulungan']['role']==9){
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a href="'.site_url('admin/edit/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a href="'.site_url('admin/view/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }else{
                                    echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                    echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                                }
                            }
                            if($_SESSION['sabilulungan']['role']==9) echo '<td style="text-align:center"><a href="'.site_url('detil/edit/'.$list->id).'">EDIT</a></td>';
                            else echo '<td style="text-align:center"><a href="'.site_url('detil/proposal/'.$list->id).'">LIHAT</a></td>';
                        }

                        elseif($list->current_stat==7 && $_SESSION['sabilulungan']['role']==7 || $list->current_stat==7 && $_SESSION['sabilulungan']['role']==9){
                            if($_SESSION['sabilulungan']['role']==9){
                                echo '<td style="text-align:center"><a href="'.site_url('tatausaha/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('walikota/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('pertimbangan/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('skpd/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('pertimbangan/view/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('tapd/edit/'.$list->id).'">EDIT</a></td>';
                                echo '<td style="text-align:center"><a href="'.site_url('walikota/view/'.$list->id).'">EDIT</a></td>';
                            }else{
                                $Qstat = $this->db->query("SELECT action FROM proposal_approval WHERE `proposal_id`='$list->id'");

                                foreach($Qstat->result_object() as $stat){
                                    if($stat->action==1) $status = '<a style="color:#00923f;cursor:text">DISETUJUI</a>'; elseif($stat->action==2) $status = '<a style="color:#F00;cursor:text">DITOLAK</a>';

                                    echo '<td style="text-align:center">'.$status.'</td>';
                                }
                            }
                            if($_SESSION['sabilulungan']['role']==9){
                                echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a href="'.site_url('admin/edit/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a href="'.site_url('admin/view/'.$list->id).'">EDIT</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                            }else{
                                echo '<td style="text-align:center">'; if(isset($list->nphd)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/nphd/'.$list->id).'">PROSES</a>'; echo '</td>';
                                echo '<td style="text-align:center">'; if(isset($list->tanggal_lpj)) echo '<a style="color:#00923f;cursor:text">SELESAI</a>'; else echo '<a href="'.site_url('admin/lpj/'.$list->id).'">PROSES</a>'; echo '</td>';
                            }
                            if($_SESSION['sabilulungan']['role']==9) echo '<td style="text-align:center"><a href="'.site_url('detil/edit/'.$list->id).'">EDIT</a></td>';
                            else echo '<td style="text-align:center"><a href="'.site_url('detil/proposal/'.$list->id).'">LIHAT</a></td>';
                        }

                echo '</tr>';
            }
        }else echo '<tr><td align="center" colspan="12">No data.</td></tr>';
        echo '</tbody></table>';

        if(isset($_POST['search'])){
            $walikota = array(1,6); $pertimbangan = array(2,4);
            if($_SESSION['sabilulungan']['role']==5) $Qpaging = $this->db->select("id")->from('proposal')->like('judul', $_POST['keyword'])->where('current_stat', NULL)->get();
            elseif($_SESSION['sabilulungan']['role']==1) $Qpaging = $this->db->select("id")->from('proposal')->like('judul', $_POST['keyword'])->where_in('current_stat', $walikota)->get();
            elseif($_SESSION['sabilulungan']['role']==4) $Qpaging = $this->db->select("id")->from('proposal')->like('judul', $_POST['keyword'])->where_in('current_stat', $pertimbangan)->get();
            elseif($_SESSION['sabilulungan']['role']==3) $Qpaging = $this->db->select("id")->from('proposal')->like('judul', $_POST['keyword'])->where('current_stat', 3)->get();
            elseif($_SESSION['sabilulungan']['role']==2) $Qpaging = $this->db->select("id")->from('proposal')->like('judul', $_POST['keyword'])->where('current_stat', 5)->get();
            elseif($_SESSION['sabilulungan']['role']==7) $Qpaging = $this->db->select("id")->from('proposal')->like('judul', $_POST['keyword'])->get();
            elseif($_SESSION['sabilulungan']['role']==9) $Qpaging = $this->db->select("id")->from('proposal')->like('judul', $_POST['keyword'])->get();
        }else{
            $walikota = array(1,6); $pertimbangan = array(2,4);
            if($_SESSION['sabilulungan']['role']==5) $Qpaging = $this->db->select("id")->from('proposal')->where('current_stat', NULL)->get();
            elseif($_SESSION['sabilulungan']['role']==1) $Qpaging = $this->db->select("id")->from('proposal')->where_in('current_stat', $walikota)->get();
            elseif($_SESSION['sabilulungan']['role']==4) $Qpaging = $this->db->select("id")->from('proposal')->where_in('current_stat', $pertimbangan)->get();
            elseif($_SESSION['sabilulungan']['role']==3) $Qpaging = $this->db->select("id")->from('proposal')->where('current_stat', 3)->get();
            elseif($_SESSION['sabilulungan']['role']==2) $Qpaging = $this->db->select("id")->from('proposal')->where('current_stat', 5)->get();
            elseif($_SESSION['sabilulungan']['role']==7) $Qpaging = $this->db->select("id")->from('proposal')->get();
            elseif($_SESSION['sabilulungan']['role']==9) $Qpaging = $this->db->select("id")->from('proposal')->get();
        } 

        $num_page = ceil($Qpaging->num_rows / $limit);
        if($Qpaging->num_rows > $limit){
            $this->ifunction->paging($p, site_url('report').'/', $num_page, $Qpaging->num_rows, 'href', false);
        }
        ?>
    </div>
    <!-- wrapper -->
</div>
<!-- content-main -->