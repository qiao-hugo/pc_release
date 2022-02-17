<?php

/**
 * 生成查询语句
 * @author young.yang
 */
class KQueryGenerator
{
    private $module;

    private $conditionals;
    private $whereFields;
    /**
     *
     * @var VtigerCRMObjectMeta
     */
    private $meta;
    /**
     *
     * @var Users
     */
    private $user;
    private $fields;

    private $columns;
    private $fromClause;
    private $whereClause;
    private $groupClause;
    private $userwhere;
    private $groupWhere;
    private $searchwhere;
    public $query;

    private $groupFieldColumns;
    private $groupField;
    private $groupInfo;
    public $conditionInstanceCount;
    private $conditionalWhere;
    public static $AND = 'AND';
    public static $OR = 'OR';
    private $userdefultfields; //用户自定义的扩展字段
    private $userfields;    //用户自定义的字段，是完全替换拼接的
    public $leftjoin = '';
    /**
     * Import Feature
     */
    private $ignoreComma;

    public function __construct($user,$focus,$module)
    {


        $this->module = $module;    //模块下的module.php的实例化
        $this->focus = $focus;
        $this->user = $user;    //当前用户
        $this->fields = array();  //模块的字段

        $this->columns = null; //列表显示字段 默认跟用户自定义
        $this->fromClause = null; //from生成
        $this->whereClause = null;//条件生成=系统+自定义+搜索
        $this->groupClause = null;
        $this->query = null;
        $this->userdefultfields= null;
        $this->userfields = null;
    }

    public function reset()
    {
        $this->fromClause = null;
        $this->whereClause = null;

        $this->columns = null;
        $this->query = null;
    }
    //设置模块的全部字段，实例化使用
    public function setFields($fields)
    {
        $this->fields = $fields;
    }
    //返回字段,列表使用
    public function getFields()
    {
        return $this->fields;
    }

    public function getListFields(){
        return $this->listFields;
    }
    public function getWhere()
    {
        return $this->whereClause;
    }

    public function  getSearchWhere(){
        return $this->searchwhere;
    }

    /**
     * 用户自定义字段
     * @param $userdefultfields
     */
    public function addUserDefultFields($userdefultfields){
        $this->userdefultfields=$userdefultfields;
    }

    /**
     * 用户自定义字段，完全替换原来的
     * @param $userfields
     */
    public function addUserFields($userfields){
        $this->userfields=$userfields;
    }
    public function addGroupField($groupField){
        $this->groupField=$groupField;
    }
    public function addGroupFieldColumns($groupFieldColumns){
        $this->groupFieldColumns=$groupFieldColumns;
    }
    public function addGroupWhere($groupWhere){
        $this->groupWhere=$groupWhere;
    }

    public function addUserWhere($where)
    {
        if(empty($this->userwhere)){
            $this->userwhere = $where;
        }else{
            $this->userwhere = $this->userwhere.' '.$where;
        }

    }
    public function addSearchWhere($where){
        $this->searchwhere=$where;
    }
    public function getModule()
    {
        return $this->module;
    }
    public function getFocus(){
        return $this->focus;
    }
    public function getConditionalWhere()
    {
        return $this->conditionalWhere;
    }


    /**
     * 初始化列表显示的字段,针对日历，活动文档的特殊的module的处理,兼容老的erp，暂时不删除。
     * customview类，
     * 增加列表排序功能
     * @param unknown $viewId
     */
    public function initForCustomViewById($moduleName)
    {


    }


