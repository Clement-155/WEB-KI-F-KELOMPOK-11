@extends('auth.dashboard')

@section('title', 'Private Files Dashboard')

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


                        <form class="mb-4" action="{{ route('privatefiles.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
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
                                    <td><a href="{{ '/download/' . ($file->private_file) }}">{!! $file->private_file !!}</a></td>
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
        error ') }}', 'ERROR : File Failed to Upload!');

    @endif
</script>
@endsection