;(function (){
	var oMenutab = document.getElementById('menuTab');
		var oMenuh3 = oMenutab.getElementsByTagName('h3');
		var aMenudiv = oMenutab.getElementsByTagName('div');
		for(var i = 0; i < oMenuh3.length; i++){
			oMenuh3[i].index = i;
			oMenuh3[i].onclick = function(){
			for(var i = 0; i < oMenuh3.length; i++){
				oMenuh3[i].className = '';
				aMenudiv[i].className = '';
				}
			    this.className = 'active';
			 	aMenudiv[this.index].className = 'block';
			}
		
		}
})()
;(function (){
	function Tab(btn,con,current){
		$(btn).click(function(){
		var index = $(this).index();

		$(this).addClass(current).siblings().removeClass(current);
		$(con).eq(index).show().siblings().hide();
	})
	}
	Tab('.tabBtn li', '.resultChart .con', 'current');
	Tab('.jxjhTab .menu li', '.jxjhTab .con', 'current');
	
//	$('.tabBtn li').click(function(){
//		var index = $(this).index();
//
//		$(this).addClass('current').siblings().removeClass('current');
//		$('.resultChart .con').eq(index).show().siblings().hide();
//	})
})()

