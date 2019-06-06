$(function(){
	$('.check_g').click(function(){
		$(this).css('display','none').parent().children('.check_w').css('display','block');
		$('#chkpass').attr('checked',false);
	})
	$('.check_w').click(function(){
		$(this).css('display','none').parent().children('.check_g').css('display','block');
		$('#chkpass').attr('checked',true);

	})
})