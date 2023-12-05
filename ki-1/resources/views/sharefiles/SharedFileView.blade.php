@extends('auth.dashboard')

@section('title', 'Download Shared Files')

@section('content')
<section>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div>
                    <h3 class="text-center my-4">Welcome : {{Auth::user()->username}}</h3>
                    <hr>
                </div>
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <!-- Upload Button -->


                        <form class="mb-4" action="{{ route('download-shared') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group mb-3">
                                <label>File ID</label>
                                <input type="text" placeholder="File ID" id="file_id" class="form-control" name="file_id" required autofocus>
                                @if ($errors->has('file_id'))
                                <span class="text-danger">{{ $errors->first('file_id') }}</span>
                                @endif
                            </div>

                            
                            <div class="form-group mb-3">
                                <label>Owner Name</label>
                                <input type="text" placeholder="File Owner's Name" id="owner_name" class="form-control" name="owner_name" required autofocus>
                                @if ($errors->has('owner_name'))
                                <span class="text-danger">{{ $errors->first('owner_name') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <label>File Key</label>
                                <input type="file" placeholder="File Key" id="file_key" class="form-control" name="file_key" required>
                                @if ($errors->has('file_key'))
                                <span class="text-danger">{{ $errors->first('file_key') }}</span>
                                @endif
                            </div>

                            <div class="d-grid mx-auto">
                                <button type="submit" class="btn btn-dark btn-block">Download</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    //message with toastr
    @if(session() -> has('success'))

    toastr.success('{{ session('
        success ') }}', 'Upload Success!');

    @elseif(session() -> has('logsuccess'))

    toastr.success('{{ session('
        success ') }}', 'Login Success!');

    @elseif(session() -> has('failed'))

    toastr.error('{{ session('
        error ') }}', 'ERROR : File Failed to Share!');

    @endif
</script>
@endsection