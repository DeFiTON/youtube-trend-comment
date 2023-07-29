</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"  crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>


<script type="text/javascript">

    $(document).ready(function() {
        let table_action = $('.dataTable').data('table');
        $('.dataTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": 'index.php?action=' + table_action,
            "pagingType": "full_numbers",
            "stateSave": true,
            // "paging": false,
            "order": [[0, "desc"]],
            "language": {
                "lengthMenu": "Показывать _MENU_ записей на странице",
                "zeroRecords": "Ничего не найдено",
                "info": "Показана страница _PAGE_ из _PAGES_",
                "infoEmpty": "Данные отсутствуют",
                "infoFiltered": "(Отфильтровано _MAX_ записей)"
            }
        });

        $('.toggle_comment').on('click', function () {
            let id = $(this).data('id');
            $.ajax( {
                type: "POST",
                data: 'action=ajaxCommentToggleStatus&id='+id,
                success: function( response ) {
                    console.log('success');
                }
            });
        });
    });
</script>
</body>
</html>