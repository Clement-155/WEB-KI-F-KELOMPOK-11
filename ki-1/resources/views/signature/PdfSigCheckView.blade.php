@extends('auth.dashboard')

@section('title', 'Check PDF Signature')

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
                        @if(session('result'))
                        <h2>{{ session('result') }}</h2>
                        @endif

                        <form class="mb-4" action="{{ route('pdf-check-signature') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Upload PDF File</label>
                                <input type="file" class="form-control @error(Auth::user()->id) is-invalid @enderror" name="private_file">

                                <!-- error message untuk title using username -->
                                @error(Auth::user()->id)
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="user">Select Owner:</label>
                                <select class="form-select" name="owner" id="owner">
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-grid mx-auto">
                                <button type="submit" class="btn btn-dark btn-block">Check</button>
                            </div>
                        </form>
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