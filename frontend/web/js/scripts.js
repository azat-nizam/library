$(document).ready(function(){
	$('.book-search').typeahead({
		limit:10,
		// получаем список книг
		source:function(query,process){
			$('.search-result tbody').html('');
			return $.post('/site/search', {'name':query},
				function(data){
					$('.search-result').html(data);
					$('.search-result').removeClass('hidden').addClass('show');
					$('.book-list').removeClass('show').addClass('hidden');
					$('.close-search').removeClass('hidden').addClass('show');
					/*
					var data=new Array();
					$.each(response,function(i,name){
						data.push(name);
					});
					//return process(data);
					*/
				},
				'html'
			);
		},
		// отображаем список
		highlighter:function(item){
			var bookData=item.split('|');
			var content=$('.search-result tbody').html();
			content+='<tr><td>'+bookData[1]+'</td><td>'+bookData[0]+'</td><td>'+(bookData[2]?'<div data-id="'+bookData[3]+'" class="book-free">Свободна</div>':'<div class="book-busy">Занята</button>')+'</td></tr>';
			$('.search-result tbody').html(content);
			$('.search-result').removeClass('hidden').addClass('show');
			$('.book-list').removeClass('show').addClass('hidden');
		},
	});
	// закрываем список результатов поиска и отображаем списки книг
	$('.close-search').click(function(){
		$('.search-result').removeClass('show').addClass('hidden');
		$('.book-list').removeClass('hidden').addClass('show');
		$('.close-search').removeClass('show').addClass('hidden');
		$('.book-search').val('');
	});
	// показываем окно входа
	$(document).on('click','#enter-modal',function(){
		$('#modal-popup').modal('show').find('#modal-book-content').load('site/login');
	});
	/*
	// автоувеличение прогрессбара
	var p=setInterval(function(){
		var b=$('.modal-loading');
		if(b.width()<900){
			b.width(b.width()+30);
			b.text(b.width()/3+'%');
			if(b.width()/3>=100){
				b.text('Загрузка...');
			}
		}else{
			clearInterval(p);
		}
	},50);
	*/
	// отправка формы на всплывающем окне
	$(document).on('submit','form#modal-book',function(){
		var f=$(this).attr('data-form');
		var _t=this;
		$.post($(this).attr('action'),
			{
				'd':$(this).serialize()
			},
			function(data){
				$('.close').trigger('click');
				if(f!=undefined && f.length>0){
					$.pjax.reload({container:'#'+f});
				}
				var response=$(_t).find('.response');
				if(response.length>0){
					response.html(data);
					$(_t).trigger('reset');
				}
				/*
				$('#modal-popup').find('#modal-book-content').html(data);
				setInterval(function(){
					$('.close').trigger('click');
				},2000);
				*/
			},
			'html'
		);
		return false;
	});
	//var hash = window.location.hash;
	//$('ul.nav-tabs a[href="' + hash + '"]').trigger('click');
	$('ul.nav-tabs a').mouseup(function(){
        console.log('проверка');
		var g=$(this).attr('href');
		if(g!=''&&g!='#'){
			var id=$(g+' > div').attr('id');
			if($('#'+id).length>0){
				$.pjax.reload({container:'#'+id});
			}
		}
		return false;
	});
});