<?php
require 'vendor/autoload.php';

use App\dbconnect as dbconnect;
use App\dbcreate as dbcreate;

$pdo = (new dbconnect())->connect();
$db = new dbcreate($pdo);

// create new projects and tasks tables
$db->createtables();

/*
insert data into projects table
$db->insertdata('projects',array('project_name'=>'Project Number 1'));
$db->insertdata('projects',array('project_name'=>'Project Number 2'));
$db->insertdata('projects',array('project_name'=>'Project Number 3'));
*/

$gvar = $_GET;
if ($gvar != null && $gvar['tag'] == 'add') {
	include 'hedit.html';
}

if ($gvar != null && $gvar['tag'] == 'delete') {
	$db->deletedata($gvar['tblname'],$gvar['id']);
	$projects = $db->getdata('projects',null,null);
	include 'hindex.html';
}

if ($gvar != null && $gvar['tag'] == 'edit') {
	if ($gvar['tblname'] == 'projects')
		$proj = $db->getdata($gvar['tblname'],$gvar['id'],null);
	else
		$task = $db->getdata($gvar['tblname'],null,$gvar['id']);
	include 'hedit.html';
}

$pvar = $_POST;
if ($pvar != null && $_SERVER["REQUEST_METHOD"] == "POST" && (isset($pvar['submit']) && $pvar['submit'] == 'edit')) {
	unset($pvar['submit']);
	isset($pvar['task_id']) ? $db->updatedata('tasks',$pvar) : $db->updatedata('projects',$pvar);
}

if ($pvar != null && $_SERVER["REQUEST_METHOD"] == "POST" && (isset($pvar['submit']) && $pvar['submit'] == 'add')) {
	unset($pvar['submit']);
	$db->insertdata('tasks',$pvar);
}

if ($gvar == null) {
	//getdata from projects table
	$projects = $db->getdata('projects',null,null);
	
	include 'hindex.html';
}
?>