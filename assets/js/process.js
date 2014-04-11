var ogb_be_url = 'admin.php?page=pipes.pipe&task=';
var ogb_need_save = false;
function showHelps(el, pid) {
	var pdiv = document.getElementById('ob-param-help-' + pid);
	if (pdiv.style.display == 'none') {
		pdiv.style.display = 'block';
		pdiv.style.marginLeft = '0';
		if (!phload[pid]) {
			ogb_loadAddonParam('ob-param-help-' + pid, 'processorhelp', '', pid);
			phload[pid] = true;
		}
	} else {
		pdiv.style.display = 'none';
	}
}
function showParams(el, pid) {
	var pdiv = document.getElementById('ob-param-' + pid);
	if (pdiv.style.display == 'none') {
		pdiv.style.display = 'block';
		pdiv.style.marginLeft = '0';
		if (!pload[pid]) {
			ogb_loadAddonParam('ob-param-' + pid, 'processor', '', pid);
			pload[pid] = true;
		}
	} else {
		pdiv.style.display = 'none';
	}
	// change the icon
	var a = el.getElementsByTagName('i')[0];
	if (a.className == 'fa fa-expand') {
		a.className = 'fa fa-compress';
	} else {
		a.className = 'fa fa-expand';
	}
}
function obgid(id) {
	return document.getElementById(id);
}
function ogb_update_ops() {
	var ops = ogb_ops.op;
	updateOengine(ogb_ops.oe);
	for (var i = 0; i < ops.length; i++) {
		updateOprocessor(ops[i], i);
	}
}
function ogb_update_ips() {
	if (!ogb_ips) {
		return;
	}
	var ips = ogb_ips.ip;
	var ia = ogb_ips.ia;
	var litxt = '';
	updateIadapter(ia);
	for (var i = 0; i < ips.length; i++) {
		updateIprocessor(ips[i], i);
	}
	ogb_update_ops();
}
function ogb_update_field(st, of) {
	if (ogb_unable_get(st)) {
		alert('disable');
		return;
	}
	el = ogb_change_field;
	if (ogb_change_field == null) {
		alert('ogb_change_field None');
		return;
	}
	var ip = el.parentNode.getElementsByTagName('input')[0];
	var ipf = ip.value.split(',');
	if (st == '') {
		ip.value = ',,' + ipf[2];
		el.innerHTML = 'Click me';
	} else {
		ip.value = st + ',' + of + ',' + ipf[2];
		el.innerHTML = (st == 'e' ? '[so]' : 'op[' + st + ']') + ' ' + of;
	}
	if (el.parentNode.parentNode.parentNode.parentNode.className == 'list-group-item') {
		var ordering = el.parentNode.parentNode.id.split('-')[2];
		var process_id = el.parentNode.parentNode.parentNode.parentNode.id.split('-')[1];
		var url = ogb_be_url + 'write_down_input_processor&input_type=' + st + '&input_value=' + of + '&input_name=' + ipf[2] + '&process_id=' + process_id + '&ordering=' + ordering + '&id=' + ogb_id;
		jQuery.ajax({
			url       : url,
			type      : 'GET',
			beforeSend: function () {
				jQuery('#dvLoading').show()
			},
			success   : function (txt) {
				var result = JSON.parse(txt);
				var current_process = new Array();
				for (var key in result) {
					if (typeof(result[key]) == 'string' && result[key] != '') {
						if (result[key].length > 200)
							result[key] = result[key].substring(0, 200) + "...";
						current_process.push(key + '<br /><p data-placement="bottom" title="' + result[key] + '" class="text-muted small">' + result[key] + '</p>');
					} else {
						current_process.push(key);
					}
				}
				updateOprocessor(current_process, ordering);
				jQuery('#dvLoading').hide();
			}
		});
	}
	call_tooltip();
	ogb_gid('ogb-list-output').style.display = 'none';
}
function ogb_getOrder(el) {
	var id = el.parentNode.parentNode.id;
	var order = id.split('-');
	if (order[1] == 'ia') {
		ogb_order = 'a';
	} else {
		ogb_order = order[2];
	}
}
function ogb_unable_get(st) {
	if (ogb_order == 'a' || st == 'e' || st == '') {
		return false;
	}
	st = parseInt(st);
	if (st < parseInt(ogb_order)) {
		return false;
	}
	return true;
}
function showListOp(el) {
	myPos = ogbFindPos(el);
	x = myPos.left - 200;
	y = myPos.top - 50;

	var a = ogb_gid('ogb-list-output');
	a.style.left = (x) + 'px';
	a.style.top = (y) + 'px';
	a.style.display = 'inline';

	ogb_getOrder(el);
	var uls = ogb_gid('ob-oplist').getElementsByTagName('ul');
	for (var i = 0; i < uls.length; i++) {
		if (ogb_order == 'a') {
			uls[i].style.display = 'block';
		} else {
			uls[i].style.display = ogb_order > i ? 'block' : 'none';
		}
	}
}
function ogbFindPos(obj) {
	var pos = jQuery(obj).position();
	var x = parseInt(pos.left);
	var y = parseInt(pos.top);
	jQuery(obj).parents().each(function (index, par) {
		if (jQuery(par).prop("id") === 'fields_matching' || jQuery(par).prop("tagName") === 'html') {
			return false;
		}
		if (jQuery(par).css('position') === 'relative') {
			var tpost = jQuery(par).position();
			x = x + parseInt(tpost.left);
			y = y + parseInt(tpost.top);
		}
	});
	x = parseInt(x);
	y = parseInt(y);
	return {'top': y, 'left': x};
}

