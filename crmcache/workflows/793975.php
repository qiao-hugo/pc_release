<?php

$workflows=array (
  0 => '793975',
  'workflowsid' => '793975',
  1 => '采购合同审核工作流',
  'workflowsname' => '采购合同审核工作流',
  2 => '0',
  'iscontract' => '0',
  3 => '0',
  'iscontent' => '0',
  4 => '0',
  'isattachment' => '0',
  5 => '0',
  'ischargeback' => '0',
  6 => 'SupplierContracts',
  'mountmodule' => 'SupplierContracts',
  'stage' => 
  array (
    1 => 
    array (
      0 => '796133',
      'workflowstagesid' => '796133',
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
      8 => '793975',
      'workflowsid' => '793975',
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
      'nostd' => 
      array (
      ),
    ),
    2 => 
    array (
      0 => '796134',
      'workflowstagesid' => '796134',
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
      8 => '793975',
      'workflowsid' => '793975',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_actioning',
      'modulestatus' => 'b_actioning',
      14 => '0',
      'isnextnode' => '0',
      15 => 'TWO_VERIFICATION',
      'workflowstagesflag' => 'TWO_VERIFICATION',
      'nostd' => 
      array (
      ),
    ),
    3 => 
    array (
      0 => '796135',
      'workflowstagesid' => '796135',
      1 => '第三级审核',
      'workflowstagesname' => '第三级审核',
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
      8 => '793975',
      'workflowsid' => '793975',
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
      'nostd' => 
      array (
      ),
    ),
    4 => 
    array (
      0 => '796136',
      'workflowstagesid' => '796136',
      1 => '财务主管审核',
      'workflowstagesname' => '财务主管审核',
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
      8 => '793975',
      'workflowsid' => '793975',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_actioning',
      'modulestatus' => 'b_actioning',
      14 => '0',
      'isnextnode' => '0',
      15 => 'CREATE_CODE',
      'workflowstagesflag' => 'CREATE_CODE',
      'nostd' => 
      array (
      ),
    ),
    5 => 
    array (
      0 => '796137',
      'workflowstagesid' => '796137',
      1 => '合同打印',
      'workflowstagesname' => '合同打印',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H104 |##| H90',
      'isrole' => 'H104 |##| H90',
      4 => '0',
      'iscost' => '0',
      5 => '5',
      'sequence' => '5',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '793975',
      'workflowsid' => '793975',
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
      'nostd' => 
      array (
      ),
    ),
    6 => 
    array (
      0 => '796138',
      'workflowstagesid' => '796138',
      1 => '合同盖章',
      'workflowstagesname' => '合同盖章',
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
      8 => '793975',
      'workflowsid' => '793975',
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
      15 => 'CLOSE_WORKSTREAM',
      'workflowstagesflag' => 'CLOSE_WORKSTREAM',
      'nostd' => 
      array (
      ),
    ),
    7 => 
    array (
      0 => '796139',
      'workflowstagesid' => '796139',
      1 => '合同领取',
      'workflowstagesname' => '合同领取',
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
      8 => '793975',
      'workflowsid' => '793975',
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
      15 => 'CREATE_SIGN_ONE',
      'workflowstagesflag' => 'CREATE_SIGN_ONE',
      'nostd' => 
      array (
      ),
    ),
    8 => 
    array (
      0 => '796140',
      'workflowstagesid' => '796140',
      1 => '合同收回',
      'workflowstagesname' => '合同收回',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H104 |##| H90',
      'isrole' => 'H104 |##| H90',
      4 => '0',
      'iscost' => '0',
      5 => '8',
      'sequence' => '8',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '793975',
      'workflowsid' => '793975',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_actioning',
      'modulestatus' => 'b_actioning',
      14 => '0',
      'isnextnode' => '0',
      15 => 'CREATE_SIGN_TWO',
      'workflowstagesflag' => 'CREATE_SIGN_TWO',
      'nostd' => 
      array (
      ),
    ),
  ),
);
?>