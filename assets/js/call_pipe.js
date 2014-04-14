var timerFtext,obHost;
var ogbCron = {};
ogbCron.count = 0;
ogbCron.run = function(){
	//var url = obHost+'index.php?option=com_obgrabber&task=callaio';
	//var url = obHost+'index.php?option=com_obgrabber&task=cronjob';
    var url = obHost+'?pipes=cron&task=callaio';	
	ogbAjax(url,function(txt){ogbCron.next(txt)});
}
ogbCron.sleep = function(txt){
	clearTimeout(timerFtext);
	timerFtext = setTimeout ('ogbCron.run()',60000);
}
ogbCron.next = function(txt){
	if(this.stop(txt)){		
		this.sleep();
	}else if(ogbCron.count<10) {
		ogbCron.count ++;
		this.run();	
	}else{
		ogbCron.count = 0;
		this.sleep();
		//location.reload();
	}
}
ogbCron.stop = function(str){
	var p = /\{ogb-res:\d\}/g;
	if(p.exec(str)=='{ogb-res:0}'){
		return false;
	}
	return true;
}

// --- ogb Ajax ---
//ogbAjax(url,function(txt){ogbCron.next(txt)});
function ogbAjax(url,func){
    var xmlhttp;
    if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
}else{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function(){
    if (xmlhttp.readyState==4 && xmlhttp.status==200){
        this.onSuccess(xmlhttp.responseText);
    }
}
xmlhttp.open('GET',url,true);
xmlhttp.send();
xmlhttp.onSuccess=function(txt){func(txt);}
}
function ogb_gid(id){
    return document.getElementById(id);
}
function ogb_html_append(id,html){
    var el = ogb_gid(id);
    var old_html = el.innerHTML;
    el.innerHTML = old_html + html;
}
function ogb_set_html(id,html){
    ogb_gid(id).innerHTML = html;
}
window.addEventListener('load',function(){ogbCron.run()},true);