function ogb_closeListOp() {
	ogb_gid('ogb-list-output').style.display = 'none';
}

function ogb_chose_field(el) {
	showListOp(el);
	var spans = el.parentNode.getElementsByTagName('span');
	for (var i = 0; i < spans.length; i++) {
		if (spans[i].className == 'obval') {
			obgid('change-field').innerHTML = spans[i].innerHTML;
			break;
		}
	}
	ogb_change_field = el;
	return;
}

function ogb_loadAdapter(id) {
	if (!id) {
		id = (document.getElementById('ogb_id')) ? document.getElementById('ogb_id').value : 0;
	}
	var name = obgid('ogb_adapter').value;
	if (name == '') {
		ogb_loada++;
		return;
	}
	if (ogb_loada > 0) {
		getIOaddon('adapter', name);
	} else {
		ogb_loada++;
	}
	//ogb_load_set_pro(name, 'adapter');
	ogb_loadAddonParam('ogb-adapter-param', 'adapter', name, id);
}

function ogb_loadEngine(id) {
	var name = obgid('ogb_engine').value;
	if (name == '') {
		ogb_loade++;
		return;
	}
	if (ogb_loade > 0) {
		getIOaddon('engine', name);
	} else ogb_loade++;
	//ogb_load_set_pro(name, 'engine');
	ogb_loadAddonParam('ogb-engine-param', 'engine', name, id);

}
function ogb_loadProcessor(name, id) {
	ogb_loadAddonParam('ob-param-' + id, 'processor', name, id);
}
function ogb_loadAddonParam(idud, type, name, id) {
	obgid(idud).innerHTML = '<i>Loading...</i>';
	var url = ogb_be_url + 'gaparam&type=' + type + '&name=' + name;
	url += id > 0 ? '&id=' + id : '';
	jQuery.ajax({
		url        : url,
		type       : 'GET',
		evalscripts: true,
		success    : function (txt) {
			updateAddonParam(txt, idud);
			call_chosen();
			//call_taginput();
			parseScript(txt);
		}
	});
}

function call_taginput() {
	jQuery('input[data-role="tagsinput"]').tagsinput({
	});
}

