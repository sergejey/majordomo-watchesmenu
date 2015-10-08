<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  //searching 'TITLE' (varchar)
  global $title;
  if ($title!='') {
   $qry.=" AND TITLE LIKE '%".DBSafe($title)."%'";
   $out['TITLE']=$title;
  }
  if (IsSet($this->script_id)) {
   $script_id=$this->script_id;
   $qry.=" AND SCRIPT_ID='".$this->script_id."'";
  } else {
   global $script_id;
  }
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['watchesmenu_qry'];
  } else {
   $session->data['watchesmenu_qry']=$qry;
  }
  if (!$qry) $qry="1";
  // FIELDS ORDER
  global $sortby_watchesmenu;
  if (!$sortby_watchesmenu) {
   $sortby_watchesmenu=$session->data['watchesmenu_sort'];
  } else {
   if ($session->data['watchesmenu_sort']==$sortby_watchesmenu) {
    if (Is_Integer(strpos($sortby_watchesmenu, ' DESC'))) {
     $sortby_watchesmenu=str_replace(' DESC', '', $sortby_watchesmenu);
    } else {
     $sortby_watchesmenu=$sortby_watchesmenu." DESC";
    }
   }
   $session->data['watchesmenu_sort']=$sortby_watchesmenu;
  }
  if (!$sortby_watchesmenu) $sortby_watchesmenu="PRIORITY DESC, TITLE";
  $out['SORTBY']=$sortby_watchesmenu;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM watchesmenu WHERE $qry ORDER BY ".$sortby_watchesmenu);
  if ($res[0]['ID']) {
   colorizeArray($res);
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
   }
   $out['RESULT']=$res;
  }
