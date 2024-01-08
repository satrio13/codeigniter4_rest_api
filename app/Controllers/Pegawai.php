<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PegawaiModel;

class Pegawai extends ResourceController
{
    function __construct()
    {
        $this->model = new PegawaiModel();
    }

    public function index()
    {
        $data = [
            'status' => 'success',
            'data' => $this->model->findAll()
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

        $this->model->insert([
            'nama' => esc($this->request->getVar('nama')),
            'jabatan' => esc($this->request->getVar('jabatan')),
            'bidang' => esc($this->request->getVar('bidang')),
            'alamat' => esc($this->request->getVar('alamat')),
            'email' => esc($this->request->getVar('email')),
            'gambar' => $nama_gambar
        ]);

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
        if(!$this->model->find($id))
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
            $gambar_db = $this->model->find($id);   
            if($gambar)
            {   
                if($gambar_db['gambar'] != '' AND $gambar_db['gambar'] != null)
                {
                    $nama_gambar = $gambar->getRandomName();
                    $gambar->move('gambar', $nama_gambar);
                    unlink('gambar/'.$gambar_db['gambar']);
                }     
            }else
            {
                $nama_gambar = $gambar_db['gambar'];
            }

            $this->model->update($id, [
                'nama' => esc($this->request->getVar('nama')),
                'jabatan' => esc($this->request->getVar('jabatan')),
                'bidang' => esc($this->request->getVar('bidang')),
                'alamat' => esc($this->request->getVar('alamat')),
                'email' => esc($this->request->getVar('email')),
                'gambar' => $nama_gambar
            ]);

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
        if(!$this->model->find($id))
        {
            return $this->failNotFound('Data pegawai tidak ditemukan');
        }else
        {
            $gambar_db = $this->model->find($id);   
            if($gambar_db['gambar'] != '' AND $gambar_db['gambar'] != null)
            {
                unlink('gambar/'.$gambar_db['gambar']);
            }

            $this->model->delete($id);
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
        if(!$this->model->find($id))
        {
            return $this->failNotFound('Data pegawai tidak ditemukan');
        }else
        {
            $data = [
                'status' => 'success',
                'pegawai_by_id' => $this->model->find($id)
            ];

            return $this->respond($data, 200);
        }
    }
    
}