function call_chosen() {
	return;
	var config = {
		".chosen-select": {}
	};
	for (var selector in config) {
		jQuery(selector).chosen(config[selector]);
	}
	;
}

function updateAddonParam(txt, idud) {
	//obgid('ogb-engine-param').innerHTML = txt;
	obgid(idud).innerHTML = txt;
	/*
	 $$('[data-toggle="tooltip"]').each(function(el) {
	 var title = el.get('data-original-title');
	 if (title) {
	 var parts = title.split('::', 2);
	 el.store('tip:title', parts[0]);
	 el.store('tip:text', parts[1]);
	 }
	 });
	 var JTooltips = new Tips($$('[data-toggle="tooltip"]'), { maxTitleChars: 50, fixed: false});
	 */
}

/*function create_set(str){
 var set	= str.split('-');
 if(ogb_id==0){
 alert('This area works on edit mode only. Please press Save button first.');
 return;
 }
 /*var i = 0
 var interval = setInterval(function(){
 addProcessor(set[i], i)
 i += 1;

 if(i == set.length)
 clearInterval(interval);
 }, 1000);
 var inputset = document.getElementsByTagName('input_set_default');
 inputset.value=str;
 console.log(document.getElementsByTagName('input_set_default').value);
 //submitform('apply');
 }*/

