@extends('auth.dashboard')

@section('content')
<main class="signup-form">
    <div class="cotainer">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <h3 class="card-header text-center">Register User</h3>
                    <div class="card-body">

                        <form action="{{ route('register.custom') }}" method="POST" enctype="multipart/form-data">

                            @csrf

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Id Card Image</label>
                                <input type="file" class="form-control @error('id-photo') is-invalid @enderror" name="id-photo">

                                <!-- error message untuk Picture -->
                                @error('id-photo')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <input type="text" placeholder="Username" id="username" class="form-control  @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autofocus>
                                @if ($errors->has('username'))
                                <span class="text-danger">{{ $errors->first('username') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <input type="password" placeholder="Password" id="password" class="form-control  @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" required>
                                @if ($errors->has('password'))
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <input type="text" placeholder="Fullname" id="fullname" class="form-control  @error('fullname') is-invalid @enderror" name="fullname" value="{{ old('fullname') }}" required autofocus>
                                @if ($errors->has('fullname'))
                                <span class="text-danger">{{ $errors->first('fullname') }}</span>
                                @endif
                            </div>
                            <!-- Gender Radio -->
                            <div class="form-group mb-3">
                                <p>Gender :</p>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="gender1" value="Male" required>
                                    <label class="form-check-label" for="gender1">
                                        Male
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="gender2" value="Female">
                                    <label class="form-check-label" for="gender2">
                                        Female
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <input type="text" placeholder="Citizenship" id="citizenship" class="form-control  @error('citizenship') is-invalid @enderror" name="citizenship" value="{{ old(key: 'citizenship') }}" required autofocus>
                                @if ($errors->has('citizenship'))
                                <span class="text-danger">{{ $errors->first('citizenship') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <input type="text" placeholder="Religion" id="religion" class="form-control @error('religion') is-invalid @enderror" name="religion" value="{{ old('religion') }}" required autofocus>
                                @if ($errors->has('religion'))
                                <span class="text-danger">{{ $errors->first('religion') }}</span>
                                @endif
                            </div>

                            <!-- Marital Status Radio -->
                            <div class="form-group mb-3">
                                <p>Marital Status :</p>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital-status" id="marital-status1" value="Married" required>
                                    <label class="form-check-label" for="marital-status1">
                                        Married
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital-status" id="marital-status2" value="Unmarried">
                                    <label class="form-check-label" for="marital-status2">
                                        Unmarried
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid mx-auto">
                                <button type="submit" class="btn btn-dark btn-block">Sign up</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection