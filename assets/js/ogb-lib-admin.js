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
	var el	= ogb_gid(id);
	var old_html	= el.innerHTML;
	el.innerHTML	= old_html + html; 
}
function ogb_set_html(id,html){
	ogb_gid(id).innerHTML = html;
}