function addProcessor(code, order) {
	if (ogb_id == 0) {
		alert('This area works on edit mode only. Please press Save button first.');
		return;
	}
	var new_processor = obgid('new_processor');
	var npp_order = obgid('npp_order');
	if (code == '' || code == null) {
		code = new_processor.value;
	}
	if (code == '') {
		alert('You need select one processor');
		new_processor.focus();
		return;
	}
	ogb_need_save = true;
	if (order == '' || order == null) {
		order = parseInt(npp_order.value);
	}
	if (order < 0) {
		order = 0;
	}
	var url = ogb_be_url + 'addprocess&code=' + code + '&order=' + order + '&id=' + ogb_id;
	jQuery.ajax({
		url    : url,
		type   : 'GET',
		success: function (txt) {
			updateProcess(code, txt);
			call_chosen();
		}
	});
	jQuery('#new_processor').val("").trigger("liszt:updated");
	jQuery('#new_processor option:first-child').attr("selected", "selected");
//	new Request({url:url,method:'get',onSuccess:function(txt,responseXML){updateProcess(code,txt);}}).send();
}
function updateProcess(code, txt) {
	var obj = eval('(' + txt + ')');
	if (obj.error != '') {
		return;
	}
	var tr = document.createElement('li');
	tr.setAttribute('class', 'list-group-item');
	tr.setAttribute('id', 'pipes_processor-' + obj.pipe_id);
//	tr.style.marginLeft	= '0';
	var td = '<div class="col-md-4"><ul class="unstyled" id="ob-ip-' + obj.order + '">';
	td += '<li><i>Loading...</i></li></ul></div>';
	td += '<div class="col-md-5"><span style="float:left"><a href="javascript:void(0);" title="Options" onclick="showParams(this,' + obj.pipe_id + ');"><i class="fa fa-expand"></i></a></span>&nbsp;<strong style="color:#006600;">' + obj.name + '</strong>';
	td += '<span style="float: left;">&nbsp;<a href="javascript:void(0);" title="Help" onclick="showHelps(this,' + obj.pipe_id + ');"><i class="fa fa-question-circle"></i></a>&nbsp;</span>';
	td += '<span style="float:right">';
	td += '<a href="javascript:void(0);" title="Remove" onclick="remove_pipe(' + obj.pipe_id + ')"><i class="fa fa-trash-o" style="color:#b94a48"></i></a>';
	td += '</span>';
	td += '</br><textarea class="form-control input-sm" name="note" rows="1" id="note_' + obj.pipe_id + '" onblur="savenote(' + obj.pipe_id + ')" placeholder="Write note here!"></textarea>';
	td += '</div><div class="col-md-3"><ul class="unstyled" id="ob-op-' + obj.order + '"><li><i>Loading...</i></li></ul></div>';
	td += '<div class="col-md-12 well well-small" style="display:none;" id="ob-param-' + obj.pipe_id + '"><i>Loading...</i></div>';
	td += '<div class="col-md-12 well well-small" style="display:none;" id="ob-param-help-' + obj.pipe_id + '"><i>Loading...</i></div>';
	td += '<div class="clearfix"></div>';
	tr.innerHTML = td;
	var list_pro = obgid('ogb_list_processor');
//	var trs = list_pro.getElementsByTagName('li');
	var trs = jQuery('#ogb_list_processor > li');
	if (max_order == 0) {
		list_pro.insertBefore(tr, trs[trs.length - 1]);
//		list_pro.innerHTML = '<li class="list-group-item">' + td + '<div>';
	} else if (trs.length > obj.order) {
		list_pro.insertBefore(tr, trs[trs.length - 1]);
	} else {
		var c = (max_order + 1) % 2;
		tr.className = 'list-group-item';
		list_pro.insertBefore(tr, trs[trs.length - 1]);
//		list_pro.appendChild(tr);
	}
	max_order++;
	obgid('npp_order').value = max_order;

	//ogb_loadAddonParam('ob-param-'+obj.pipe_id,'processor',code,obj.pipe_id);
	getIOaddon('processor', code, obj.order);

}
function remove_pipe(pid) {
	if (!confirm('Are you sure you want to delete the Pipe[' + pid + ']?')) {
		return;
	}
	ogb_need_save = true;
	var url = ogb_be_url + 'remove_pipe&pid=' + pid + '&itid=' + ogb_id
	jQuery.ajax({
		url    : url,
		type   : 'GET',
		success: function (txt) {
			remove_result(txt, pid);
		}
	});
	//new Request({url:url,method:'get',onSuccess:function(txt){remove_result(txt,pid);}}).send();
}
function remove_result(txt, id) {
	var tr = obgid('ob-param-' + id).parentNode;
	tr.parentNode.removeChild(tr);
	max_order;
	obgid('npp_order').value = max_order;
	/*obgid('ob-ip-'+max_order).getElementsByTagName('input')[0].name = 'ip['+(max_order-1)+'][0]';
	 //	obgid('ob-ip-'+max_order+' li input').name = 'op['+(max_order-1)+'][0]';
	 obgid('ob-ip-'+max_order).id = 'ob-ip-'+(max_order-1);
	 var output_proc = obgid('ob-op-'+max_order).getElementsByTagName('input');

	 obgid('ob-op-'+max_order).id = 'ob-op-'+(max_order-1);*/
	//alert(txt+': '+id);
	location.reload();
}
function ogb_load_process_params() {
	for (var i = 0; i < ogb_pipes.length; i++) {
		ogb_loadAddonParam('ob-param-' + ogb_pipes[i].id, 'processor', ogb_pipes[i].code, ogb_pipes[i].id);
	}
}
function getIOaddon(type, code, order) {
	var url = ogb_be_url + 'getioaddon&type=' + type + '&name=' + code;
	if ((type == 'adapter' && arguments.length > 2) || (type == 'engine' && arguments.length > 2)) {
		for (i = 2; i < arguments.length; i++) {
			url += '&arg' + i + '=' + arguments[i];
		}
	}
	if (type != 'processor')
		url += '&id=' + ogb_id;

	jQuery.ajax({
		url        : url,
		type       : 'GET',
		evalscripts: true,
		success    : function (txt) {
			updateIOaddon(txt, type, code, order);
		}
	});
//	new Request({url:url,method:'get',onSuccess:function(txt){updateIOaddon(txt,type,code,order);}}).send();
}
function updateIOaddon(txt, type, code, order) {
	var obj = eval('(' + txt + ')');
	if (obj.err != '') {
		//alert(obj.err);
		return;
	}
	switch (type) {
		case 'engine':
			updateOengine(obj.output);
			break;
		case 'processor':
			var ip = makeIdefault(obj.input);
			updateIprocessor(ip, order);
			updateOprocessor(obj.output, order);
			save_b4_post();
			break;
		case 'adapter':
			var ia = makeIdefault(obj.input);
			updateIadapter(ia);
			break;
		default:
			alert('unknow type ' + type);
	}
}
function updateOengine(oe) {
	var litxt = '';
	var oelist = '<b>[so]</b>';
	for (var i = 0; i < oe.length; i++) {
		var real_value = oe[i].split('<br />');
		litxt += '<li><text class="drag_drop" draggable="true" ondragstart="drag(event)" >[so] ' + real_value[0] + '</text><br />' + real_value[1];
		litxt += '<input type="hidden" name="oe[' + i + ']" value="' + real_value[0] + '"></li>';
		oelist += '<li class="obfield" onclick="ogb_update_field(\'e\',\'' + real_value[0] + '\');">&nbsp;&nbsp; - ' + oe[i] + '</li>';
	}
	obgid('ob-oe').innerHTML = litxt;
	obgid('ob-oelist').innerHTML = '<ul class="unstyled oblistfield">' + oelist + '</ul>';
	call_tooltip();
}
function updateOprocessor(op, order) {
	var litxt = '';
	var ul = document.createElement('ul');
	ul.className = 'unstyled oblistfield';
	var li = '<b>po[' + order + ']</b>';

	for (var i = 0; i < op.length; i++) {
		var real_value = op[i].split('<br />');
		litxt += '<li><text class="drag_drop" draggable="true" ondragstart="drag(event)" >po[' + order + '] ' + real_value[0] + '</text><br />' + real_value[1];
		litxt += '<input type="hidden" name="op[' + order + '][' + i + ']" value="' + real_value[0] + '"></li>';
		li += '<li class="obfield hasTip" onclick="ogb_update_field(\'' + order + '\',\'' + real_value[0] + '\');">&nbsp;&nbsp; - ' + op[i] + '</li>';
	}
	//obgid('ob-op-'+order).innerHTML = litxt;
	var tg = obgid('ob-op-' + order);
	if (tg) {
		tg.innerHTML = litxt;
	}

	ul.innerHTML = li;

	var list_op = obgid('ob-oplist');
	var uls = list_op.getElementsByTagName('ul');
	if (order == 0 && uls.length == 0) {
		list_op.innerHTML = '<ul class="unstyled oblistfield">' + li + '</ul>';
	} else if (uls.length > order) {
		//list_op.insertBefore(ul, uls[order]);
		uls[order].text = ul;
	} else {
		list_op.appendChild(ul);
	}
	call_tooltip();
}