    public function getQuery()
    {
        if (empty($this->query)) {

            $query = "SELECT ";
            $query .= $this->getSelectClauseColumnSQL();
            $query .= $this->getFromClause();
            $query .= $this->getWhereClause();


            $this->query = $query;
            return $query;
        } else {
            return $this->query;
        }
    }
    public function getQueryGroup(){
        $query = "SELECT ";
        $query .= $this->groupFieldColumns;
        $query .= $this->getFromClause();
        $query .= $this->getWhereClause();
        $query .= $this->getGroupClause();
        $this->query = $query;
        return $query;
    }
    public function getQueryCount(){
        $this->getSelectClauseColumnSQL();
        $query = "SELECT ";
        $query .= ' count(1) as counts';
        $query .= $this->getFromClause();
        $query .= $this->getWhereClause();

        return $query;
    }
    /**
     * 获取字段，
     *  1.如果是当前表字段直接返回:表名.字段
     *  2.如果是非当前的表返回子查询，条件为(select 子表字段 from 子表名 where 子表名.主键=主表.字段) as 字段
     * 需要解决主表中的
     * @param $field
     * @return string
     */
    public function getSQLColumn($field)
    {

        $column=$field['columnname'];
        $baseTable = $this->focus->table_name;
        $baseTableIndex = $this->focus->table_index;
        if($field['uitype']==56){
            return "IF(".$field['tablename'].".".$column."=1,'是','否') as ".$column;
        }
        //2015-05-28 自定义字段
        if(!empty($field['reldefaultfield'])){
            return $field['reldefaultfield'];
        }
        if($field['fieldtype']=='reference'&&!empty($field['reltablename'])){
            if($field['uitype']==53){
                return '(select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',\'[\',usercode,\']\',(if(`status`=\'Active\' AND isdimission=0,\'\',\'[离职]\'))) as last_name from vtiger_users where '.$field['reltablename'].'.'.$field['reltablefield'].'=vtiger_users.id) as ' . $column;
            }else{
                if(!isset($this->leftjoin[$field['reltablename']])) {  //reltablefield 关联表主键  relentityidfield 原表主键 reltablecol关联表要显示的字段
                    if($field['listtabid']!=2){
                        if($field['relleftjoin']){  // 当需要关联另外一个leftjoin 的表使用
                            if($field['reltablename'] == 'vtiger_servicecomments'){
                                $this->leftjoin[$field['reltablename']] = " LEFT JOIN " . $field['reltablename'] . " ON  (vtiger_account.accountid = vtiger_servicecomments.related_to and vtiger_servicecomments.assigntype = 'accountby' and vtiger_servicecomments.related_to>0)";
                            }else{
                                $this->leftjoin[$field['reltablename']] = " LEFT JOIN " . $field['reltablename'] . " ON  " . $field['reltablename'] . "." . $field['reltablefield'] . "=" . $field['relleftjoin']."";
                            }

                        }else{
                            $this->leftjoin[$field['reltablename']] = ' LEFT JOIN ' . $field['reltablename'] . ' ON ' . $field['reltablename'] . '.' . $field['reltablefield'] . '=' . $field['tablename'] . '.' . $field['relentityidfield'];
                        }
                    }

                }
                if(in_array($field['uitype'],array(15,16))){   //下拉类型不产生关联
                    return ' ('.$field['reltablename'].'.'.$field['reltablecol'].') as '.$column.'';
                }
                if($field['listtabid']==2){
                    return ' ('.$field['tablename'].'.'.$column.'_name) as '.$column.','.$field['tablename'].'.'.$column.' as '.$column.'_'.$field['fieldtype'];
                }
                return ' ('.$field['reltablename'].'.'.$field['reltablecol'].') as '.$column.','.$field['tablename'].'.'.$column.' as '.$column.'_'.$field['fieldtype'];
                //
            }

        }
        //20150423 young 对于需要使用原值的，统一加入一个字段
        /*if($field['uitype']==110){
            //return "(select GROUP_CONCAT(label) from vtiger_crmentity where FIND_IN_SET(crmid,replace(".$field['tablename'] . '.' . $column.",' |##| ',','))) as ".$column;
            return "(select GROUP_CONCAT(label) from vtiger_crmentity where vtiger_crmentity.crmid in(replace(".$field['tablename'] . '.' . $column.",' |##| ',','))) as ".$column;
        }*/
        /*if($field['uitype']==54){
            return "(select GROUP_CONCAT(last_name) from vtiger_users where id in(replace(".$field['tablename'] . '.' . $column.",' |##| ',','))) as ".$column;
        }*/
        if($field['uitype']==73){
            return "(select GROUP_CONCAT(label) from vtiger_crmentity where vtiger_crmentity.crmid in(replace(".$field['tablename'] . '.' . $column.",' |##| ',','))) as ".$column;
        }

        if ($field['tablename'] == 'vtiger_crmentity') {
            if(in_array($column,array('smownerid','modifiedby','smcreatorid'))){
                return 'IFNULL((select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',\'[\',usercode,\']\',(if(`status`=\'Active\' AND isdimission=0,\'\',\'[离职]\'))) as last_name from vtiger_users where vtiger_crmentity.'.$column.'=vtiger_users.id),\'--\') as ' . $column. ',vtiger_crmentity.'.$column.' as '.$column.'_'.$field['fieldtype'];
            }else{
                //return '(select ' . $column . ' from vtiger_crmentity where vtiger_crmentity.crmid=' .$baseTable . '.' . $baseTableIndex . ' and vtiger_crmentity.deleted=0) as ' . $column;
                return 'vtiger_crmentity.'.$column;
            }

        }

        if($field['uitype']==53){
            return '(select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',\'[\',usercode,\']\',(if(`status`=\'Active\' AND isdimission=0,\'\',\'[离职]\'))) as last_name from vtiger_users where '.$baseTable.'.'.$column.'=vtiger_users.id) as ' . $column;
        }//end
        /*if($field['fieldtype']=='reference'&&$field['uitype']==10){
            return ' (select '.$field['nfieldname'].' from '.$field['ntablename'].' where '.$field['ntablename'].'.'.$field['entityidfield'].'='.$baseTable.'.'.$column.') as ' . $column . ','.$baseTable.'.'.$column.' as '.$column.'_'.$field['fieldtype'];
        }*/
        //20150430 young 解决51的关联问题
        if($field['uitype']==51){
            return ' IFNULL((select label from vtiger_crmentity where crmid='.$baseTable.'.'.$column.'),\'--\') as ' . $column;
        }//end

        if($field['uitype']==102 && $field['tablename'] == 'vtiger_knowledge'){
            return ' IF('.$baseTable.'.'.$column.' = \'\',IFNULL((select IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\') as departmentname from vtiger_users where vtiger_knowledge.author=vtiger_users.id),\'\'),IFNULL((select departmentname from vtiger_departments where departmentid = '.$baseTable.'.'.$column.'),\'\')) as '.$column;
        }

        if($field['tablename']==$baseTable){
            return $field['tablename'] . '.' . $column;
        }else{
            return $field['tablename'] . '.' . $column;
        }



    }

