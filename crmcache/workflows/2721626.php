<?php

$workflows=array (
  0 => '2721626',
  'workflowsid' => '2721626',
  1 => '发票作废/红冲',
  'workflowsname' => '发票作废/红冲',
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
    8 => 
    array (
      0 => '2721627',
      'workflowstagesid' => '2721627',
      1 => '申请作废',
      'workflowstagesname' => '申请作废',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '8',
      'sequence' => '8',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2721626',
      'workflowsid' => '2721626',
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
      15 => 'APPLICATION_VOID',
      'workflowstagesflag' => 'APPLICATION_VOID',
      16 => '1',
      'reviewer' => '1',
      'nostd' => 
      array (
      ),
    ),
    9 => 
    array (
      0 => '2721628',
      'workflowstagesid' => '2721628',
      1 => '申请红冲',
      'workflowstagesname' => '申请红冲',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '9',
      'sequence' => '9',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2721626',
      'workflowsid' => '2721626',
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
      15 => 'APPLICATION_RED',
      'workflowstagesflag' => 'APPLICATION_RED',
      16 => '1',
      'reviewer' => '1',
      'nostd' => 
      array (
      ),
    ),
    10 => 
    array (
      0 => '2721629',
      'workflowstagesid' => '2721629',
      1 => '发票管理员通过',
      'workflowstagesname' => '发票管理员通过',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '10',
      'sequence' => '10',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2721626',
      'workflowsid' => '2721626',
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
      15 => 'INVOICE_ADMIN_THROUGH',
      'workflowstagesflag' => 'INVOICE_ADMIN_THROUGH',
      16 => '1',
      'reviewer' => '1',
      'nostd' => 
      array (
      ),
    ),
  ),
);
?>