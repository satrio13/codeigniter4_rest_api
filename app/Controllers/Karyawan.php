<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\KaryawanModel;

class Karyawan extends ResourceController
{
    function __construct()
    {
        $this->model = new KaryawanModel();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        $data = [
            'status' => 'success',
            'data' => $this->model->list_karyawan()
        ]; 

        return $this->respond($data, 200);
    }

    public function create()
    {
        $rules = $this->validate([
            'nama' => 'required|max_length[100]',
            'jabatan' => 'required|max_length[50]',
            'bidang' => 'required|max_length[50]',
            'alamat' => 'required',
            'email' => 'required|max_length[100]|valid_email',
            'gambar' => 'uploaded[gambar]|max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,img/jpg,image/jpeg,image/png]'
        ]);

        if(!$rules)
        {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $gambar = $this->request->getFile('gambar');
        $nama_gambar = $gambar->getRandomName();
        $gambar->move('gambar', $nama_gambar);

        $data = [
            'nama' => esc($this->request->getVar('nama')),
            'jabatan' => esc($this->request->getVar('jabatan')),
            'bidang' => esc($this->request->getVar('bidang')),
            'alamat' => esc($this->request->getVar('alamat')),
            'email' => esc($this->request->getVar('email')),
            'gambar' => $nama_gambar,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->model->tambah_karyawan($data);
        if($this->model->affectedRows() > 0)
        {
            $response = [
                'status' => 'success',
                'message' => 'Data berhasil ditambahkan'
            ];

            return $this->respondCreated($response);
        }else
        {
            $response = [
                'status' => 'success',
                'message' => 'Data gagal ditambahkan'
            ];

            return $this->respond($response, 400);
        }
    }

    public function update($id = null)
    {
        $karyawan = $this->model->get_karyawan($id);
        if(!$karyawan)
        {
            return $this->failNotFound('Data pegawai tidak ditemukan');
        }else
        {
            $rules = $this->validate([
                'nama' => 'required|max_length[100]',
                'jabatan' => 'required|max_length[50]',
                'bidang' => 'required|max_length[50]',
                'alamat' => 'required',
                'email' => 'required|max_length[100]|valid_email',
                'gambar' => 'max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,img/jpg,image/jpeg,image/png]'
            ]);

            if(!$rules)
            {
                $response = [
                    'message' => $this->validator->getErrors()
                ];

                return $this->failValidationErrors($response);
            }

            $gambar = $this->request->getFile('gambar');
            if($gambar)
            {   
                if($karyawan['gambar'] != '' AND $karyawan['gambar'] != null)
                {
                    $nama_gambar = $gambar->getRandomName();
                    $gambar->move('gambar', $nama_gambar);
                    unlink('gambar/'.$karyawan['gambar']);
                }     
            }else
            {
                $nama_gambar = $karyawan['gambar'];
            }

            $data = [
                'nama' => esc($this->request->getVar('nama')),
                'jabatan' => esc($this->request->getVar('jabatan')),
                'bidang' => esc($this->request->getVar('bidang')),
                'alamat' => esc($this->request->getVar('alamat')),
                'email' => esc($this->request->getVar('email')),
                'gambar' => $nama_gambar,            
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->model->edit_karyawan($data, $id);
            if($this->model->affectedRows() > 0)
            {
                $response = [
                    'status' => 'success',
                    'message' => 'Data berhasil diupdate'
                ];

                return $this->respond($response, 200);
            }else
            {
                $response = [
                    'status' => 'success',
                    'message' => 'Data gagal diupdate'
                ];
    
                return $this->respond($response, 400);
            }
        }
    }
    
    public function delete($id = null)
    {
        $karyawan = $this->model->get_karyawan($id);
        if(!$karyawan)
        {
            return $this->failNotFound('Data pegawai tidak ditemukan');
        }else
        {
            if($karyawan['gambar'] != '' AND $karyawan['gambar'] != null)
            {
                unlink('gambar/'.$karyawan['gambar']);
            }

            $this->model->hapus_karyawan($id);
            if($this->model->affectedRows() > 0)
            {
                $response = [
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus'
                ];
        
                return $this->respondDeleted($response);
            }else
            {
                $response = [
                    'status' => 'success',
                    'message' => 'Data gagal dihapus'
                ];
    
                return $this->respond($response, 400);
            }
        }
    }

    public function show($id = null)
    {
        $karyawan = $this->model->get_karyawan($id);
        if(!$karyawan)
        {
            return $this->failNotFound('Data pegawai tidak ditemukan');
        }else
        {
            $data = [
                'status' => 'success',
                'pegawai_by_id' => $karyawan
            ];

            return $this->respond($data, 200);
        }
    }

}