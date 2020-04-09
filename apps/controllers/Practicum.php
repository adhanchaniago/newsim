<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Practicum extends CI_Controller
{

  var $data;

  public function __construct()
  {
    parent::__construct();
    if (userdata('login') != 'laboran' && userdata('login') != 'aslab') {
      redirect();
    }
    $id_laboran = $this->db->get_where('users', array('idUser' => userdata('id')))->row()->id_laboran;
    $this->data = array(
      'profil'              => $this->m->profilLaboran($id_laboran)->row(),
      'jumlah_komplain'     => $this->m->hitungKomplain()->row()->komplain,
      'jumlah_pinjam_lab'   => $this->m->hitungPeminjamanLab()->row()->pinjamlab,
      'jumlah_pinjam_alat'  => $this->m->hitungPeminjamanAlat()->row()->pinjamalat
    );
  }

  public function Courses()
  {
    $data           = $this->data;
    $data['title']  = 'Courses | SIM Laboratorium';
    $data['data']   = $this->m->daftarMataKuliah()->result();
    view('laboran/header', $data);
    view('laboran/courses', $data);
    view('laboran/footer');
  }

  public function SaveCourses()
  {
    set_rules('kode_mk', 'Courses Code', 'required|trim');
    set_rules('nama_mk', 'Courses Name', 'required|trim');
    set_rules('sks_mk', 'SKS', 'required|trim');
    if (validation_run() == false) {
      redirect('Laboran/Courses');
    } else {
      $kode_mk  = input('kode_mk');
      $nama_mk  = input('nama_mk');
      $sks_mk   = input('sks_mk');
      $input    = array(
        'kode_mk' => $kode_mk,
        'nama_mk' => $nama_mk,
        'sks'     => $sks_mk
      );
      $this->m->insertData('matakuliah', $input);
      set_flashdata('msg', '<div class="alert alert-success msg">Courses Successfully Saved</div>');
      redirect('Practicum/Courses');
    }
  }

  public function EditCourses()
  {
    set_rules('id_mk', 'ID MK', 'required|trim');
    set_rules('kode_mk', 'Courses Code', 'required|trim');
    set_rules('nama_mk', 'Courses Name', 'required|trim');
    set_rules('sks_mk', 'SKS', 'required|trim');
    if (validation_run() == false) {
      redirect('Laboran/Courses');
    } else {
      $id_mk    = input('id_mk');
      $kode_mk  = input('kode_mk');
      $nama_mk  = input('nama_mk');
      $sks_mk   = input('sks_mk');
      $input    = array(
        'kode_mk' => $kode_mk,
        'nama_mk' => $nama_mk,
        'sks'     => $sks_mk
      );
      $this->m->updateData('matakuliah', $input, 'id_mk', $id_mk);
      set_flashdata('msg', '<div class="alert alert-success msg">Courses Successfully Updated</div>');
      redirect('Practicum/Courses');
    }
  }

  public function DeleteCourses($id)
  {
    if (userdata('login') == 'laboran') {
      $this->m->deleteData('matakuliah', 'id_mk', $id);
      redirect('Practicum/Courses');
    } else {
      redirect();
    }
  }

  public function PracticumAssistant()
  {
    $data           = $this->data;
    $data['title']  = 'Courses | SIM Laboratorium';
    $data['data']   = $this->m->daftarAsprak()->result();
    view('laboran/header', $data);
    view('laboran/practicum_assistant', $data);
    view('laboran/footer');
  }

  public function AddAsprakCSV()
  {
    if (empty($_FILES['file']['name'])) {
      redirect('Practicum/PracticumAssistant');
    } else {
      $file = $_FILES['file']['tmp_name'];
      $ekstensi_file  = explode('.', $_FILES['file']['name']);
      if (strtolower(end($ekstensi_file)) === 'csv' && $_FILES['file']['size'] > 0) {
        $handle = fopen($file, 'r');
        $i = 0;
        $sama = array();
        while (($row = fgetcsv($handle, 2048))) {
          $i++;
          if ($i == 1) {
            continue;
          }
          $input = array(
            'nim_asprak'  => $row[0],
            'nama_asprak' => $row[1]
          );
          $cek_data = $this->db->get_where('asprak', array('nim_asprak' => $row[0]))->row();
          if ($cek_data) {
            array_push($sama, $input);
          } else {
            $this->db->insert('asprak', $input);
          }
        }
        fclose($handle);
      }
      set_flashdata('msg', '<div class="alert alert-success msg">Practicum Assistant Successfully Saved</div>');
      redirect('Practicum/PracticumAssistant');
    }
  }

  public function ajaxMataKuliah()
  {
    $hasil  = '';
    $id     = input('id');
    $cek    = $this->db->get_where('matakuliah', array('id_mk' => $id))->row();
    if ($cek == true) {
      $hasil .= $cek->kode_mk . ' ' . $cek->nama_mk;
    }
    echo $hasil;
  }
}
