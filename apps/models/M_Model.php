<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Model extends CI_Model
{

  function insertData($tabel, $data)
  {
    $this->db->insert($tabel, $data);
  }

  function updateData($tabel, $data, $where, $nilai)
  {
    $this->db->where($where, $nilai);
    $this->db->update($tabel, $data);
  }

  function deleteData($tabel, $where, $nilai)
  {
    $this->db->where($where, $nilai);
    $this->db->delete($tabel);
  }

  function hitungKomplain()
  {
    $this->db->select('count(idKomplain) komplain');
    $this->db->from('komplain');
    $this->db->where('diperbaikiOleh', null);
    return $this->db->get();
  }

  function hitungPeminjamanAlat()
  {
    $this->db->select('count(idPinjamAlat) pinjamalat');
    $this->db->from('peminjamanalat');
    $this->db->where('status != "Selesai"');
    return $this->db->get();
  }

  function hitungPeminjamanLab()
  {
    $this->db->select('count(idPinjamLab) pinjamlab');
    $this->db->from('peminjamanlab');
    $this->db->where('status != "Selesai"');
    return $this->db->get();
  }

  function hitungKomplainBelumSelesai()
  {
    $this->db->select('count(idKomplain) komplain_belum');
    $this->db->from('komplain');
    $this->db->where('statusKomplain', '0');
    $this->db->where('year(tglKomplain) = year(current_date())');
    $this->db->group_by('year(tglKomplain)');
    return $this->db->get();
  }

  function hitungKomplainSelesai()
  {
    $this->db->select('count(idKomplain) komplain_selesai');
    $this->db->from('komplain');
    $this->db->where('statusKomplain', '1');
    $this->db->where('year(tglKomplain) = year(current_date())');
    $this->db->group_by('year(tglKomplain)');
    return $this->db->get();
  }

  function hitungPeminjamanLabBelumSelesai()
  {
    $this->db->select('count(idPinjamLab) lab_belum');
    $this->db->from('peminjamanlab');
    $this->db->where('status != "Selesai"');
    $this->db->where('year(tglPinjam) = year(current_date())');
    $this->db->group_by('year(tglPinjam)');
    return $this->db->get();
  }

  function hitungPeminjamanLabSelesai()
  {
    $this->db->select('count(idPinjamLab) lab_selesai');
    $this->db->from('peminjamanlab');
    $this->db->where('status = "Selesai"');
    $this->db->where('year(tglPinjam) = year(current_date())');
    $this->db->group_by('year(tglPinjam)');
    return $this->db->get();
  }

  function hitungPeminjamanAlatBelumSelesai()
  {
    $this->db->select('count(idPinjamAlat) alat_belum');
    $this->db->from('peminjamanalat');
    $this->db->where('status != "Selesai"');
    $this->db->where('year(tglPinjam) = year(current_date())');
    $this->db->group_by('year(tglPinjam)');
    return $this->db->get();
  }

  function hitungPeminjamanAlatSelesai()
  {
    $this->db->select('count(idPinjamAlat) alat_selesai');
    $this->db->from('peminjamanalat');
    $this->db->where('status = "Selesai"');
    $this->db->where('year(tglPinjam) = year(current_date())');
    $this->db->group_by('year(tglPinjam)');
    return $this->db->get();
  }

  function grafikKomplain()
  {
    $this->db->select('date_format(tglKomplain, "%b") bulan, count(idKomplain) jumlah');
    $this->db->from('komplain');
    $this->db->where('year(tglKomplain) = year(curdate())');
    $this->db->group_by('date_format(tglKomplain, "%b")');
    $this->db->order_by('tglKomplain', 'asc');
    return $this->db->get();
  }

  function daftarPengumuman()
  {
    $this->db->select('*');
    $this->db->from('pengumuman');
    $this->db->order_by('tglPengumuman', 'desc');
    $this->db->limit('7');
    return $this->db->get();
  }

  function daftarLaboratorium()
  {
    $this->db->select('*');
    $this->db->from('laboratorium');
    $this->db->order_by('namaLab', 'asc');
    return $this->db->get();
  }

  function dataStockList($id)
  {
    $this->db->where('substring(sha1(idAlat), 7, 4) = "' . $id . '"');
    return $this->db->get('alatlab');
  }

  function daftarLabPraktikum()
  {
    return $this->db->order_by('namaLab', 'asc')->get_where('laboratorium', array('tipeLab' => 'Practicum Lab'));
  }

  function daftarLabRiset()
  {
    return $this->db->order_by('namaLab', 'asc')->get_where('laboratorium', array('tipeLab' => 'Research Lab'));
  }

  function detailLaboratorium($id)
  {
    $this->db->where('substring(sha1(idLab), 7, 4) = "' . $id . '"');
    return $this->db->get('laboratorium');
  }

  function pjAslab($id, $periode)
  {
    $this->db->select('aslab.namaLengkap');
    $this->db->from('aslab');
    $this->db->join('asistenlab', 'aslab.idAslab = asistenlab.idAslab');
    $this->db->where('substring(sha1(asistenlab.idLab), 7, 4) = "' . $id . '"');
    $this->db->where('aslab.tahunAjaran', $periode);
    $this->db->order_by('aslab.namaLengkap', 'asc');
    return $this->db->get();
  }

  function daftarInventarisLab($id)
  {
    $this->db->select('alatlab.namaAlat, sum(alatlab.jumlah) jumlah, alatlab.kondisi, alatlab.catatan');
    $this->db->from('laboratorium');
    $this->db->join('alatlab', 'laboratorium.idLab = alatlab.idLab');
    $this->db->where('substring(sha1(laboratorium.idLab), 7, 4) = "' . $id . '"');
    $this->db->group_by('alatlab.namaAlat');
    return $this->db->get();
  }

  function daftarMataKuliah()
  {
    return $this->db->order_by('kode_mk')->get('matakuliah');
  }
}
