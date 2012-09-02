function addhe(num){
	$("#heCount").text(($("#heCount").text() *1)  + 1);
	$.post('/posts/addhe',{
		'id' : num,
		'add' : 1,
	},function(){});
}

$(function(){

    $("#reloadPost").on("click",function(){
        $("#lpost").text('loading...');
        $.get("posts/getlpost",function(data){
            $("#lpost").html(data);
        });
    });


});
