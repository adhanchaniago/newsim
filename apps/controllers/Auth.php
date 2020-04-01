<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    if (userdata('login') == 'laboran') {
      redirect('Laboran/Dashboard');
    } elseif (userdata('login') == 'aslab') {
      #
    } elseif (userdata('login') == 'asprak') {
      redirect('Asprak/Dashboard');
    } elseif (userdata('login') == 'grant') {
      #
    } elseif (userdata('login') == 'magang') {
      #
    } else {
      set_rules('username_user', 'Username', 'required|trim');
      set_rules('password_user', 'Password', 'required|trim');
      if (validation_run() == false) {
        $data['title']  = 'SIM Laboratorium | Telkom University';
        view('auth/index', $data);
      } else {
        $username = input('username_user');
        $password = sha1(input('password_user'));
        $where    = array('username' => $username, 'password' => $password);
        $cekData  = $this->auth->cekUser($where)->row();
        if ($cekData) {
          $history = array(
            'ip'            => $this->cekIP(),
            'browser'       => $this->cekUserAgent(),
            'platform'      => $this->agent->platform(),
            'username'      => $username,
            'tanggal_login' => date('Y-m-d H:i:s')
          );
          $this->auth->insertData('history_login', $history);
          if ($cekData->jenisAkses == 'laboran') {
            $session = array(
              'login'     => $cekData->jenisAkses,
              'id'        => $cekData->idUser,
              'username'  => $cekData->username,
              'nama'      => 'Staff Laboratory',
              'jabatan'   => $cekData->jabatan
            );
            set_userdata($session);
            redirect('Laboran/Dashboard');
          } elseif ($cekData->jenisAkses == 'aslab') {
            echo 2;
          } elseif ($cekData->jenisAkses == 'asprak') {
            $session = array(
              'login'     => $cekData->jenisAkses,
              'id'        => $cekData->idUser,
              'username'  => $cekData->username,
              'nim'       => $cekData->nimAsprak,
              'jabatan'   => $cekData->jabatan
            );
            set_userdata($session);
            redirect('Asprak/Dashboard');
          } elseif ($cekData->jenisAkses == 'magang') {
            echo 4;
          } elseif ($cekData->jenisAkses == 'grant') {
            echo 5;
          }
        } else {
          set_flashdata('msg', '<div class="alert alert-danger">Incorrect Username or Password</div>');
          redirect('Auth');
        }
      }
    }
  }

  public function RegisterAslab()
  {
    set_rules('nim_user', 'NIM', 'required|trim');
    set_rules('username_user', 'Username', 'required|trim');
    set_rules('password_user', 'Password', 'required|trim');
    if (validation_run() == false) {
      $data['title']  = 'Laboratory Assistant | SIM Laboratorium';
      $data['data']   = $this->auth->daftarAslab('2019/2020')->result();
      view('auth/register_aslab', $data);
    } else {
      $nim_user       = input('nim_user');
      $username_user  = input('username_user');
      $password_user  = sha1(input('password_user'));
      $input          = array(
        'username'    => $username_user,
        'password'    => $password_user,
        'idAslab'     => $nim_user,
        'jenisAkses'  => 'aslab',
        'status'      => '1'
      );
      $this->auth->insertData('users', $input);
      set_flashdata('msg', '<div class="alert alert-success msg">Thank you for register. Now you can login using your account.</div>');
      redirect();
    }
  }

  public function RegisterAsprak()
  {
    set_rules('nim_user', 'NIM', 'required|trim');
    set_rules('username_user', 'Username', 'required|trim');
    set_rules('password_user', 'Password', 'required|trim');
    if (validation_run() == false) {
      $data['title']  = 'Register Practicum Assistant | SIM Laboratorium';
      $data['data']   = $this->auth->daftarAsprak()->result();
      view('auth/register_asprak', $data);
    } else {
      $nim_user       = input('nim_user');
      $username_user  = input('username_user');
      $password_user  = sha1(input('password_user'));
      $input          = array(
        'username'    => $username_user,
        'password'    => $password_user,
        'nimAsprak'   => $nim_user,
        'jenisAkses'  => 'asprak',
        'jabatan'     => 'Asisten Praktikum',
        'status'      => '1'
      );
      $this->auth->insertData('users', $input);
      set_flashdata('msg', '<div class="alert alert-success msg">Thank you for register. Now you can login using your account.</div>');
      redirect();
    }
  }

  public function RegisterLecture()
  {
    set_rules('nip_user', 'NIP', 'required|trim');
    set_rules('nama_user', 'Nama', 'required|trim');
    set_rules('username_user', 'Username', 'required|trim');
    set_rules('password_user', 'Password', 'required|trim');
    if (validation_run() == false) {
      $data['title']  = 'Register Lecture | SIM Laboratorium';
      view('auth/register_dosen', $data);
    } else {
      $nip_user       = input('nip_user');
      $nama_user      = input('nama_user');
      $username_user  = input('username_user');
      $password_user  = sha1(input('password_user'));
      $input          = array(
        'username'    => $username_user,
        'password'    => $password_user,
        'nipDosen'    => $nip_user,
        'jenisAkses'  => 'dosen',
        'jabatan'     => 'Lecture',
        'status'      => '0'
      );
      $this->auth->insertData('users', $input);
      set_flashdata('msg', '<div class="alert alert-success msg">Thank you for register. Now you can login using your account.</div>');
      redirect();
    }
  }

  public function RegisterStaff()
  {
    #
  }

  public function ajaxCekUsername()
  {
    if (!empty($_POST['username'])) {
      $cek_username = $this->auth->cekUsername($_POST['username'])->row()->jumlah;
      if ($cek_username > 0) {
        echo 'Username <b>' . $_POST['username'] . ' </b>already exist';
      } else {
        echo 'null';
      }
    }
  }

  private function cekIP()
  {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip_address = $_SERVER['REMOTE_ADDR'];
    }
    return $ip_address;
  }

  private function cekUserAgent()
  {
    if ($this->agent->is_browser()) {
      $agent = $this->agent->browser() . ' ' . $this->agent->version();
    } elseif ($this->agent->is_robot()) {
      $agent = $this->agent->robot();
    } elseif ($this->agent->is_mobile()) {
      $agent = $this->agent->mobile();
    } else {
      $agent = 'Unidentified User Agent';
    }
    return $agent;
  }

  public function Logout()
  {
    $this->session->sess_destroy();
    redirect();
  }
}
