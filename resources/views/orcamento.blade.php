<!DOCTYPE html>
<html>

<head>
    <title>Cadastro de orçamentos</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.17/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.17/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.22/dataRender/datetime.js"></script>
</head>

<body class="bg-dark">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-5">
                    <div class="card-header">
                        <div class="col-md-12">
                            <h4 class="card-title">Cadastro de orçamentos
                                <a class="btn btn-success ml-5" href="javascript:void(0)" id="createNewItem"> Novo Orçamento</a>
                            </h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered data-table">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Descrição</th>
                                    <th>Valor Orçado</th>
                                    <th>Data</th>
                                    <th width="15%">Editar/Excluir</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal fade" id="ajaxModel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modelHeading"></h4>
                                </div>
                                <div class="modal-body">
                                    <form id="ItemForm" name="ItemForm" class="form-horizontal">
                                        <input type="hidden" name="Item_id" id="Item_id">
                                        <div class="form-group">
                                            <label for="name" class="col-sm-4 control-label">Cliente</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="cliente_modal" name="cliente_modal" placeholder="Enter Name" value="" maxlength="50" required="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="name" class="col-sm-4 control-label">Vendedor</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="vendedor_modal" name="vendedor_modal" placeholder="Enter Name" value="" maxlength="50" required="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Descrição</label>
                                            <div class="col-sm-12">
                                                <textarea name="descricao_modal" id="descricao_modal" required="" placeholder="Enter descriptions" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="name" class="col-sm-4 control-label">Valor Orçado</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="valor_modal" name="valor_modal" placeholder="Enter Name" value="" maxlength="50" required="">
                                            </div>
                                        </div>
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
    $(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            "order": [
                [5, "desc"]
            ],
            ajax: "{{ route('orcamentos.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'cliente',
                    name: 'cliente'
                },
                {
                    data: 'vendedor',
                    name: 'vendedor'
                },
                {
                    data: 'descricao',
                    name: 'descricao'
                },
                {
                    data: 'valor_orcado',
                    name: 'valor_orcado'
                },
                {
                    data: 'created_at',
                    name: 'created_at',

                },
                {

                    data: 'action',
                    name: 'action',
                    orderable: true,
                    searchable: true
                },
            ]
        });

        $('#createNewItem').click(function() {
            $('#saveBtn').val("create-Item");
            $('#Item_id').val('');
            $('#ItemForm').trigger("reset");
            $('#modelHeading').html("Novo orçamento");
            $('#ajaxModel').modal('show');
        });

        $('body').on('click', '.editItem', function() {
            var Item_id = $(this).data('id');
            //  console.log(Item_id);
            $.get("{{ route('orcamentos.index') }}" + '/' + Item_id + '/edit', function(data) {
                console.log(data)
                $('#modelHeading').html("Edit Item");
                $('#saveBtn').val("edit-user");
                $('#ajaxModel').modal('show');
                $('#Item_id').val(data.id);
                $('#cliente_modal').val(data.cliente);
                $('#vendedor_modal').val(data.vendedor);
                $('#valor_modal').val(data.valor_orcado);
                $('#descricao_modal').text(data.descricao);
            })
        });

        $('#saveBtn').click(function(e) {
            e.preventDefault();
            $(this).html('Sending..');

            $.ajax({
                data: $('#ItemForm').serialize(),
                url: "{{ route('orcamentos.store') }}",
                type: "POST",
                dataType: 'json',

                success: function(data) {
                    console.log(data);
                    $('#ItemForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    table.draw();

                },
                error: function(data) {
                    console.log('Error1:', data);
                    $('#saveBtn').html('Save Changes');
                }
            });
        });

        $('body').on('click', '.deleteItem', function() {

            var Item_id = $(this).data("id");
            confirm("Confirma a exclusão?");

            $.ajax({
                type: "DELETE",
                url: "{{ route('orcamentos.store') }}" + '/' + Item_id,
                success: function(data) {
                    table.draw();
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });

    });
</script>

</html>