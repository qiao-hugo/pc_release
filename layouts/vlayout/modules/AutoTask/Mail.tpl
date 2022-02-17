<table class="table table-bordered ">
<tr><td>主题</td><td>{$MAIL_DETAIL['subject']}</td><td>附件</td><td>{$MAIL_DETAIL['file']}</td></tr>
<tr><td>收件人</td><td>{$MAIL_DETAIL['to_email']}</td><td>自定义收件邮箱</td><td>{$MAIL_DETAIL['custom_rece']}</td></tr>
<tr><td>抄送人</td><td>{$MAIL_DETAIL['cc_email']}</td><td>自定义抄送邮箱</td><td>{$MAIL_DETAIL['custom_copy']}</td></tr>
<tr><td>邮件内容</td><td colspan="3"><textarea id="email_ID" readonly="readonly" class="productnote" name="editorValue">{$MAIL_DETAIL['body']}</textarea></td></tr>
</table>
