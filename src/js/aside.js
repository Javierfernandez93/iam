class Aside {
	constructor()
	{
		this.state = 'extend';
		this.time = 20;
		this.width = {
			extend : '25%',
			small : '5.5%'
		};
	}
	hide(callback,element){
		if(element)
		{
			$(element).parent().find('li').removeClass('box-aside-active');
			$(element).addClass('box-aside-active');
		}
		if(this.state == 'extend')
			$('.box-aside').removeClass('box-aside-show').addClass('box-aside-hide');
		else 
			$('.box-aside').removeClass('box-aside-show');

		if(callback) callback();
	}
	show(callback){
		if(this.state == 'extend')
			$('.box-aside').removeClass('box-aside-hide').addClass('box-aside-show');
		else
			$('.box-aside').removeClass('box-aside-show');
		if(callback) callback();
	}
	toggleSize(callback){
		$('.aside-item').toggle();
		$('.box-aside').removeClass('box-aside-show');
		if(this.state == 'extend')
		{
			this.state = 'small';
			$('.box-toggle-image').attr('src','../../src/img/list.png');
			$('.box-user-col').removeClass('col-md-3').addClass('col-md-12').css({'padding':'0px'});
			$('.box-aside').animate({'width':this.width.small},this.time)
		} else {
			this.state = 'extend';
			$('.box-toggle-image').attr('src','../../src/img/icons.png');
			$('.box-user-col').removeClass('col-md-12').addClass('col-md-3').css({'padding':'0 15px'});
			$('.box-aside').animate({'width':this.width.extend},this.time)
		}
	}
	toggle(callback){
		if(this.state == 'extend')
		{
			if($('.box-aside').hasClass('box-aside-hide'))
			{
				$('.box-aside').removeClass('box-aside-hide').addClass('box-aside-show');
			} else {
				$('.box-aside').removeClass('box-aside-show').addClass('box-aside-hide');
			}
		}
		if(callback) callback();
	}
}