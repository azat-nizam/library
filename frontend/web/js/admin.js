$(document).ready(function(){
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
				$.pjax.reload({container:"#"+gridId});
			},
			'html'
		);
	}
	// взятие книги
	$(document).on('click','.book-free',function(){
		// отображение всплывающего окна для работы с книгой
		$('#modal-popup').modal('show').find('#modal-book-content').load('site/modal?id='+$(this).attr('data-id'));
	});
	// клик по строке в списке книг
	//$('.tab-pane-row').click(function(e){
	$(document).on('click','.tab-pane-row',function(e){
		var bb=$(this).find('.book-busy');
		if($(e.target).hasClass('book-busy')){
			bookBook($(bb).attr('data-id'),'unreserve','book-busy','book-free',$(this).parent().parent().parent().parent().attr('id'));
		}else{
			$('#modal-popup').modal('show').find('#modal-book-content').load('site/modal?id='+$(this).find('td:last').attr('data-id')+'&g='+$(this).parent().parent().parent().parent().attr('id'));
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
		console.log('on change');
	});
})