@extends('auth.dashboard')

@section('content')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Private Files Dashboard</title>
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

                        
                        <form class="mb-4"action="{{ route('privatefiles.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="font-weight-bold">Upload File (pdf, doc, docx, xls,xlsx, and video)</label>
                                <input type="file" class="form-control @error(Auth::user()->id) is-invalid @enderror" name="private_file">

                                <!-- error message untuk title using username -->
                                @error(Auth::user()->id)
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-md btn-primary">SIMPAN</button>
                            <button type="reset" class="btn btn-md btn-warning">RESET</button>

                        </form>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" class="col-4">Time Uploaded</th>
                                    <th scope="col" class="col-8">File Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($privateFiles as $file)
                                <tr>
                                    <td>{{ $file->created_at }}</td>
                                    <!-- Builds file path for download -->
                                    <td><a href="{{ Storage::url('private/privatefiles/' . (Auth::user()->username) . '/' . ($file->private_file)) }}" download>{!! $file->private_file !!}</a></td>
                                    <td class="text-center">
                                        <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{ route('privatefiles.destroy', $file->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">HAPUS</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <div class="alert alert-danger">
                                    Data Post belum Tersedia.
                                </div>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $privateFiles->links() }}
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
            error ') }}', 'ERROR : File Failed to Upload!');

        @endif
    </script>

</body>
@endsection