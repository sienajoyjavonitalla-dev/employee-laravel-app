@extends('layouts.app')
@section('styles')
<style>
    
    .notes-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr); /* Creates 5 columns */
        grid-gap: 10px; /* Adds spacing between notes */
    }

    .note {
        background-color: #ffebcc;
        border: 1px solid #ffc966;
        border-radius: 5px;
        padding: 10px;
    }

    .note-actions {
        display: flex;
        justify-content: flex-end;
        align-items: flex-end;
    }
</style>
@stop
@section('content')
    <div class="container">
        <h1>Sticky Notes</h1>
        <a class="btn btn-success mb-4" href="javascript:void(0)" id="createNew"> Create New </a>

        <div class="row">

            <div class="col-md-12">
                <div class="notes-container">
                    @foreach($notes as $note)
                        <div class="note" id="note-{{ $note->id }}">
                            
                            <div class="note-header">
                                <h3>{{ $note->title }}</h3>
                                
                            </div>
                            <p>{{ $note->content }}</p>
                            <div class="note-actions">
                                <a href="javascript:void(0)" data-id="{{ $note->id }}" class="btn text-primary btn-xs editNote"><i class="fas fa-pen"></i></a>
                                <form action="{{ route('notes.destroy', $note->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-danger btn btn-xs" onclick="return confirm('Are you sure you want to delete this note?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- <div class="col-md-4">
                <div class="note-form">
                    <h2>Create New Note</h2>
                    <form action="{{ route('notes.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea name="content" id="content" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Save Note</button>
                    </form>
                </div>
            </div> -->
        </div>
    </div>
    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>

                <div class="modal-body">
                    <form id="noteForm" action="{{ route('notes.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="id">

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control" required value="">
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea name="content" id="content" class="form-control" rows="4" required value=""></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Save Note</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(document).ready(function() {
        $('#createNew').click(function () {
            $('#saveBtn').val("create-note");
            $('#id').val('');

            $('#noteForm').trigger("reset");
            $('#modelHeading').html("Create New");
            $('#ajaxModel').modal('show');

        });
        $('.editNote').click(function () {
            var id = $(this).data('id');

            $.get("{{ route('notes.index') }}" +'/' + id +'/edit', function (data) {
                $('#modelHeading').html("Edit Note");
                $('#saveBtn').val("edit-note");
                $('#ajaxModel').modal('show');
                $('#id').val(data.id);
                $('#title').val(data.title);
                $('#content').val(data.content);
            })
        });

        $('.note').draggable({
            containment: '#notes-container',
        });
    });
</script>
@stop
