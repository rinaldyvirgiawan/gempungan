<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<div role="main" class="content-main" style="margin:120px 0 50px">
    <div class="wrapper clearfix">        
        <div class="primary">    
        <h1 class="page-title page-title-border">Laporan</h1>        
            <ul class="project-list-wrapper list-nostyle clearfix" style="width: 1024px;">
                <?php
                $limit = 30;
                $p = $p ? $p : 1;
                $position = ($p -1) * $limit;
                $this->db->_protect_identifiers=false;

                $Qlist = $this->db->query("SELECT * FROM laporan ORDER BY tahun DESC LIMIT $position,$limit");

                if($Qlist->num_rows){
                    $i = 0;
                    foreach($Qlist->result_object() as $list){
                        echo '<li class="clearfix" id="'.$i.'" style="width:320px;min-height:170px">
                                <a href="'.base_url('media/laporan/'.$list->file).'">
                                <div class="project-list-text" style="width:300px;color:#000">
                                    <h3 style="color:#0093bb">Tahun '.$list->tahun.'</h3>
                                    <p class="author"><span class="label">Anggaran:</span> Rp. '.number_format($list->anggaran,0,",",".").',-</p>
                                    <p class="author"><span class="label">Realisasi (Rp):</span> Rp. '.number_format($list->realisasi_rp,0,",",".").',-</p>
                                    <p class="author"><span class="label">Realisasi (%):</span> Rp. '.number_format($list->realisasi_persen,0,",",".").',-</p>
                                    <p class="author"><span class="label">Jumlah Penerima yang Mencairkan:</span> '.$list->penerima_cair.'</p>
                                    <p class="author"><span class="label">Jumlah Penerima yang Menyampaikan Laporan:</span> '.$list->penerima_lapor.'</p>
                                    <p class="author"><span class="label">Nilai yang Dilaporkan:</span> Rp. '.number_format($list->nilai_lapor,0,",",".").',-</p>
                                </div>
                                </a>
                            </li>'; 

                        $i++;

                        if($i==3){
                            echo '</ul><ul class="project-list-wrapper list-nostyle clearfix" style="width: 1024px;">';
                            $i = 0;
                        }
                    }                    
                }
                ?>
            </ul>
            <!-- project-list-wrapper -->
            <?php
            $Qpaging = $this->db->query("SELECT laporan_id FROM laporan");

            $num_page = ceil($Qpaging->num_rows / $limit);
            if($Qpaging->num_rows > $limit){
                $this->ifunction->paging($p, site_url('listlaporan').'/', $num_page, $Qpaging->num_rows, 'href', false);
            }
            ?>
        </div>
        <!-- primary -->
    </div>
    <!-- wrapper -->
</div>
<!-- content-main -->