function call_tooltip() {
	jQuery('.text-muted').tooltip();
}

function updateIprocessor(ip, order) {
	var litxt = '';
	for (var i = 0; i < ip.length; i++) {
		var st = ip[i].st;
		var key;
		if (st != '') {
			key = st == 'e' ? '[so]' : 'op[' + st + ']';
		} else {
			key = '';
		}
		key = key == '' ? 'Click me' : key + ' ' + ip[i].of;
		var style = ip[i].if == '' ? ' style="display:none;"' : '';
		var onclick = ip[i].if == '' ? '' : 'onclick="ogb_chose_field(this);"';
		litxt += '<li' + style + '><span ondrop="drop(event)" ondragover="allowDrop(event)" ondragstart="drag(event)" class="obkey" ' + onclick + '>' + key + ' </span>&nbsp;';
		litxt += '<span class="obval hasTip">pi[' + order + '] ' + ip[i].if + '</span>';
		litxt += '<input type="hidden" value="' + st + ',' + ip[i].of + ',' + ip[i].if + '" name="ip[' + order + '][' + i + ']"></li>';
	}
	var tg = obgid('ob-ip-' + order);
	if (tg) {
		tg.innerHTML = litxt;
	}
}
function makeIdefault(ivals_1) {
	var ivals_2 = [];

	for (var i = 0; i < ivals_1.length; i++) {
		ivals_2[i] = {};
		ivals_2[i].st = '';
		ivals_2[i].of = '';
		ivals_2[i].if = ivals_1[i];
	}
	return ivals_2;
}
function updateIadapter(ia) {
	var litxt = '';
	for (var i = 0; i < ia.length; i++) {
		var st = ia[i].st;
		var key;
		if (st != '') {
			key = st == 'e' ? '[so]' : 'po[' + st + ']';
		} else {
			key = '';
		}
		key = key == '' ? 'Click me' : key + ' ' + ia[i].of;
		litxt += '<li><span ondrop="drop(event)" ondragover="allowDrop(event)" ondragstart="drag(event)" class="obkey" onclick="ogb_chose_field(this);">' + key + ' </span>&nbsp;';
		litxt += '<span class="obval hasTip">[di] ' + ia[i].if + '</span>';
		litxt += '<input type="hidden" value="' + st + ',' + ia[i].of + ',' + ia[i].if + '" name="ia[' + i + ']"><div class="clearfix"></div></li>';
	}
	obgid('ob-ia').innerHTML = litxt;
	return;
	//alert('updateIadapter');	
	var aa = iValue;
	var txt = '';
	for (var i = 0; i < aa.length; i++) {
		txt += "\n" + i + ' - ' + aa[i];
	}
	alert(txt);
}

