<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<div role="main" class="content-main" style="margin:120px 0 50px">
    <div class="wrapper">
        <form action="<?php echo site_url('statistik') ?>" method="post" class="form-check form-global">
        <h1 class="page-title page-title-border">Statistik</h1>
        <div class="form-global">
            <div class="date-search clearfix">
                <div class="control-group">
                    <label class="control-label control-label-inline" for="">Tahun: </label>
                    <input id="datepicker-from" type="text" name="tahun" value="<?php if(isset($_POST['tahun'])) echo $_POST['tahun']; ?>">
                </div>
                <div class="control-actions">
                    <input name="rekap" class="btn-red btn-plain btn" type="submit" value="Lihat">
                </div>
            </div>
        </form>

        <?php   
        $limit = 30;
        $p = $p ? $p : 1;
        $position = ($p -1) * $limit;
        $this->db->_protect_identifiers=false;
        ?>

        <table class="table-global">
            <thead>
                <tr>
                    <th rowspan="2" width="50">No.</th>
                    <th rowspan="2">SKPD</th>
                    <th rowspan="2">Nilai Proposal</th>
                    <th rowspan="2">Nilai Penyetujuan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $Qlist = $this->db->select("id, name")->from('skpd')->order_by('id', 'ASC')->limit($limit, $position)->get();

                if($Qlist->num_rows){
                    $i = 1;
                    foreach($Qlist->result_object() as $list){
                        if(isset($_POST['rekap'])){
                            $tahun = $_POST['tahun'];
                            $Qmohon = $this->db->query("SELECT a.skpd_id, SUM(b.amount) AS mohon, SUM(b.correction) AS setuju
                                                FROM proposal a
                                                JOIN proposal_dana b ON b.proposal_id=a.id
                                                WHERE a.skpd_id='$list->id' AND YEAR(a.time_entry)='$tahun'");                            
                        }else $Qmohon = $this->db->query("SELECT a.skpd_id, SUM(b.amount) AS mohon, SUM(b.correction) AS setuju
                                                FROM proposal a
                                                JOIN proposal_dana b ON b.proposal_id=a.id
                                                WHERE a.skpd_id='$list->id'");

                        $mohon = $Qmohon->result_object();

                        echo '<tr>
                                <td style="text-align:center">'.$i.'</td>
                                <td>'.$list->name.'</td>
                                <td>'; if(isset($mohon[0]->mohon)) echo 'Rp. '.number_format($mohon[0]->mohon,0,",",".").',-'; else echo '-'; echo '</td>
                                <td>'; if(isset($mohon[0]->setuju)) echo 'Rp. '.number_format($mohon[0]->setuju,0,",",".").',-'; else echo '-'; echo '</td>
                            </tr>';
                        $i++;
                    }
                }else echo '<tr><td colspan="3">No data.</td></tr>';
                ?>
            </tbody>
        </table>

        <?php
        $Qpaging = $this->db->select("id")->from('skpd')->order_by('id', 'ASC')->get();

        $num_page = ceil($Qpaging->num_rows / $limit);
        if($Qpaging->num_rows > $limit){
            $this->ifunction->paging($p, site_url('statistik').'/', $num_page, $Qpaging->num_rows, 'href', false);
        }
        ?>
        </div>
    </div>
    <!-- wrapper -->
</div>
<!-- content-main -->