    /**
     *
     * @history
     *    2014-10-28 young 针对评论来做专门修改if($this->module=='ModComments')这一行为新增
     * @return Ambigous <NULL, string>
     */
    public function getSelectClauseColumnSQL()
    {
        if(!empty($this->userfields)){
            $this->columns = $this->userfields;
            return $this->userfields;
        }
        $columns = array();
        //默认字段$this->fields;当前模块.php文件中设置
        //系统字段，读取数据库，这里信息要全
       // $moduleFields=array('accountname'=>array());

        /*if(empty($this->fields)){
            $this->fields=$this->focus->list_fields_name;
        }*/

        $allListFields=$this->module->getListFields();
        //print_r($this->fields);die();
       // $listFields=array_values($this->fields);
        foreach ($allListFields as $val) {
            //if(in_array($val['columnname'],$listFields)){
                $sql = $this->getSQLColumn($val);
                $columns[] = $sql;
            //}
        }
        $this->columns = implode(',', $columns);
        if(empty($this->columns)){
            $this->columns='*';
        }
        if(!empty($this->userdefultfields)){
            $this->userdefultfields=trim(trim($this->userdefultfields),',');
            $this->columns = $this->columns.','.$this->userdefultfields;
        }
        return $this->columns.','.$this->focus->table_name.'.'.$this->focus->table_index;
    }

