$(document).ready(function(){
	$('.book-search').keyup(function(){
		if($(this).val()==''){
			$.post(
				'/site/searchfirst',
				{},
				function(data){
					changeUrl('search&q=');
					$('.search-result').html(data);
					$('.adaptive-search').removeClass('col-xs-10').addClass('col-xs-12');
					$('.search-result').removeClass('hidden').addClass('show');
					$('.book-list').removeClass('show').addClass('hidden');
					$('.close-search').removeClass('hidden').addClass('show');
				},
				'html'
			)
		}
	});
	$('.book-search').typeahead({
		limit:10,
		// получаем список книг
		source:function(query,process){
			$('.search-result tbody').html('');
			return $.post('/site/search', {'name':query},
				function(data){
					changeUrl('search&q='+query);
					$('.search-result').html(data);
					$('.adaptive-search').removeClass('col-xs-10').addClass('col-xs-12');
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
		$('.adaptive-search').removeClass('col-xs-12').addClass('col-xs-10');
		$('.search-result').removeClass('show').addClass('hidden');
		$('.book-list').removeClass('hidden').addClass('show');
		$('.close-search').removeClass('show').addClass('hidden');
		$('.book-search').val('');
		var tab=$('#myTab .active a').attr('href');
		changeUrl(tab.substr(1));
	});
	// показываем окно входа
	$(document).on('click','#enter-modal',function(){
		$('#modal-popup').modal('show').find('#modal-book-content').load('site/login');
		$('#modal-popup').find('.modal-header h2').text('Login');
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
	/*
	function changeBookStatus(id){
		var e = $('[data-id='+id+']');
		if (e.hasClass('book-free')) {
			e.removeClass('book-free').addClass('book-busy').html('<span>Занята</span>');
		} else {
			e.removeClass('book-busy').addClass('book-free').html('<span>Доступна</span>');
		}
	}
	*/
    function changeBookStatus2(id){
		var e = $('[data-id='+id+']');
        $.each(e, function(i, v) {
            if ($(v).hasClass('book-free')) {
                $(v).removeClass('book-free').addClass('book-busy').html('<span>Занята</span>');
                //console.log('reserve' + $(v).html());
            } else {
                $(v).removeClass('book-busy').addClass('book-free').html('<span>Доступна</span>');
                //console.log('unreserve' + $(v).html());
            }
        });
	}
	// отправка формы на всплывающем окне
	$(document).on('submit','form#modal-book',function(){
        console.log('submit');
		var f=$(this).attr('data-form');
		var _t=this;
		$.post($(this).attr('action'),
			{
				'd':$(this).serialize()
			},
			function(data){
				var content = $('#modal-book-content').html();
				$('#modal-book-content').html(data);
				//$('#modal-popup .close').trigger('click');
				if(f!=undefined && f.length>0){
					//$.pjax.reload({container:'#'+f});
                    console.log($(_t).find('#book-id').val());
                    var reader = $(_t).find('#book-reader').val();
                    var status = $(_t).find('#book-status').val();
                    if (
                        status == '1' && reader == ''
                        || status == '0' && reader != ''
                    ) {
                        changeBookStatus2($(_t).find('#book-id').val());
                    } else {
                        
                    }
				}
				var response=$(_t).find('.response');
				if(response.length>0){
					response.html(data);
					$(_t).trigger('reset');
				}
				setTimeout(function(){
					console.log('close click');
					$('.close').trigger('click');
					$('#modal-book-content').html(content);
				},2000);
				//return false;
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
	var search = window.location.search.substr(1),
	keys = {};
	search.split('&').forEach(function(item) {
		item = item.split('=');
		keys[item[0]] = item[1];
	});
	if(keys['tab']!=undefined){
		if(keys['tab']=='search'&&keys['q']!=undefined){
			var q=decodeURI(keys['q']);
			$('.book-search').val(q).trigger('keyup');
		}
		$('ul.nav-tabs a[href="#' + keys['tab'] + '"]').trigger('click');
	}
	$('ul.nav-tabs a').mouseup(function(){
		var g=$(this).attr('href');
		console.log(g);
		if(g!=''&&g!='#'){
			var id=$(g+' > div').attr('id');
			console.log($('#'+id).length);
			if($('#'+id).length>0){
				changeUrl(g.substr(1));
				$.pjax.reload({container:'#'+id});
			}
		}
		return false;
	});
	function isHhistoryApiAvailable() {
		return !!(window.history && history.pushState);
	}
	function changeUrl(id){
		if(isHhistoryApiAvailable()){
			var url=window.location.pathname+'?tab='+id;
			window.history.pushState(null, null, url);
		}
		return false;
	}
	if(!isHhistoryApiAvailable()){
		console.log('Ваш браузер не поддерживает изменение url без перезагрузки страницы');
	}
	$('.adaptive-menu-btn').click(function(){
		if ($('.adaptive-navbar').css('display') !== 'none') {
			$('#myTab').toggle();
		}
	});
	$('#myTab a').click(function(){
		if ($('.adaptive-navbar').css('display') !== 'none' && $(this).attr('id') !== 'myTabDrop1') {
			setTimeout(function(){
				$('#myTab').toggle();
			}, 200);
		}
	});
});