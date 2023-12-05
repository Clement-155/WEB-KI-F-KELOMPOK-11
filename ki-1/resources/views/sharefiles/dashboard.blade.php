@extends('auth.dashboard')

@section('title', 'Share Files Dashboard')

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


                        <form class="mb-4" action="{{ route('share.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Choose File</label>
                                <select class="form-select" name="sharefile" id="sharefile">
                                    @foreach($files as $file)
                                    <option value="{{ $file->id }}">{{ $file->private_file }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="user">Select User:</label>
                                <select class="form-select" name="user" id="user">
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-grid mx-auto">
                                <button type="submit" class="btn btn-md btn-primary">SHARE</button>
                            </div>
                        </form>
                        @if(session('encrypted'))
                        <h4>File id</h4>
                        <p>{{ session('file_id') }}</p>
                        <h4>File Key</h4>
                        <p>{{ session('encrypted') }}</p>
                        @endif
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