    /**
     * 在这里开始拼接语句了 from 之后的子句
     *  1.返回基础表，如果在配置中有其他的表则关联过来，有可能在简化@TODO
     * @return null|string
     */
    public function getFromClause()
    {
        if (!empty($this->query) || !empty($this->fromClause)) {
            return $this->fromClause;
        }

        $baseTable = $this->focus->table_name;//获取基础表
        $baseTableIndex = $this->focus->table_index;
        $sql = " FROM $baseTable ";
        $defaultTableList = $this->focus->tab_name_index;//关联表，是存储多个数据的地方
        if(!empty($defaultTableList)){
            foreach ($defaultTableList as $tableName=>$refield) {
                if($tableName!=$baseTable){

                    $sql .= " LEFT JOIN $tableName ON $baseTable." .
                        "$baseTableIndex = $tableName.$refield";
                }

            }
        }
        $leftjoins=$this->leftjoin;
        if(!empty($leftjoins)){
            foreach($leftjoins as $leftjoin => $val){
                $sql .= $val.' ';
            }
        }

        /*$relate_table = $this->focus->relate_table;//关联表，是存储多个数据的地方
        if(!empty($relate_table)){
            foreach($relate_table as  $table=>$field){
                $sql .= " LEFT JOIN vtiger_account ON vtiger_account.accountid=$table.$field ";
            }
        }*/
        $this->fromClause = $sql;
        return $sql;
    }

    /**
     * 条件生成包括自定义
     * @return string
     */
    public function getWhereClause()
    {
        $where=' WHERE 1=1';
        //1.系统 比如crmentity 中deleted=0，自定义表中也有
        $tab_name = $this->focus->tab_name;//获取基础表
        $baseTable = $this->focus->table_name;//获取基础表
        if(in_array(TABLEPIX.'crmentity',$tab_name)){
            $where.=' and '.TABLEPIX.'crmentity.deleted=0 ';
        }else{
            $moduleFields =  Vtiger_Cache::get('fieldInfo', $this->module->getId());
            if(!empty($moduleFields['deleted'])){
                $where.=' and '.$baseTable.'.deleted=0 ';
            }
        }
        //2.用户 比如用户中的条件，是不可修改的,是系统约定
        if($this->userwhere){
            $where.='  '.$this->userwhere;
        }
        //3.检索 通过前台提交过来的
        if($this->searchwhere){
            $where.=' and ( '.$this->searchwhere.' ) ';
        }
        //4.分组 分组的条件
        if($this->groupWhere){
            $where.='  '.$this->groupWhere;
        }
        $this->whereClause=$where;
        return $where;
    }
    public function getGroupClause(){
        $this->groupClause=' GROUP BY '.$this->groupField;
        return $this->groupClause;
    }

