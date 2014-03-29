var ogb_id = 0,ogbHost='',ogb_row=0,ogb_max_row=0,ogb_ud=false;
var ogbPost = {};
ogbPost.onload	= function (){
	this.get_data_engine(ogb_id);
}
ogbPost.get_data_engine	= function(id){
	var url = ogbHost+'admin.php?page=pipes.pipe&task=gengin&id='+id;
	if(ogb_ud){url+='&u=1';}
	var html= '<div id="res-item-'+id+'" class="">[ <b>Source: </b>getting started... ]</div>';
	ogb_set_html('ogb_res',html);
	ogbAjax(url,function(txt){ogbPost.geDataSucess(txt)});
}

ogbPost.geDataSucess	= function(txt){
	ogb_set_html('res-item-'+ogb_id,txt);
	var a = ogb_gid('ogb-'+ogb_id+'-efound');
	ogb_max_row = a.value;
	if(ogb_max_row>0){
		ogbPost.updatePro();
		ogbPost.adapter_store();
	}
}
ogbPost.adapter_store	= function(){
	var url = window.location.origin+window.location.pathname+'?page=pipes.pipe&task=asave&id='+ogb_id+'&row='+ogb_row;
	if(ogb_ud){url	+= '&u=1';}
	ogb_html_append('ogb-'+ogb_id+'-get-rows','<li id="ogb-'+ogb_id+'-get-row-'+ogb_row+'">Item '+ogb_row+' [Processing ...]</li>');
	//ogbAjax(url,function(txt){ogbPost.adapter_store_ss(txt)});
	jQuery.ajax({
		url    : url,
		type   : 'GET',
		success: function (txt) {
			ogbPost.adapter_store_ss(txt);
		}
	});
}
ogbPost.adapter_store_ss = function(txt){
	//ogb_set_html('ogb-'+ogb_id+'-get-row-'+ogb_row,txt+'<br />------ ------ ------<br />');
	
	var id = 'ogb-'+ogb_id+'-get-row-'+ogb_row;
	ogb_row++;
	ogbPost.updatePro();
	
	if(ogb_max_row>ogb_row){
		txt += '<br />------ ------ ------<br />';
	}
	ogb_set_html(id,txt);
	
	if(ogb_max_row>ogb_row){
		ogbPost.adapter_store();
	}else{
		html	= '<hr /><h3>Done</h3>';
		ogb_html_append('res-item-'+ogb_id,html);
	}
} 
ogbPost.updatePro	= function(){
	var	rate	= Math.round(ogb_row/ogb_max_row * 100 );
	ogb_set_html('ogb-'+ogb_id+'-res-bar','<div class="progress-bar bar-success" role="progressbar" aria-valuenow="'+rate+'" aria-valuemin="0" aria-valuemax="100" style="width: '+rate+'%; height: 100%;"><small>'+ogb_row+'/'+ogb_max_row+' Complete (success)</small></div>');
}