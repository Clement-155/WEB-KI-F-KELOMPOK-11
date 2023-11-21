@extends('auth.dashboard')

@section('content')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Share Files Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body style="background: lightgray">

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

                        
                        <form class="mb-4"action="{{ route('share.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="font-weight-bold">Choose File</label>
                                <select name="sharefile" id="sharefile">
                                     @foreach($files as $file)
                                    <option value="{{ $file->id }}">{{ $file->private_file }}</option>
                                    @endforeach
                                </select>
                                <label for="user">Select User:</label>
                                <select name="user" id="user">
                                @foreach($users as $user)
                                 <option value="{{ $user->id }}">{{ $user->username }}</option>
                                @endforeach
                                 </select>
                            </div>
                            <button type="submit" class="btn btn-md btn-primary">SHARE</button>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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

</body>
@endsection