    /**
     * 获取查询条件值
     * @param mixed $value
     * @param String $operator
     * @param WebserviceField $field
     */
    private function getConditionValue($value, $operator, $field)
    {

        $operator = strtolower($operator);
        $db = PearDatabase::getInstance();

        if (is_string($value) && $this->ignoreComma == false) {
            $valueArray = explode(',', $value);
            if (($field->getFieldDataType() == 'multipicklist' || $field->getUIType() == 54) && in_array($operator, array('e', 'n'))) {
                $valueArray = getCombinations($valueArray);

                foreach ($valueArray as $key => $value) {
                    $valueArray[$key] = ltrim($value, ' |##| ');
                }
            }
        } elseif (is_array($value)) {
            $valueArray = $value;
        } else {
            $valueArray = array($value);
        }
        $sql = array();
        if ($operator == 'between' || $operator == 'bw' || $operator == 'notequal') {
            if ($field->getFieldName() == 'birthday') {
                $valueArray[0] = getValidDBInsertDateTimeValue($valueArray[0]);
                $valueArray[1] = getValidDBInsertDateTimeValue($valueArray[1]);
                $sql[] = "BETWEEN DATE_FORMAT(" . $db->quote($valueArray[0]) . ", '%m%d') AND " .
                    "DATE_FORMAT(" . $db->quote($valueArray[1]) . ", '%m%d')";
            } else {
                if ($this->isDateType($field->getFieldDataType())) {
                    $valueArray[0] = getValidDBInsertDateTimeValue($valueArray[0]);
                    $dateTimeStart = explode(' ', $valueArray[0]);
                    if ($dateTimeStart[1] == '00:00:00' && $operator != 'between') {
                        $valueArray[0] = $dateTimeStart[0];
                    }
                    $valueArray[1] = getValidDBInsertDateTimeValue($valueArray[1]);
                    $dateTimeEnd = explode(' ', $valueArray[1]);
                    if ($dateTimeEnd[1] == '00:00:00') {
                        $valueArray[1] = $dateTimeEnd[0];
                    }
                }

                if ($operator == 'notequal') {
                    $sql[] = "NOT BETWEEN " . $db->quote($valueArray[0]) . " AND " .
                        $db->quote($valueArray[1]);
                } else {
                    $sql[] = "BETWEEN " . $db->quote($valueArray[0]) . " AND " .
                        $db->quote($valueArray[1]);
                }
            }
            return $sql;
        }
        foreach ($valueArray as $value) {
            if (!$this->isStringType($field->getFieldDataType())) {
                $value = trim($value);
            }
            if ($operator == 'empty' || $operator == 'y') {
                $sql[] = sprintf("IS NULL OR %s = ''", $this->getSQLColumn($field->getFieldName()));
                continue;
            }
            if ((strtolower(trim($value)) == 'null') ||
                (trim($value) == '' && !$this->isStringType($field->getFieldDataType())) &&
                ($operator == 'e' || $operator == 'n')
            ) {
                if ($operator == 'e') {
                    $sql[] = "IS NULL";
                    continue;
                }
                $sql[] = "IS NOT NULL";
                continue;
            } elseif ($field->getFieldDataType() == 'boolean') {
                $value = strtolower($value);
                if ($value == 'yes') {
                    $value = 1;
                } elseif ($value == 'no') {
                    $value = 0;
                }
            } elseif ($this->isDateType($field->getFieldDataType())) {
                $value = getValidDBInsertDateTimeValue($value);
                $dateTime = explode(' ', $value);
                if ($dateTime[1] == '00:00:00') {
                    $value = $dateTime[0];
                }
            }

            if ($field->getFieldName() == 'birthday' && !$this->isRelativeSearchOperators(
                    $operator)
            ) {
                $value = "DATE_FORMAT(" . $db->quote($value) . ", '%m%d')";
            } else {
                $value = $db->sql_escape_string($value);
            }

            if (trim($value) == '' && ($operator == 's' || $operator == 'ew' || $operator == 'c')
                && ($this->isStringType($field->getFieldDataType()) ||
                    $field->getFieldDataType() == 'picklist' ||
                    $field->getFieldDataType() == 'multipicklist')
            ) {
                $sql[] = "LIKE ''";
                continue;
            }

            if (trim($value) == '' && ($operator == 'k') &&
                $this->isStringType($field->getFieldDataType())
            ) {
                $sql[] = "NOT LIKE ''";
                continue;
            }

            switch ($operator) {
                case 'e':
                    $sqlOperator = "=";
                    break;
                case 'n':
                    $sqlOperator = "<>";
                    break;
                case 's':
                    $sqlOperator = "LIKE";
                    $value = "$value%";
                    break;
                case 'ew':
                    $sqlOperator = "LIKE";
                    $value = "%$value";
                    break;
                case 'c':
                    $sqlOperator = "LIKE";
                    $value = "%$value%";
                    break;
                case 'k':
                    $sqlOperator = "NOT LIKE";
                    $value = "%$value%";
                    break;
                case 'l':
                    $sqlOperator = "<";
                    break;
                case 'g':
                    $sqlOperator = ">";
                    break;
                case 'm':
                    $sqlOperator = "<=";
                    break;
                case 'h':
                    $sqlOperator = ">=";
                    break;
                case 'a':
                    $sqlOperator = ">";
                    break;
                case 'b':
                    $sqlOperator = "<";
                    break;
            }
            if (!$this->isNumericType($field->getFieldDataType()) &&
                ($field->getFieldName() != 'birthday' || ($field->getFieldName() == 'birthday'
                        && $this->isRelativeSearchOperators($operator)))
            ) {
                $value = "'$value'";
            }
            if ($this->isNumericType($field->getFieldDataType()) && empty($value)) {
                $value = '0';
            }
            $sql[] = "$sqlOperator $value";
        }
        return $sql;
    }