function ogb_config_as_default() {
	if (!confirm('Are you sure you?')) {
		return;
	}
	obgid('task').value = 'cfdf';
	obgid('adminForm').submit();
}
function savenote(p_id) {
	var new_note = document.getElementById('note_' + p_id).value;
	var url = ogb_be_url + 'savenote&id=' + p_id + '&new_note=' + new_note;

	jQuery.ajax({
		url    : url,
		type   : 'GET',
		success: function (resp) {
			if (resp) document.getElementById('processor_' + p_id + '__params_note').value = new_note;
		}
	});
}

function ogb_loadA_extra() {
	var value = obgid('adapter_params_categories').value;
	var url = ogb_be_url + 'getioaddon&type=adapter&name=k2&arg2=onjs&arg3=' + value;

	new Request({url: url, method: 'get', onSuccess: function (txt) {
		updateIOaddon(txt, 'adapter', 'k2');
	}}).send();
}

function ogb_load_set_pro(name, type) {
	var divset = obgid('jform_set_default_obg');
	if (type == 'adapter') {
		var engine = obgid('ogb_engine').value;
		var adapter = name;
	} else {
		var adapter = obgid('ogb_adapter').value;
		var engine = name;
	}
	if (engine == 'rssreader' && ( adapter == 'post' || adapter == 'content' )) {
		divset.style.visibility = 'visible';
	} else {
		divset.style.visibility = 'hidden';
	}
}
function save_b4_post() {
	var inputValues = new Array();
	jQuery('input').each(function () {
		var check = true;
		if (jQuery(this).is(':radio')) {
			if (!jQuery(this).is(":checked")) {
				check = false;
			}
		}
		if (check) {
			inputValues[jQuery(this).attr('name')] = jQuery(this).val();
		}
	});
	jQuery('textarea').each(function () {
		inputValues[jQuery(this).attr('name')] = jQuery(this).val();
	});
	jQuery('select option:selected').each(function () {
		if (typeof inputValues[jQuery(this).parent().attr('name')] !== 'undefined' && !jQuery(this).parent().attr('disabled')) {
			inputValues[jQuery(this).parent().attr('name')] = inputValues[jQuery(this).parent().attr('name')] + '||' + jQuery(this).val();
		} else {
			inputValues[jQuery(this).parent().attr('name')] = jQuery(this).val();
		}
	});
	var jform = new Array();
	var engine_par = new Array();
	var adapter_par = new Array();
	var oe = new Array();
	var ia = new Array();
	var ip = new Array();
	var op = new Array();
	var proce = new Array();
	for (key in inputValues) {
		var index_arr = key.split('[');
		if (key.indexOf("jform") != -1) {
			jform.push(index_arr[1].replace(']', '') + '||' + inputValues[key]);
		}
		if (key.indexOf("engine[params]") != -1) {
			engine_par.push(index_arr[2].replace(']', '') + '||' + inputValues[key]);
		}
		if (key.indexOf("adapter[params]") != -1) {
			adapter_par.push(index_arr[2].replace(']', '') + '||' + inputValues[key]);
		}
		if (key.indexOf("oe[") != -1) {
			oe.push(index_arr[1].replace(']', '') + '||' + inputValues[key]);
		}
		if (key.indexOf("ia[") != -1) {
			ia.push(index_arr[1].replace(']', '') + '||' + inputValues[key]);
		}
		if (key.indexOf("ip[") != -1) {
			ip.push(index_arr[1].replace(']', '') + '_' + index_arr[2].replace(']', '') + '||' + inputValues[key]);
		}
		if (key.indexOf("op[") != -1) {
			op.push(index_arr[1].replace(']', '') + '_' + index_arr[2].replace(']', '') + '||' + inputValues[key]);
		}
		if (key.indexOf("processor[") != -1) {
			proce.push(index_arr[1].replace(']', '') + '-' + index_arr[3].replace(']', '') + '||' + inputValues[key]);
		}
	}
	var url = ogb_be_url + 'save_b4_post';
	jQuery.ajax({
		url    : url,
		type   : 'POST',
		data   : {jform: jform, engine_par: engine_par, adapter_par: adapter_par, oe: oe, ia: ia, ip: ip, op: op, proc: proce},
		success: function (resp) {
		}
	});
}

