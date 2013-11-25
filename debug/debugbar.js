
Mdk_Debugbar = new Object();
Mdk_Debugbar.init = function(){
	$.ajax({
		url:"/local/mdk/debug/error_reporting.php", 
		success: function(data){
				$("body").append(data);
			}
		}
	);
};

Mdk_Debugbar.setVisible = function(visible){
	if(visible){
		$("#mdk_debugbar").css("display","block");
	}else{
		$("#mdk_debugbar").css("display","none");
	}
}

Mdk_Debugbar.collapseGroup = function(id){
	var display = ($("#"+id).css("display")=="block")?"none":"block";
	$("#"+id).css("display",display);
}