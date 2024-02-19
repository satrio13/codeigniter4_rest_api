<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'tb_karyawan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false;

    function list_karyawan()
    {
        return $this->table('tb_karyawan')->select('*')->orderBy('nama','asc')->get()->getResult();
    }

    function get_karyawan($id)
    {
        return $this->db->table('tb_karyawan')->getWhere(['id' => $id])->getRowArray();
    }

    function tambah_karyawan($data)
    {
        $this->db->table('tb_karyawan')->insert($data);
    }

    function edit_karyawan($data, $id)
    {
        $this->db->table('tb_karyawan')->where(['id' => $id])->update($data);
    }
    
    function hapus_karyawan($id)
    {
        $this->db->table('tb_karyawan')->where(['id' => $id])->delete();
    }
    
}