function display_form() {
	var form_iw = obgid('dropdown-iwant');
	if (form_iw.style.display == 'none') {
		form_iw.style.display = 'block';
		obgid('iwantto').style.display = 'block';
		obgid('iwanto_thanks').innerHTML = 'COM_OBGRABBER_IWANT_DISCLAIM';
	} else {
		form_iw.style.display = 'none';
	}

	var button = obgid('iw_btn');
	var textarea = obgid('iwantto');
	textarea.focus();
	textarea.value = 'I want ';
	button.onclick = function () {
		var cur_url = encodeURIComponent(window.location.href);
		var input = obgid('iwantto').value;
		if (input == '') {
			alert('COM_OBGRABBER_PLEASE_INPUT_WHAT_YOU_WANT');
			return false;
		} else {
			button.innerHTML = 'COM_OBGRABBER_SENDING';
			var url = ogb_be_url + 'iwant&mess=' + input + '&cur_url=' + cur_url;

			jQuery.ajax({
				url    : url,
				method : 'get',
				success: function (txt) {
					button.innerHTML = 'Send';
					obgid('iwantto').style.display = 'none';
					obgid('iwanto_thanks').innerHTML = 'COM_OBGRABBER_THANK_YOU';
					setTimeout(function () {
						form_iw.style.display = 'none';
					}, 3000);
				}
			});
		}
	};
}
function refresh_mapping() {
	if (!confirm('Are you sure you want to reset fields_mapping')) {
		return;
	}
	ogb_need_save = true;
	var url = ogb_be_url + 'remove_pipe&itid=' + ogb_id + '&pid=';
	var id_array = new Array();
	jQuery('.obkey').each(function (i, obj) {
		var ip = obj.parentNode.getElementsByTagName('input')[0];
		var ipf = ip.value.split(',');
		var list_obj = obj.parentNode.parentNode.parentNode.parentNode;
		if (list_obj.className == 'list-group-item') {
			var seperate = list_obj.id.split('-');
			id_array[i] = seperate[1];
			list_obj.remove();
		}
		ip.value = ',,' + ipf[2];
		obj.innerHTML = 'Click me';
	});
	for (var j = 0; j < id_array.length; j++) {
		var url_each = url + id_array[j] + '&count=' + (j + 1);
		jQuery.ajax({
			url    : url_each,
			type   : 'GET',
			success: function (txt) {
				if (txt == id_array.length) {
					submitbutton(ogb_gid('adminForm'), 'apply');
				} else {
					//	alert('please wait in seconds!');
				}
			}
		});
	}
	if (id_array.length == 0)
		submitbutton(ogb_gid('adminForm'), 'apply');
}
function call_function_from_addon(type, name, callback, value) {
	jQuery('#dvLoading').show();
	//var url = ogb_be_url + 'execaddonmethod&type=' + type + '&name=' + name + '&method=' + callback + '&val_default=' + value + '&id=' + ogb_id + '&ajax=1';
	/*jQuery.ajax({
	 url    : url,
	 type   : 'GET',
	 success: function (txt) {
	 getIOaddon(type, name);
	 update_all_processor_output();
	 jQuery('#dvLoading').hide();
	 call_tooltip();
	 }
	 });*/
	var url = ogb_be_url + 'execaddonmethod';
	jQuery.ajax({
		url    : url,
		type   : 'POST',
		data   : {type: type, name: name, method: callback, val_default: value, id: ogb_id, ajax: 1},
		success: function (txt) {
			getIOaddon(type, name);
			update_all_processor_output();
			jQuery('#dvLoading').hide();
			call_tooltip();
		}
	});
}

