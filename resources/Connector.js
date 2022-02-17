/*+***********************************************************************************
 * 异步请求类
 * @author young.yang 2015-05-27
 * @copyright CRM
 *************************************************************************************/
var AppConnector = {

	/**
	 * 发送一个 pjax请求 (push state +ajax)
	 *  Success - 请求成功返回数据集
	 *  error - 错误状态，抛出错误存在对象
	 *  @return - deferred promise
	 */
	requestPjax : function(params) {
		return AppConnector._request(params, true);
	},
    /**
     * 列表页面异步请求
     * @param params
     * @returns {*}
     */
    requestPjaxPost : function(params){
        var aDeferred = jQuery.Deferred();
        if(typeof params == 'undefined') params = {};
        //caller has send only data
        if(typeof params.data == 'undefined') {
            if(typeof params == 'string') {
                var callerParams = params;
                if(callerParams.indexOf('?')!== -1) {
                    var callerParamsParts = callerParams.split('?')
                    callerParams = callerParamsParts[1];
                }
            }else{
                callerParams = jQuery.extend({}, params);
            }
            params = {};

            params.data = callerParams;
        }
        var success = function(data,status,jqXHR) {
            aDeferred.resolve(data);
        }
        var error = function(jqXHR, textStatus, errorThrown){
            aDeferred.reject(textStatus, errorThrown);
        }
        if(typeof params.container == 'undefined') params.container = '#pjaxContainer';

        if(navigator.userAgent.indexOf("MSIE")>0)
        {
            if(navigator.userAgent.indexOf("MSIE 9.0")>0)
            {
                params.type = 'GET';
            }
        }
        if(typeof params.type == 'undefined') params.type = 'POST';
        var pjaxContainer = jQuery('#pjaxContainer');

        // young.yang 20150512 pjax不兼容，修改成ajax提交
        params.success = success;
        params.error = error;
        params.dataType = 'html';
        $.ajax(params);
        //jQuery.pjax(params);
        return aDeferred.promise();
    },
	/**
	 *  发送ajax请求.
	 *  Success - 返回获取到的数据
     *  error - 错误状态，抛出错误存在对象
	 *  @return - deferred promise
	 */
	request : function(params) {
		return AppConnector._request(params, false);
	},

    /**
     * 请求方法
     * @param params 参数
     * @param pjaxMode true = pjax,false=ajax
     * @returns {*}
     * @private
     */
	_request : function(params, pjaxMode) {
		var aDeferred = jQuery.Deferred();

		if(typeof pjaxMode == 'undefined') {
			pjaxMode = false;
		}
		if(typeof params == 'undefined') params = {};
		//caller has send only data
		if(typeof params.data == 'undefined') {
			if(typeof params == 'string') {
				var callerParams = params;
				if(callerParams.indexOf('?')!== -1) {
					var callerParamsParts = callerParams.split('?')
					callerParams = callerParamsParts[1];
				}
			}else{
				callerParams = jQuery.extend({}, params);
			}
			params = {};
			
			params.data = callerParams;
		}
		//Make the request as post by default
		if(typeof params.type == 'undefined') params.type = 'POST';

		//By default we expect json from the server
		if(typeof params.dataType == 'undefined'){
			var data = params.data;
			//view will return html
			params.dataType='json';
			if(data.hasOwnProperty('view')){
				params.dataType='html';
			}
			else if (typeof data == 'string' && data.indexOf('&view=') !== -1) {
				params.dataType='html';
			}
			
			if(typeof params.url != 'undefined' && params.url.indexOf('&view=')!== -1) {
				params.dataType='html';
			}
		}
		//If url contains params then seperate them and make them as data
		if(typeof params.url != 'undefined' && params.url.indexOf('?')!== -1) {
			var urlSplit = params.url.split('?');
			var queryString = urlSplit[1];
			params.url = urlSplit[0];
			var queryParameters = queryString.split('&');
			for(var index=0; index<queryParameters.length; index++) {
				var queryParam = queryParameters[index];
				var queryParamComponents = queryParam.split('=');
				params.data[queryParamComponents[0]] = queryParamComponents[1];
			}
		}

		if(typeof params.url == 'undefined' ||  params.url.length <= 0){
			 params.url = 'index.php';
		}

		var success = function(data,status,jqXHR) {
			aDeferred.resolve(data);
		}

		var error = function(jqXHR, textStatus, errorThrown){
			aDeferred.reject(textStatus, errorThrown);
		}
		
		if(pjaxMode) {
			if(typeof params.container == 'undefined') params.container = '#pjaxContainer';

			params.type = 'GET';

			var pjaxContainer = jQuery('#pjaxContainer');
			//Clear contents existing before
			if(params.container == '#pjaxContainer') {
				pjaxContainer.html('');
			}

			jQuery(document).on('pjax:success', function(event, data,status,jqXHR){
				pjaxContainer.html('');
				success(data,status,jqXHR);
			})
			
			jQuery(document).on('pjax:error', function(event, jqXHR, textStatus, errorThrown){
				pjaxContainer.html('');
				error(jqXHR, textStatus, errorThrown);
			})
			jQuery.pjax(params);

		}else{
			params.success = success;
			params.error = error;
			
			//console.log(params);
			jQuery.ajax(params);
		}
		return aDeferred.promise();
	}

}