    //加入用户搜索条件
    public function addUserSearchConditions($input)
    {
        global $default_charset;

        //$allListFields=$this->module->getListFields();

        $uitype=0;
        $fieldid = 0;
        $fieldtype ='string';
        //搜索字段
        if (isset($input['search_field']) && $input['search_field'] != "") {
            $fieldName = vtlib_purify($input['search_field']);
            if(strpos($fieldName,'##')!==false){
                $temp = explode('##',$fieldName);
                $fieldName = $temp[0];
                $uitype = $temp[1];
                $fieldid=$temp[2];
                $fieldtype=$temp[3];
            }

        } else {
            return;
        }
        $counts=empty($input['counts'])?0:$input['counts'];

        //搜索内容
        if (isset($input['search_text']) && $input['search_text'] != "") {
            // search other characters like "|, ?, ?" by jagi
            $value = $input['search_text'];
			$value = vtlib_purify($value);   //加入html过滤过滤
			 $value=trim($value);
			$value=str_replace(array(')','#','\'','%'),'',$value);
           // $stringConvert = $value;
        }else if(empty($input['andor']) && $counts<=3){
            //加上多条件时报错,加
            return;
        }


       
		
		
        if (!empty($input['operator'])) {
            $operator = $input['operator'];
        } else {
            //$operator = sprintf("IS NULL OR %s = ''", $this->getSQLColumn($field->getFieldName()));
            $operator = 'LIKE';
        }

        if(strpos($value,'##')!==false){
            $counts=explode('##',$value);
            if(count($counts)==2){
                $value=$counts[1];
            }else{
                $value = '0';
            }
        }
        //特殊条件
        switch ($operator) {
            case 'LIKE':
                $value = "%$value%";
                break;
            case 'NOT LIKE':
                $value = "%$value%";
                break;
            case 'IN':
                $value = "('$value')";
                break;
        }

        //括号
        $leftkh=$input['leftkh'];
        $rightkh=$input['rightkh'];
        $andor=$input['andor'];

        //客户id，filedname 跟columnname不一样，造成问题
        //bug#8758 检索项目性别，输入条件【男】后点击提交查询按钮，检索结果出来男女都有，且检索条件被清空
        //bug#8760
        if($fieldName=='assigned_user_id'){
            $fieldName='smownerid';
        }
        if($fieldName=='account_id'){
            $fieldName='accountid';
        }
        if($fieldName=='gendertype'){
            $fieldName='gender';
        }
        if($fieldName=='makedecisiontype'){
            $fieldName='makedecision';
        }
        //end
		if($uitype==70){
            if($operator == '>='){
                $value = $value.' 00:00:00';
            }
            if($operator == '<='){
                $value = $value.' 23:59:59';
            }
        }
        if(!is_numeric($value)){
            $value = '\''.$value.'\'';
        }
		

        if((strtolower(trim($value)) == 'null') ||($value == '')||($value == '\'\'')){
            $sql = ' ('.$fieldName.' IS NULL OR '.$fieldName.'=\'\')';
        }else{
            $sql = ' ('.$fieldName.' '.$operator.' '.$value.' AND '.$fieldName.' IS NOT NULL)';
        }

        if($uitype==54){
            $sql=' FIND_IN_SET('.$value.',REPLACE('.$fieldName.',\' |##| \',\',\')) ';
        }
        if(empty($this->searchwhere)){
			$this->searchwhere=' '.$leftkh.$sql.$rightkh.' '.$andor;
        }else{
            $this->searchwhere.=' '.$leftkh.$sql.$rightkh.' '.$andor;
        }

    }


}

?>