function update_all_processor_output() {
	var list_in_page = document.getElementsByClassName('obkey');
	for (var i = 0; i < list_in_page.length; i++) {
		var el = list_in_page[i];
		if (el.parentNode.parentNode.parentNode.parentNode.className == 'list-group-item') {
			var ip = el.parentNode.getElementsByTagName('input')[0];
			var ipf = ip.value.split(',');
			ogb_change_field = el;
			//if (ipf[0] != '' && ipf[1] != '')
			ogb_update_field(ipf[0], ipf[1]);
		}
	}
	call_tooltip();
}

function parseScript(strcode) {
	var scripts = new Array();         // Array which will store the script's code

	// Strip out tags
	while (strcode.indexOf("<script") > -1 || strcode.indexOf("</script") > -1) {
		var s = strcode.indexOf("<script");
		var s_e = strcode.indexOf(">", s);
		var e = strcode.indexOf("</script", s);
		var e_e = strcode.indexOf(">", e);

		// Add to scripts array
		scripts.push(strcode.substring(s_e + 1, e));
		// Strip from strcode
		strcode = strcode.substring(0, s) + strcode.substring(e_e + 1);
	}

	// Loop through every script collected and eval it
	for (var i = 0; i < scripts.length; i++) {
		var scrpt = document.createElement('script');
		scrpt.text = scripts[i];
		document.head.appendChild(scrpt);
	}
}

function allowDrop(ev) {
	ev.preventDefault();
}

function drag(ev) {
	ev.dataTransfer.setData("Text", ev.target.innerHTML);
}

function drop(ev) {
	ev.preventDefault();
	var data = ev.dataTransfer.getData("Text");
	var el = ev.target;
	var input = el.parentNode.getElementsByTagName('input')[0];
	if (typeof(input) == 'undefined') {
		return false;
	}
	var data_seperate = data.split(' ');
	var of = data_seperate[1];
	if (data_seperate[0] == '[so]') {
		var st = 'e';
	} else {
		var st = data_seperate[0].match(/\[(.+)\]/i);
		st = st[1];
	}
	var ipf = input.value.split(',');
//	input.value = st + ',' + of + ',' + ipf[2];
	ev.target.innerHTML = data;
	ogb_change_field = el;
	ogb_update_field(st, of);
}
