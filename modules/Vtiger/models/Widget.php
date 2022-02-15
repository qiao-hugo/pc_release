<?php
/**
 * 挂件
 */
class Vtiger_Widget_Model extends Vtiger_Base_Model {
	
	//挂件位置
	public function getWidth() {
		$largerSizedWidgets = array('GroupedBySalesPerson', 'PipelinedAmountPerSalesPerson', 'GroupedBySalesStage', 'Funnel Amount');
		$title = $this->getName();
		if(in_array($title, $largerSizedWidgets)) {
			$this->set('width', '6');
		}
		$width = $this->get('width');
		if(empty($width)) {
			$this->set('width', '4');
		}
		return $this->get('width');
	}
	public function getHeight() {
		//Special case for History widget
		$title = $this->getTitle();
		if($title == 'History') {
			$this->set('height', '2');
		}
		$height = $this->get('height');
		if(empty($height)) {
			$this->set('height', '1');
		}
		return $this->get('height');
	}
	public function getPositionCol($default=0) {
		$position = $this->get('position');
		if ($position) {
			$position = Zend_Json::decode(decode_html($position));
			return intval($position['col']);
		}
		return $default;
	}
	public function getPositionRow($default=0) {
		$position = $this->get('position');
		if ($position) {
			$position = Zend_Json::decode(decode_html($position));
			return intval($position['row']);
		}
		return $default;
	}

	/**
	 * 获取挂件链接 //挂件标题 没有Title的使用linklabel//以及名称正则匹配name
	 */
	public function getUrl() {
		$url = decode_html($this->get('linkurl')).'&linkid='.$this->get('linkid');
		$widgetid = $this->has('widgetid')? $this->get('widgetid') : $this->get('id');
		if ($widgetid) $url .= '&widgetid=' . $widgetid;
		//Vtiger_Request的__construct方法过滤条件参数追加 gaocl/2014-12-29 add start
		$url.='&checkflg=1';
		//Vtiger_Request的__construct方法过滤条件参数追加 gaocl/2014-12-29 add end
		return $url;
	}
	public function getTitle() {
		$title = $this->get('title');
		if(!isset($title)) {
			$title = $this->get('linklabel');
		}
		return $title;
	}
	public function getName() {
		$widgetName = $this->get('name');
		if(empty($widgetName)){
			//since the html entitites will be encoded
			//转来转去有卵用
			//TODO : See if you need to push decode_html to base model
			$linkUrl = $this->getUrl();
			//$linkUrl = decode_html($this->getUrl());
			preg_match('/name=[a-zA-Z]+/', $linkUrl, $matches);
			$matches = explode('=', $matches[0]);
			$widgetName = $matches[1];
			$this->set('name', $widgetName);
		}
		return $widgetName;
	}
	
	/**
	 * 初始化
	 */
	public static function getInstanceFromValues($valueMap) {
		$self = new self();
		$self->setData($valueMap);
		return $self;
	}
	//获取用户首页挂件 Link表
	public static function getInstance($linkId, $userId) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT * FROM vtiger_module_dashboard_widgets
			INNER JOIN vtiger_links ON vtiger_links.linkid = vtiger_module_dashboard_widgets.linkid
			WHERE linktype = "DASHBOARDWIDGET" AND vtiger_links.linkid = ? AND userid = ?', array($linkId, $userId));
		$self = new self();
		if($db->num_rows($result)) {
			$row = $db->query_result_rowdata($result, 0);
			$self->setData($row);
		}
		return $self;
	}
	public static function getInstanceWithWidgetId($widgetId, $userId) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT * FROM vtiger_module_dashboard_widgets
			INNER JOIN vtiger_links ON vtiger_links.linkid = vtiger_module_dashboard_widgets.linkid
			WHERE linktype = "DASHBOARDWIDGET" AND vtiger_module_dashboard_widgets.id = ? AND userid = ?', array($widgetId, $userId));
		$self = new self();
		if($db->num_rows($result)) {
			$row = $db->query_result_rowdata($result, 0);
			$self->setData($row);
		}
		return $self;
	}

	//位置排序
	public static function updateWidgetPosition($position, $linkId, $widgetId, $userId) {
		if (!$linkId && !$widgetId) return;
			$db = PearDatabase::getInstance();
			$sql = 'UPDATE vtiger_module_dashboard_widgets SET position=? WHERE userid=?';
			$params = array($position, $userId);
			if ($linkId) {
				$sql .= ' AND linkid = ?';
				$params[] = $linkId;
			} else if ($widgetId) {
				$sql .= ' AND id = ?';
				$params[] = $widgetId;
			}
		$db->pquery($sql, $params);
	}

	/**
	 * 用户挂件显示/关闭
	 */
	public function add() {
		$db = PearDatabase::getInstance();
		$sql = 'SELECT id FROM vtiger_module_dashboard_widgets WHERE linkid = ? AND userid = ?';
		$params = array($this->get('linkid'), $this->get('userid'));
		$filterid = $this->get('filterid');
		if (!empty($filterid)) {
			$sql .= ' AND filterid = ?';
			$params[] = $this->get('filterid');
		}
		$result = $db->pquery($sql, $params);
		if(!$db->num_rows($result)) {
			$db->pquery('INSERT INTO vtiger_module_dashboard_widgets(linkid, userid, filterid, title, data) VALUES(?,?,?,?,?)',
					array($this->get('linkid'), $this->get('userid'), $this->get('filterid'), $this->get('title'), Zend_Json::encode($this->get('data'))));
			$this->set('id', $db->getLastInsertID());
		} else {
			$this->set('id', $db->query_result($result, 0, 'id'));
		}
	}
	public function remove() {
		$db = PearDatabase::getInstance();
		$db->pquery('DELETE FROM vtiger_module_dashboard_widgets WHERE id = ? AND userid = ?',
				array($this->get('id'), $this->get('userid')));
	}
	public function getDeleteUrl() {
		$url = 'index.php?module=Vtiger&action=RemoveWidget&linkid='. $this->get('linkid');
		$widgetid = $this->has('widgetid')? $this->get('widgetid') : $this->get('id');
		if ($widgetid) $url .= '&widgetid=' . $widgetid;
		return $url;
	}

	/**
	 * 默认挂件
	 */
	public function isDefault() {
		$defaultWidgets = $this->getDefaultWidgets();
		$widgetName = $this->getName();
		if (in_array($widgetName, $defaultWidgets)) {
			return true;
		}
		return false;
	}
	public function getDefaultWidgets() {
		return array();
	}
}