<?php
/*+************
 * 数据记录模型 用于新增编辑
 ***************/
class Schoolresume_Record_Model extends Vtiger_Record_Model {

    static public function getSchoolresumeInfo($record) {
        $schoolresumeModel = Vtiger_Record_Model::getInstanceById($record, 'Schoolresume');
        $schoolResumeData = array(
            'name'=>$schoolresumeModel->entity->column_fields['name'],
            'gendertype'=>$schoolresumeModel->entity->column_fields['gendertype'],
            'email'=>$schoolresumeModel->entity->column_fields['email'],
            'telephone'=>$schoolresumeModel->entity->column_fields['telephone'],
            );
        $schoolResumeData['gendertype'] = $schoolResumeData['gendertype']=='MALE' ? '男' : '女';
        return $schoolResumeData;
    }
    /**
     * 发送邮件
     * @param Vtiger_Request $request
     */
    public function sendemail($mailer,$content){
        $mailer->ClearAttachments();//清除附件或图片
        /*$content['email'];//收件人的邮箱地址
        $content['sendname'];//收件人的名字
        $content['subject'];//邮件主题
        $content['body'];//邮件内容
        $content['filepath'];//附件的绝对路径
        $content['filename'];//附件的名称此处以 收件人名字+.docx
        */
        $content['email']=trim($content['email']);
        $msg=false;
        if($this->checkEmails($content['email'])){
            $mailer->ClearAddresses();//清除上一次发件人邮件地址信息
            $mailer->AddAddress($content['email'], $content['sendname']);//收件人的地址\
            $mailer->AddAttachment($content['filepath'],$content['filename']);
            $mailer->WordWrap = 100;
            $mailer->IsHTML(true);

            $mailer->Subject = $content['subject'];

            $mailer->Body =  $content['body'];
            $mailer->AltBody = '无法显示邮件';//不去持HTML时显示
            $email_flag=$mailer->Send()?'send':'fail';
            $msg=$email_flag=='fail'?false:true;
        }
        return $msg;

    }
    public function checkEmails($str){
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        //$regex = '/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }

    /**
     * 发件邮件的配置
     * @param $config
     * @return PHPMailer
     */
    public function configEmailServer($config){
        global $adb;
        $arr=array();
        $query = "SELECT * FROM `vtiger_systems` WHERE server_type='email'  AND id=?";
        $result = $adb->pquery($query, array($config['server_id']));
        if (!$adb->num_rows($result)) {
            $response = new Vtiger_Response();
            $response->setResult(array('服务器配置错误'));
            $response->emit();
            exit;
        }
        $result = $adb->query_result_rowdata($result);
        $result['from_email_field'] = $result['from_email_field'] != '' ? $result['from_email_field'] : $result['server_username'];

        global $root_directory;
        require_once $root_directory.'modules/Emails/class.phpmailer.php';
        $mailer=new PHPMailer();
        $mailer->IsSmtp();
        //$mailer->SMTPDebug = true;
        $mailer->SMTPAuth=$result['smtp_auth'];
        $mailer->Host=$result['server'];
        //$mailer->Host='smtp.qq.com';
        $mailer->SMTPSecure = "SSL";
        //$mailer->Port = $result['server_port'];
        $mailer->Username = $result['server_username'];//用户名
        $mailer->Password = $result['server_password'];//密码
        $mailer->From = $result['from_email_field'];//发件人
        $mailer->FromName = $config['server_name'];
        return $mailer;
    }
}
