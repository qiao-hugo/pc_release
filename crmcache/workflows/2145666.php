<?php

$workflows=array (
  0 => '2145666',
  'workflowsid' => '2145666',
  1 => '媒体外采充值【后付款】审核流程【有垫款】',
  'workflowsname' => '媒体外采充值【后付款】审核流程【有垫款】',
  2 => '0',
  'iscontract' => '0',
  3 => '0',
  'iscontent' => '0',
  4 => '0',
  'isattachment' => '0',
  5 => '0',
  'ischargeback' => '0',
  6 => 'RefillApplication',
  'mountmodule' => 'RefillApplication',
  7 => NULL,
  'dataupdate' => NULL,
  'stage' => 
  array (
    1 => 
    array (
      0 => '2145674',
      'workflowstagesid' => '2145674',
      1 => '分公司媒介审核',
      'workflowstagesname' => '分公司媒介审核',
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
      8 => '2145666',
      'workflowsid' => '2145666',
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
      15 => 'COMPANY_MEDIA',
      'workflowstagesflag' => 'COMPANY_MEDIA',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    2 => 
    array (
      0 => '2145695',
      'workflowstagesid' => '2145695',
      1 => '媒介负责人审核',
      'workflowstagesname' => '媒介负责人审核',
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
      8 => '2145666',
      'workflowsid' => '2145666',
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
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    3 => 
    array (
      0 => '2145696',
      'workflowstagesid' => '2145696',
      1 => '部门负责人一级审核',
      'workflowstagesname' => '部门负责人一级审核',
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
      8 => '2145666',
      'workflowsid' => '2145666',
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
    4 => 
    array (
      0 => '2149002',
      'workflowstagesid' => '2149002',
      1 => '部门负责人二级审核',
      'workflowstagesname' => '部门负责人二级审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '4',
      'sequence' => '4',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2145666',
      'workflowsid' => '2145666',
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
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    5 => 
    array (
      0 => '2145698',
      'workflowstagesid' => '2145698',
      1 => '责任会计审核',
      'workflowstagesname' => '责任会计审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H121 |##| H127 |##| H90 |##| H171',
      'isrole' => 'H121 |##| H127 |##| H90 |##| H171',
      4 => '0',
      'iscost' => '0',
      5 => '5',
      'sequence' => '5',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2145666',
      'workflowsid' => '2145666',
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
      15 => 'GUARANTY_NODE',
      'workflowstagesflag' => 'GUARANTY_NODE',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    6 => 
    array (
      0 => '2145699',
      'workflowstagesid' => '2145699',
      1 => '财务运营经理审核',
      'workflowstagesname' => '财务运营经理审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H89',
      'isrole' => 'H89',
      4 => '0',
      'iscost' => '0',
      5 => '6',
      'sequence' => '6',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2145666',
      'workflowsid' => '2145666',
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
    7 => 
    array (
      0 => '2145702',
      'workflowstagesid' => '2145702',
      1 => '提单人确认',
      'workflowstagesname' => '提单人确认',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '7',
      'sequence' => '7',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2145666',
      'workflowsid' => '2145666',
      9 => '0',
      'timelimit' => '0',
      10 => 'MyCheck',
      'handleaction' => 'MyCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'c_receive',
      'modulestatus' => 'c_receive',
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
  ),
);
?>