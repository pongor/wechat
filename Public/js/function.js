

$(function(){
	
	


	/**
	 * section：初始化上下边距
	 */
	$('.mod_section').css('padding-top',$('.mod_header').css('height'));
	$('.mod_section').css('padding-bottom',$('.mod_foter').css('height'));


	/**
	 * selectSimilarity: 点击输入框，optionBox的展开和隐藏and赋值
	 */
	$(".selectSimilarity").click(function(event){
		$(this).children('.selectSimiOptBox').slideToggle(200);
		event.stopPropagation();
		$('.selectSimiOptBox').each(function(){
			if($(this).css('display') != 'none'){
				$(this).css('display','none');
			}
		});
		$(this).children('.selectSimiOptBox').css('display','block');
	});	
	$(".selectSimiOptBox p").click(function(){
		var pVal = $(this).html();
		$(this).parent().siblings("input").val(pVal);
	})
	
	
	/**
	 * 表格：初始化宽度和颜色
	 */
	$(".TWT td").each(function(){
		var width = $(this).attr('class').split('TWT')[1];
		$(this).attr('width',width+'%');
	});
	
	$(".mod_tbody tr:odd").css('background-color','#FFFFFF');
	$(".mod_tbody tr:even").css('background-color','#f7f7f7');


	/**
	 * slew: 选中页码的样式变化
	 */
	$(".cell_slewOne li").click(function(){
		$(this).addClass("offSlew").siblings().removeClass("offSlew");
	});	
	
	
	/**
	 * radio or checkbox：选中及样式切换
	 */
	$('.checkOption').click(function(){
		if($(this).children('input:radio').length){
			//radio
			var name = $(this).children('input:radio').attr('name');
			$("input:radio[name="+name+"]").each(function(){
				$(this).prevAll('div').children('img').css('display','none');
				$(this).attr('checked',false);
			});
			$(this).children('div').children('img').css('display','block');
			$(this).children('input:radio').attr('checked',true);
		}else{
			//checkBox
			if($(this).children('input:checkbox').attr('checked') == 'checked'){
				$(this).children('input:checkbox').attr('checked',false);
				$(this).children('div').children('img').css('display','none');
			}else{
				$(this).children('input:checkbox').attr('checked',true);
				$(this).children('div').children('img').css('display','block');
			}			
		}
	});
	
	
	
})
	