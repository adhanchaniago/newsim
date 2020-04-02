<?php

class M_Laboran extends CI_Model
{

  function dataStockList($id)
  {
    $this->db->where('substring(sha1(idAlat), 7, 4) = "' . $id . '"');
    return $this->db->get('alatlab');
  }

  function daftarLaboratorium()
  {
    $this->db->select('*');
    $this->db->from('laboratorium');
    $this->db->order_by('namaLab', 'asc');
    return $this->db->get();
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

  function daftarLabPraktikum()
  {
    return $this->db->order_by('namaLab', 'asc')->get_where('laboratorium', array('tipeLab' => 'Practicum Lab'));
  }

  function daftarLabRiset()
  {
    return $this->db->get_where('laboratorium', array('tipeLab' => 'Research Lab'));
  }

  function daftarMataKuliah()
  {
    return $this->db->order_by('kode_mk')->get('matakuliah');
  }

  function daftarAslab($tahun)
  {
    return $this->db->order_by('namaLengkap', 'asc')->get_where('aslab', array('tahunAjaran' => $tahun));
  }

  function detailAslab($id)
  {
    $this->db->where('substring(sha1(idAslab), 7, 4) = "' . $id . '"');
    return $this->db->get('aslab');
  }

  function daftarPJAslab()
  {
    $this->db->select('laboratorium.namaLab, asistenlab.idAslab');
    $this->db->from('laboratorium');
    $this->db->join('asistenlab', 'laboratorium.idLab = asistenlab.idLab');
    return $this->db->get();
  }

  function detailPJAslab($id)
  {
    $this->db->select('laboratorium.namaLab, laboratorium.idLab, substring(sha1(asistenlab.idAslab), 7, 4) idAslab, asistenlab.idLab');
    $this->db->from('laboratorium');
    $this->db->join('asistenlab', 'laboratorium.idLab = asistenlab.idLab');
    $this->db->where('substring(sha1(asistenlab.idAslab), 7, 4) = "' . $id . '"');
    return $this->db->get();
  }

  function kegiatanAslab($id)
  {
    $this->db->select('date_format(aslabMasuk, "%Y-%m-%d") aslabMasuk, date_format(aslabMasuk, "%H:%i") masuk, if (aslabKeluar, date_format(aslabKeluar, "%H:%i"), "-") keluar, jurnal');
    $this->db->from('jurnalaslab');
    $this->db->where('substring(sha1(idAslab), 7, 4) = "' . $id . '"');
    $this->db->order_by('aslabMasuk', 'desc');
    return $this->db->get();
  }

  function kegiatanAslabBulan($id, $periode)
  {
    $this->db->select('date_format(aslabMasuk, "%Y-%m-%d") aslabMasuk, date_format(aslabMasuk, "%H:%i") masuk, if (aslabKeluar, date_format(aslabKeluar, "%H:%i"), "-") keluar, jurnal');
    $this->db->from('jurnalaslab');
    $this->db->where('substring(sha1(idAslab), 7, 4) = "' . $id . '"');
    $this->db->where($periode);
    $this->db->order_by('aslabMasuk', 'desc');
    return $this->db->get();
  }

  function jadwalLab()
  {
    $this->db->select('concat(matakuliah.kode_mk, " | ", matakuliah.nama_mk, " | ", jadwal_lab.kelas, " / ", jadwal_lab.kode_dosen) title, jadwal_lab.jam_masuk start, jadwal_lab.jam_selesai end, jadwal_lab.hari_ke, prodi.color');
    $this->db->from('jadwal_lab');
    $this->db->join('matakuliah', 'jadwal_lab.id_mk = matakuliah.id_mk');
    $this->db->join('prodi', 'jadwal_lab.id_prodi = prodi.id_prodi');
    return $this->db->get();
  }

  function jadwalPerLab($id)
  {
    $this->db->select('concat(matakuliah.kode_mk, " | ", matakuliah.nama_mk, " | ", jadwal_lab.kelas, " / ", jadwal_lab.kode_dosen) title, jadwal_lab.jam_masuk start, jadwal_lab.jam_selesai end, jadwal_lab.hari_ke, prodi.color');
    $this->db->from('jadwal_lab');
    $this->db->join('matakuliah', 'jadwal_lab.id_mk = matakuliah.id_mk');
    $this->db->join('prodi', 'jadwal_lab.id_prodi = prodi.id_prodi');
    $this->db->where('substring(sha1(jadwal_lab.id_lab), 7, 4) = "' . $id . '"');
    return $this->db->get();
  }
}
