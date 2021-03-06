<?php

$workflows=array (
  0 => '778075',
  'workflowsid' => '778075',
  1 => '预开票加签审核流程',
  'workflowsname' => '预开票加签审核流程',
  2 => '0',
  'iscontract' => '0',
  3 => '0',
  'iscontent' => '0',
  4 => '0',
  'isattachment' => '0',
  5 => '0',
  'ischargeback' => '0',
  6 => 'Newinvoice',
  'mountmodule' => 'Newinvoice',
  7 => NULL,
  'dataupdate' => NULL,
  'stage' => 
  array (
    1 => 
    array (
      0 => '778076',
      'workflowstagesid' => '778076',
      1 => '第一审核人',
      'workflowstagesname' => '第一审核人',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H115 |##| H116 |##| H120 |##| H78 |##| H79 |##| H83 |##| H86 |##| H87 |##| H94 |##| H95 |##| H96 |##',
      'isrole' => 'H115 |##| H116 |##| H120 |##| H78 |##| H79 |##| H83 |##| H86 |##| H87 |##| H94 |##| H95 |##| H96 |##',
      4 => '0',
      'iscost' => '0',
      5 => '1',
      'sequence' => '1',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '778075',
      'workflowsid' => '778075',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_check',
      'modulestatus' => 'b_check',
      14 => '0',
      'isnextnode' => '0',
      15 => '',
      'workflowstagesflag' => '',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    2 => 
    array (
      0 => '778077',
      'workflowstagesid' => '778077',
      1 => '第二审核人审核',
      'workflowstagesname' => '第二审核人审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H115 |##| H123 |##| H78 |##| H79 |##| H94',
      'isrole' => 'H115 |##| H123 |##| H78 |##| H79 |##| H94',
      4 => '0',
      'iscost' => '0',
      5 => '2',
      'sequence' => '2',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '778075',
      'workflowsid' => '778075',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_check',
      'modulestatus' => 'b_check',
      14 => '0',
      'isnextnode' => '0',
      15 => '',
      'workflowstagesflag' => '',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    3 => 
    array (
      0 => '778078',
      'workflowstagesid' => '778078',
      1 => '财务主管审核',
      'workflowstagesname' => '财务主管审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H89',
      'isrole' => 'H89',
      4 => '0',
      'iscost' => '0',
      5 => '3',
      'sequence' => '3',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '778075',
      'workflowsid' => '778075',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_check',
      'modulestatus' => 'b_check',
      14 => '0',
      'isnextnode' => '0',
      15 => '',
      'workflowstagesflag' => '',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    4 => 
    array (
      0 => '778079',
      'workflowstagesid' => '778079',
      1 => '发票管理员开立发票',
      'workflowstagesname' => '发票管理员开立发票',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H102 |##| H121 |##| H89 |##| H90 |##| H171',
      'isrole' => 'H102 |##| H121 |##| H89 |##| H90 |##| H171',
      4 => '0',
      'iscost' => '0',
      5 => '4',
      'sequence' => '4',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '778075',
      'workflowsid' => '778075',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_check',
      'modulestatus' => 'b_check',
      14 => '0',
      'isnextnode' => '0',
      15 => 'open_invoice',
      'workflowstagesflag' => 'open_invoice',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    5 => 
    array (
      0 => '778081',
      'workflowstagesid' => '778081',
      1 => '发票领取',
      'workflowstagesname' => '发票领取',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H102 |##| H121 |##| H89 |##| H90 |##| H171',
      'isrole' => 'H102 |##| H121 |##| H89 |##| H90 |##| H171',
      4 => '0',
      'iscost' => '0',
      5 => '5',
      'sequence' => '5',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '778075',
      'workflowsid' => '778075',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_check',
      'modulestatus' => 'b_check',
      14 => '0',
      'isnextnode' => '0',
      15 => 'receive_invoice',
      'workflowstagesflag' => 'receive_invoice',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
  ),
);
?>