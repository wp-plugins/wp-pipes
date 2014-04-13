<?php

function home_base_url(){   

// first get http protocol if http or https

$base_url = (isset($_SERVER['HTTPS']) &&

$_SERVER['HTTPS']!='off') ? 'https://' : 'http://';

// get default website root directory

$tmpURL = dirname(__FILE__);

// when use dirname(__FILE__) will return value like this "C:\xampp\htdocs\my_website",

//convert value to http url use string replace, 

// replace any backslashes to slash in this case use chr value "92"

$tmpURL = str_replace(chr(92),'/',$tmpURL);

// now replace any same string in $tmpURL value to null or ''

// and will return value like /localhost/my_website/ or just /my_website/

$tmpURL = str_replace($_SERVER['DOCUMENT_ROOT'],'',$tmpURL);

// delete any slash character in first and last of value

$tmpURL = ltrim($tmpURL,'/');

$tmpURL = rtrim($tmpURL, '/');


// check again if we find any slash string in value then we can assume its local machine

    if (strpos($tmpURL,'/')){

// explode that value and take only first value

       $tmpURL = explode('/',$tmpURL);

       $tmpURL = $tmpURL[0];

      }

// now last steps

// assign protocol in first value

   if ($tmpURL !== $_SERVER['HTTP_HOST'])

// if protocol its http then like this

      $base_url .= $_SERVER['HTTP_HOST'].'/'.$tmpURL.'/';

    else

// else if protocol is https

      $base_url .= $tmpURL.'/';

// give return value

return $base_url; 

}

?>


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
obHost = '<?php echo home_base_url()  ;?>';
//obHost = window.location.protocol+"//"+window.location.hostname;
window.addEventListener('load',function(){ogbCron.run()},true);
