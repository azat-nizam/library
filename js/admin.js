$(document).ready(function(){
	function changeBookStatus(id){
        var e = $('[data-id='+id+']');
        $.each(e, function(i, v) {
            if ($(v).hasClass('book-free')) {
                $(v).removeClass('book-free').addClass('book-busy').html('<span>Занята</span>');
                console.log('reserve' + $(v).html());
            } else {
                $(v).removeClass('book-busy').addClass('book-free').html('<span>Доступна</span>');
                console.log('unreserve' + $(v).html());
            }
        });
	}
	// резервирование книги/снятие с резервации
	function bookBook(id,action,rc,ac,gridId){
		$.post(
			'/site/book',
			{
				'id':id,
				'action':action
			},
			function(data){
				//$('[data-id="'+id+'"]').html(data).removeClass(rc).addClass(ac);
				//$.pjax.reload({container:"#"+gridId});
				changeBookStatus(id);
			},
			'html'
		);
		return false;
	}
	// взятие книги
	$(document).on('click','.book-free',function(){
		// отображение всплывающего окна для работы с книгой
		$('#modal-popup').modal('show').find('#modal-book-content').load('site/modal?id='+$(this).attr('data-id'));
	});
	// клик по строке в списке книг
	//$('.tab-pane-row').click(function(e){
	$(document).on('click','.tab-pane-row',function(e){
		console.log('click');
		var bb=$(this).find('.book-busy');
		if(
			//$(e.target).hasClass('book-busy') ||
			$(e.target).parent().hasClass('book-busy')
        ){
            console.log('unreserve from admin');
			bookBook($(bb).attr('data-id'),'unreserve','book-busy','book-free',$(this).parent().parent().parent().parent().attr('id'));
			var td = $(e.target).parent().parent().children();
			$.each(td, function(i,v){
				if (i == 2) {
					// очищаем читателя для только что сданной книги
					$(v).html('');
				}
				if (i === 3) {
					$(v).text('');
				}
				if (i == 4) {
					//$(v).removeClass('book-busy').addClass('book-free').find('span').html('Доступна');
				}
			});
		} else {
			var id = $(this).find('td:last').attr('data-id');
			$('#modal-popup')
				.modal('show')
				.find('#modal-book-content')
				.load('site/modal?id='+id+'&g='+$(this).parent().parent().parent().parent().attr('id'));
			$('#modal-popup')
				.find('.modal-header h2')
				.text('Работа с книгой');
		}
	});
	var categoryName='';
	$(document).on('click','.editable-field span',function(){
		var s=$(this);
		var p=s.parent();
		var data=s.text();
		categoryName=data;
		s.remove();
		p.html('<input class="editable-input" type="text" value="'+data+'" />');
		p.find('input.editable-input').focus().select();
	});
	$(document).on('blur','.editable-field input',function(){
		var i=$(this);
		var p=i.parent();
		var data=i.val();
		r=true;
		if(data==''){
			var r=confirm('Вы действительно хотите удалить категорию?');
		}
		if(r){
			$.post(
				'/site/addcategory',
				{
					i:p.parent().find('td:first').text(),
					n:data,
				},
				function(data){
					$.pjax.reload({container:"#add-category-grid"});
				}
			);
		}else{
			data=categoryName;
			categoryName='';
		}
		i.remove();
		p.html('<span>'+data+'</span>');
	});
	$('.add-category').click(function(){
		var s=$('#add-category-grid table tr:last');
		s.after('<tr data-key="'+(parseInt(s.attr('data-key'))+1)+'">'+s.html()+'</tr>');
		var s=$('#add-category-grid table tr:last');
		s.find('td:first').text('');
	});
	$(document).on('change','#book-startdate',function(){
		if($(this).val()==''){
			$('#book-enddate').val('');
			return;
		}
		var t=Date.parse($(this).val());
		var r=$('#reading-time').val() * 24 * 3600 * 1000;
		var d=new Date(r+t);
		$('#book-enddate').val(d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate());
	});
});