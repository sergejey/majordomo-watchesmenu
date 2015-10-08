<?php
/**
* Watchesmenu 
*
* Watchesmenu
*
* @package project
* @author Serge J. <jey@tut.by>
* @copyright http://www.atmatic.eu/ (c)
* @version 0.1 (wizard, 15:10:01 [Oct 07, 2015])
*/
//
//
class watchesmenu extends module {
/**
* watchesmenu
*
* Module class constructor
*
* @access private
*/
function watchesmenu() {
  $this->name="watchesmenu";
  $this->title="Watches Menu";
  $this->module_category="<#LANG_SECTION_OBJECTS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  if (IsSet($this->script_id)) {
   $out['IS_SET_SCRIPT_ID']=1;
  }
  if ($this->single_rec) {
   $out['SINGLE_REC']=1;
  }
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='watchesmenu' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_watchesmenu') {
   $this->search_watchesmenu($out);
  }
  if ($this->view_mode=='edit_watchesmenu') {
   $this->edit_watchesmenu($out, $this->id);
  }
  if ($this->view_mode=='delete_watchesmenu') {
   $this->delete_watchesmenu($this->id);
   $this->redirect("?");
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 if ($this->ajax) {
  global $clicked;
  if ($clicked) {
   $item=SQLSelectOne("SELECT * FROM watchesmenu WHERE ID='".(int)$clicked."'");

   if ($item['LINKED_OBJECT'] && $item['LINKED_METHOD']) {
    callMethod($item['LINKED_OBJECT'].'.'.$item['LINKED_METHOD']);
   }
   if ($item['SCRIPT_ID']) {
    runScript($item['SCRIPT_ID']);
   }

   $result=array('RESULT'=>'OK');
   header("Content-type:application/json");
   echo json_encode($result);exit;
  } else {
   $items=SQLSelect("SELECT ID, TITLE, SUBTITLE FROM watchesmenu ORDER BY PRIORITY DESC, TITLE");
   $total=count($items);
   for($i=0;$i<$total;$i++) {
    $items[$i]['TITLE']=processTitle($items[$i]['TITLE']);
    if ($items[$i]['SUBTITLE']) {
     $items[$i]['SUBTITLE']=processTitle($items[$i]['SUBTITLE']);
    }
   }
   header("Content-type:application/json");
   echo json_encode(array('items'=>$items));exit;
  }
 }
}
/**
* watchesmenu search
*
* @access public
*/
 function search_watchesmenu(&$out) {
  require(DIR_MODULES.$this->name.'/watchesmenu_search.inc.php');
 }
/**
* watchesmenu edit/add
*
* @access public
*/
 function edit_watchesmenu(&$out, $id) {
  require(DIR_MODULES.$this->name.'/watchesmenu_edit.inc.php');
 }
/**
* watchesmenu delete record
*
* @access public
*/
 function delete_watchesmenu($id) {
  $rec=SQLSelectOne("SELECT * FROM watchesmenu WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM watchesmenu WHERE ID='".$rec['ID']."'");
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS watchesmenu');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall() {
/*
watchesmenu - Watchesmenu
*/
  $data = <<<EOD
 watchesmenu: ID int(10) unsigned NOT NULL auto_increment
 watchesmenu: PRIORITY int(10) NOT NULL DEFAULT '0'
 watchesmenu: TITLE varchar(255) NOT NULL DEFAULT ''
 watchesmenu: SUBTITLE varchar(255) NOT NULL DEFAULT ''
 watchesmenu: LINKED_OBJECT varchar(255) NOT NULL DEFAULT ''
 watchesmenu: LINKED_METHOD varchar(255) NOT NULL DEFAULT ''
 watchesmenu: SCRIPT_ID int(10) NOT NULL DEFAULT '0'
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgT2N0IDA3LCAyMDE1IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
