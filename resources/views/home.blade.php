@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif

            @if(session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">Contacts
                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#myModal" id="open">Add Contact</button>
                    <button type="button" class="btn btn-success float-right" style="margin-right:5px;" data-toggle="modal" data-target="#myUploadModal" id="uploadOpen">Upload Contact CSV</button>
                    <button type="button" class="btn btn-info float-right" style="margin-right:5px;">Track Klavio</button> 
                    <form method="post" action="{{url('test')}}" id="form">
                        @csrf
                        <!-- Modal -->
                        <div class="modal" tabindex="-1" role="dialog" id="myModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="alert alert-danger" style="display:none"></div>
                                <div class="modal-header">
                                    <h5 class="modal-title">Create Contact</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                        <label for="Name">Full Name:</label>
                                        <input type="text" class="form-control" name="full_name" required id="full_name">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="email">Email Address:</label>
                                            <input type="email" class="form-control" name="email" required id="email">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="phone">Phone:</label>
                                            <input type="text" class="form-control" name="phone" id="phone">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button  class="btn btn-success" id="ajaxSubmit">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <form class="form-horizontal" method="POST" action="{{ route('contacts.upload') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <!-- Modal -->
                        <div class="modal" tabindex="-1" role="dialog" id="myUploadModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="alert alert-danger" style="display:none"></div>
                                <div class="modal-header">
                                    
                                    <h5 class="modal-title">Upload CSV</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
                                        <label for="csv_file" class="col-md-4 control-label">Contact CSV file</label>

                                        <div class="col-md-6">
                                            <input id="csv_file" type="file" class="form-control" name="csv_file" required>

                                            @if ($errors->has('csv_file'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('csv_file') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Upload</button>
                                </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Created By</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  $(function () {
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('contact.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'full_name', name: 'full_name'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'created_by', name: 'created_by'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    jQuery('#ajaxSubmit').click(function(e){
        $("input").removeClass("errors");
        e.preventDefault();
        jQuery.ajax({
            url: "{{ url('/contacts') }}",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            method: 'post',
            data: {
                full_name: jQuery('#full_name').val(),
                email: jQuery('#email').val(),
                phone: jQuery('#phone').val()
            },
            success: function(result){
            if(result.errors)
            {
                console.log(result.errors);
                jQuery('.alert-danger').html('');

                jQuery.each(result.errors, function(key, value){
                    $("#"+key).addClass('errors');
                    jQuery('.alert-danger').show();
                    jQuery('.alert-danger').append('<li>'+value+'</li>');
                });
            }
            else
            {
                jQuery('.alert-danger').hide();
                $('#open').hide();
                $('#myModal').modal('hide');
                location.reload();
            }
        }});
    });
    
  });
</script>
@endsection