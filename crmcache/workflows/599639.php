<?php

$workflows=array (
  0 => '599639',
  'workflowsid' => '599639',
  1 => '预开票审核流程',
  'workflowsname' => '预开票审核流程',
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
      0 => '599643',
      'workflowstagesid' => '599643',
      1 => '部门总监审核',
      'workflowstagesname' => '部门总监审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H115 |##| H78 |##| H79 |##| H83 |##| H86 |##| H87 |##| H88 |##| H89 |##| H94',
      'isrole' => 'H115 |##| H78 |##| H79 |##| H83 |##| H86 |##| H87 |##| H88 |##| H89 |##| H94',
      4 => '0',
      'iscost' => '0',
      5 => '1',
      'sequence' => '1',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '599639',
      'workflowsid' => '599639',
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
      15 => 'AUDIT_VERIFICATION',
      'workflowstagesflag' => 'AUDIT_VERIFICATION',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    2 => 
    array (
      0 => '599644',
      'workflowstagesid' => '599644',
      1 => '发票管理员开立发票',
      'workflowstagesname' => '发票管理员开立发票',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H102 |##| H121 |##| H125 |##| H89 |##| H90 |##| H171',
      'isrole' => 'H102 |##| H121 |##| H125 |##| H89 |##| H90 |##| H171',
      4 => '0',
      'iscost' => '0',
      5 => '2',
      'sequence' => '2',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '599639',
      'workflowsid' => '599639',
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
    3 => 
    array (
      0 => '599645',
      'workflowstagesid' => '599645',
      1 => '发票领取',
      'workflowstagesname' => '发票领取',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H102 |##| H121 |##| H89 |##| H90 |##| H171',
      'isrole' => 'H102 |##| H121 |##| H89 |##| H90 |##| H171',
      4 => '0',
      'iscost' => '0',
      5 => '3',
      'sequence' => '3',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '599639',
      'workflowsid' => '599639',
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