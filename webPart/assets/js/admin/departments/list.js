function deleteDepartment(ele) {
    const row = ele.closest('*[data-dept-id]');
    const id = row.data('dept-id');
    
    Notiflix.Confirm.Show( 
        'Удаление',
        'Вы уверены, что хотите удалить выбранный департамент?',
        'Да',
        'Нет',
        () => { 
            $.post( SITE + "api/admin/departments/delete", { id: id }, ( data ) => {
                if(data.status == 'success') {
                    Notiflix.Notify.Success(data.message)
                    row.remove()
                } else {
                    Notiflix.Notify.Failure(data.message)
                }
              }, "json");
        })
}

$('*[data-action="remove-dept"]').on('click', function() {
    deleteDepartment($(this));
  });
