<?php

$workflows=array (
  0 => '2721906',
  'workflowsid' => '2721906',
  1 => '供应商结算单审核流',
  'workflowsname' => '供应商结算单审核流',
  2 => '0',
  'iscontract' => '0',
  3 => '0',
  'iscontent' => '0',
  4 => '0',
  'isattachment' => '0',
  5 => '0',
  'ischargeback' => '0',
  6 => 'SupplierStatement',
  'mountmodule' => 'SupplierStatement',
  7 => NULL,
  'dataupdate' => NULL,
  'stage' => 
  array (
    1 => 
    array (
      0 => '2721907',
      'workflowstagesid' => '2721907',
      1 => '第一级审核',
      'workflowstagesname' => '第一级审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '1',
      'sequence' => '1',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2721906',
      'workflowsid' => '2721906',
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
      16 => '1',
      'reviewer' => '1',
      'nostd' => 
      array (
      ),
    ),
    2 => 
    array (
      0 => '2721908',
      'workflowstagesid' => '2721908',
      1 => '第二级审核',
      'workflowstagesname' => '第二级审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '2',
      'sequence' => '2',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2721906',
      'workflowsid' => '2721906',
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
      15 => 'TWO_VERIFICATION',
      'workflowstagesflag' => 'TWO_VERIFICATION',
      16 => '1',
      'reviewer' => '1',
      'nostd' => 
      array (
      ),
    ),
    3 => 
    array (
      0 => '2721909',
      'workflowstagesid' => '2721909',
      1 => '业务负责人审核',
      'workflowstagesname' => '业务负责人审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '3',
      'sequence' => '3',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2721906',
      'workflowsid' => '2721906',
      9 => '0',
      'timelimit' => '0',
      10 => 'ProductCheck',
      'handleaction' => 'ProductCheck',
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
      16 => '1',
      'reviewer' => '1',
      'nostd' => 
      array (
      ),
    ),
    4 => 
    array (
      0 => '2721910',
      'workflowstagesid' => '2721910',
      1 => '责任会计审核',
      'workflowstagesname' => '责任会计审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H127 |##| H90',
      'isrole' => 'H127 |##| H90',
      4 => '0',
      'iscost' => '0',
      5 => '4',
      'sequence' => '4',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2721906',
      'workflowsid' => '2721906',
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
      16 => '1',
      'reviewer' => '1',
      'nostd' => 
      array (
      ),
    ),
    5 => 
    array (
      0 => '2721911',
      'workflowstagesid' => '2721911',
      1 => '财务主管审核',
      'workflowstagesname' => '财务主管审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '5',
      'sequence' => '5',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2721906',
      'workflowsid' => '2721906',
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
      15 => 'CWSH',
      'workflowstagesflag' => 'CWSH',
      16 => '1',
      'reviewer' => '1',
      'nostd' => 
      array (
      ),
    ),
    6 => 
    array (
      0 => '2721912',
      'workflowstagesid' => '2721912',
      1 => '结算单打印',
      'workflowstagesname' => '结算单打印',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H104 |##| H90',
      'isrole' => 'H104 |##| H90',
      4 => '0',
      'iscost' => '0',
      5 => '6',
      'sequence' => '6',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2721906',
      'workflowsid' => '2721906',
      9 => '0',
      'timelimit' => '0',
      10 => 'maincompany',
      'handleaction' => 'maincompany',
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
      16 => '1',
      'reviewer' => '1',
      'nostd' => 
      array (
      ),
    ),
    7 => 
    array (
      0 => '2721913',
      'workflowstagesid' => '2721913',
      1 => '结算单盖章',
      'workflowstagesname' => '结算单盖章',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H104 |##| H90',
      'isrole' => 'H104 |##| H90',
      4 => '0',
      'iscost' => '0',
      5 => '7',
      'sequence' => '7',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2721906',
      'workflowsid' => '2721906',
      9 => '0',
      'timelimit' => '0',
      10 => 'maincompany',
      'handleaction' => 'maincompany',
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
      16 => '1',
      'reviewer' => '1',
      'nostd' => 
      array (
      ),
    ),
  ),
);
?>