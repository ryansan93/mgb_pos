<?php

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Event extends Public_Controller {

  /**
  * Constructor
  */
  function __construct() {
    parent::__construct ();
  }

  public function save($model, $message = null) {
    $m_log = new \Model\Storage\LogTables_model();
    $now = $m_log->getDate();
    $m_log->tbl_name = $model->getTable();
    $m_log->tbl_id = $model->id;
    $m_log->user_id = $this->userid;
    $m_log->waktu = $now['waktu'];
    $m_log->deskripsi = $message ?: $this->userdata['Nama_User'];
    $m_log->_action = 'insert';
    // $m_log->_json = $model->toJson();
    $m_log->save ();

    return $m_log;
  }

  public function update($model, $message = null) {
    $m_log = new \Model\Storage\LogTables_model();
    $now = $m_log->getDate();
    $m_log->tbl_name = $model->getTable();
    $m_log->tbl_id = $model->id;
    $m_log->user_id = $this->userid;
    $m_log->waktu = $now['waktu'];
    $m_log->deskripsi = $message ?: $this->userdata['Nama_User'];
    $m_log->_action = 'update';
    // $m_log->_json = $model->toJson();
    $m_log->save ();

    return $m_log;
  }

  public function delete($model, $message = null) {
    $m_log = new \Model\Storage\LogTables_model();
    $now = $m_log->getDate();
    $m_log->tbl_name = $model->getTable();
    $m_log->tbl_id = $model->id;
    $m_log->user_id = $this->userid;
    $m_log->waktu = $now['waktu'];
    $m_log->deskripsi = $message ?: $this->userdata['Nama_User'];
    $m_log->_action = 'delete';
    // $m_log->_json = $model->toJson();
    $m_log->save ();

    return $m_log;
  }
}
