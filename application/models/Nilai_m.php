<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Nilai_m extends CI_Model {

    /**
     * Fungsi model USER =======================
     * 
     * * */
    function inputNilai($nilai, $bagianpenilai, $bagiandinilai, $idkuisioner, $catatan) {
        $bulan = date('n') - 1;
        $tahun = date('Y');

        if ($bulan == 0) { //kondisi bila bulan jaunari menilai bulan dec tahun sebelumnya
            $bulan = 12;
            $tahun = date('Y') - 1;
            $this->db->query("INSERT INTO `nilai` (`idnilai`, `nilai`, `bagianpenilai`, `bagiandinilai`, `idkuisioner`, `bulan`, `tahun`, `catatan`, `saran`) 
            VALUES (NULL, '$nilai', '$bagianpenilai', '$bagiandinilai', '$idkuisioner', '$bulan', '$tahun', '$catatan', NULL);");
        } else {
            $this->db->query("INSERT INTO `nilai` (`idnilai`, `nilai`, `bagianpenilai`, `bagiandinilai`, `idkuisioner`, `bulan`, `tahun`, `catatan`, `saran`) 
            VALUES (NULL, '$nilai', '$bagianpenilai', '$bagiandinilai', '$idkuisioner', '$bulan', '$tahun', '$catatan', NULL);");
        }
    }

    function kritikSaran($bagianpenilai, $bagiandinilai, $kritiksaran) {
        $bulan = date('n') - 1;
        $tahun = date('Y');

        if ($bulan == 0) { //kondisi bila bulan jaunari menilai bulan dec tahun sebelumnya
            $bulan = 12;
            $tahun = date('Y') - 1;
            $this->db->query("INSERT INTO `kritiksaran` (`idkritik`, `bagianpenilai`, `bagiandinilai`, `bulan`, `tahun`, `kritiksaran`) 
                VALUES (NULL, '$bagianpenilai', '$bagiandinilai', '$bulan', '$tahun', '$kritiksaran');");
        } else {
            $this->db->query("INSERT INTO `kritiksaran` (`idkritik`, `bagianpenilai`, `bagiandinilai`, `bulan`, `tahun`, `kritiksaran`) 
                VALUES (NULL, '$bagianpenilai', '$bagiandinilai', '$bulan', '$tahun', '$kritiksaran');");
        }
    }

    function pengecekanInputNilai($bulan, $tahun, $bagiandinilai, $bagian) {
        $query = $this->db->query("SELECT * FROM nilai WHERE bagianpenilai='$bagian' AND bulan='$bulan' AND tahun='$tahun' AND bagiandinilai='$bagiandinilai'");
//        return $query->result_array();
        if ($query->result_array() != NULL) {
            return true;
        } else {
            return false;
        }
    }

    function selectNilai($bulan, $tahun, $bagiandinilai, $bagian) {
        $query = $this->db->query("SELECT * FROM nilai WHERE bagianpenilai='$bagian' AND bulan='$bulan' AND tahun='$tahun' AND bagiandinilai='$bagiandinilai'");
        return $query->result_array();
    }

    function selectKuisioner($bulan, $tahun, $bagiandinilai, $bagian) {
        $query = $this->db->query("SELECT pertanyaan FROM kuisioner a, nilai b WHERE b.idkuisioner = a.idkuisioner AND b.bagianpenilai='$bagian' AND b.bulan='$bulan' AND b.tahun='$tahun' AND b.bagiandinilai='$bagiandinilai'");
        return $query->result();
    }

    function selectKritikSaran($bulan, $tahun, $bagiandinilai, $bagian) {
        $query = $this->db->query("SELECT `kritiksaran` FROM `kritiksaran` 
                                        WHERE `bagianpenilai` = '$bagian'
                                        AND `bagiandinilai` = '$bagiandinilai'
                                        AND `bulan` = '$bulan'
                                        AND `tahun` = '$tahun'");
        return $query->row_array();
    }

    /**
     * 
     * Fungsi model TIMKPI =======================
     * 
     * * */
    function getKritikSaranBulanan($bulan, $tahun) {
        $query = $this->db->query("
                SELECT bagianpenilai, bagiandinilai, kritiksaran 
                FROM `kritiksaran`
                WHERE bulan='$bulan' AND tahun='$tahun'
            ");
        $data = $query->result();
        return $data;
    }

    function getCatatanBulanan($bulan, $tahun) {
        $query = $this->db->query("
                SELECT bagianpenilai, bagiandinilai, pertanyaan , catatan 
                FROM `nilai` , kuisioner
                WHERE nilai.idkuisioner = kuisioner.idkuisioner 
                AND bulan='$bulan' AND tahun='$tahun' AND catatan <> '' 
                ORDER BY `nilai`.`bagiandinilai` ASC
            ");
        $data = $query->result();
        return $data;
    }

    function getProgressPengumpulan($bulan, $tahun) {
        $query = $this->db->query("
                SELECT a.bagian ,  b.bagianpenilai

                FROM account a
                LEFT JOIN (SELECT bagianpenilai FROM `nilai` WHERE bulan = '$bulan' AND tahun = '$tahun' GROUP BY `bagianpenilai` ORDER BY `catatan`) b 

                ON a.bagian = b.bagianpenilai
                WHERE a.isadmin = '0'
            ");
        $data = $query->result();
        return $data;
    }

    function getDetilProgressPengumpulan($departemen, $bulan, $tahun) {
        $query = $this->db->query("SELECT a.bagiandinilai AS relasi, b.bagiandinilai AS status
            FROM relasipenilaian a LEFT JOIN (SELECT bagiandinilai FROM nilai WHERE bulan = '$bulan' AND tahun = '$tahun' AND bagianpenilai = '$departemen' GROUP BY bagiandinilai) b ON a.bagiandinilai = b.bagiandinilai 
            WHERE a.bagianpenilai = '$departemen'");
        $data = $query->result();
        return $data;
    }

    function getAvgNilaiKKPBulanan($bulan, $tahun) {
        $query = $this->db->query("
            SELECT bagiandinilai,AVG(nilai) AS avgnilai FROM `nilai` 
            WHERE bulan = '$bulan'
            AND tahun = '$tahun'
            GROUP BY bagiandinilai
            ");
        $data = $query->result();
        return $data;
    }

    function detilPenilai($noBulan, $tahun, $bagiandinilai) {
        $query = $this->db->query("
            SELECT bagianpenilai , AVG(nilai) as average FROM nilai
            WHERE bulan = '$noBulan' 
            AND tahun = '$tahun'
            AND bagiandinilai = '$bagiandinilai'
            GROUP BY bagianpenilai ;
            ");
        return $query->result();
    }

    function getDetilNilaiPerKuisioner($noBulan, $tahun, $bagiandinilai) {
        $query = $this->db->query("SELECT a.idkuisioner, b.pertanyaan, AVG(a.nilai) AS nilai
            FROM `nilai` a , kuisioner b
            WHERE a.idkuisioner = b.idkuisioner
            AND bagiandinilai = '$bagiandinilai' 
            AND bulan = '$noBulan' 
            AND tahun = '$tahun' 
            GROUP BY idkuisioner");
        return $query->result();
    }

    function getAvgDetilNilaiPerKuisioner($noBulan, $tahun, $bagiandinilai) {
        $data = $this->getDetilNilaiPerKuisioner($noBulan, $tahun, $bagiandinilai);
        
        $no = 0;
        $sumnilai = 0;
        
        foreach ($data as $key) {
            $no++;
            $sumnilai+= $key->nilai;
        }
        
        return $sumnilai/$no;
    }

    function avgNilaiBulanan($noBulan, $tahun, $bagiandinilai) {
        // fungsi untuk mendapatkan average nilai departemen dengan parameter bulan dan tahun

        $bagiandinilai = urldecode($bagiandinilai);
        $detilpenilai = $this->detilPenilai($noBulan, $tahun, $bagiandinilai);

        $counter = 0;
        $sumtotal = 0;
        foreach ($detilpenilai as $key) {
            $counter++;
            $sumtotal += round($key->average, 2);
        }
        $average = $sumtotal / $counter;
//        echo "$average";

        return $average;
    }

    function detilNilaiPenilai($noBulan, $tahun, $bagiandinilai) {
        $query = $this->db->query("
            SELECT a.bagianpenilai, b.pertanyaan , a.nilai, a.catatan FROM `nilai` a,`kuisioner` b 
            WHERE bagiandinilai = '$bagiandinilai'
            AND bulan = '$noBulan'
            AND tahun = '$tahun'
            AND a.idkuisioner = b.idkuisioner
            ");
        return $query->result();
    }

    function tahunlistdb() {
        $query = $this->db->query("SELECT tahun FROM `nilai` GROUP BY tahun ");
        return $query->result();
    }

    function bulankonversi($nobulan) {
        switch ($nobulan) {
            case 1:
                $hasil = 'January';
                return $hasil;
                break;
            case 2:
                $hasil = 'February';
                return $hasil;
                break;
            case 3:
                $hasil = 'March';
                return $hasil;
                break;
            case 4:
                $hasil = 'April';
                return $hasil;
                break;
            case 5:
                $hasil = 'May';
                return $hasil;
                break;
            case 6:
                $hasil = 'June';
                return $hasil;
                break;
            case 7:
                $hasil = 'July';
                return $hasil;
                break;
            case 8:
                $hasil = 'August';
                return $hasil;
                break;
            case 9:
                $hasil = 'September';
                return $hasil;
                break;
            case 10:
                $hasil = 'October';
                return $hasil;
                break;
            case 11:
                $hasil = 'November';
                return $hasil;
                break;
            case 12:
                $hasil = 'December';
                return $hasil;
                break;
            default:
                break;
        }
    }

}

?>