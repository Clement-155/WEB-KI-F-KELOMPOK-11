@extends('auth.dashboard')

@section('title', 'Profile')

@section('content')
<section class="section about-section gray-bg" id="about">
    <div class="container">
        <div class="row align-items-center">
            <div class="card mb-3" style="max-width: 540px;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="{{ Storage::url('id-card/' . $userProfile["id-card"])}}" class="col-6" title="" alt="">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <div><strong>Full Name:</strong></div>
                                    <div>{{ $userProfile["fullname"] }}</div>
                                </li>
                                <li class="list-group-item">
                                    <div><strong>Gender:</strong></div>
                                    <div>{{ $userProfile["gender"] }}</div>
                                </li>
                                <li class="list-group-item">
                                    <div><strong>Citizenship:</strong></div>
                                    <div>{{ $userProfile["citizenship"] }}</div>
                                </li>
                                <li class="list-group-item">
                                    <div><strong>Religion:</strong></div>
                                    <div>{{ $userProfile["religion"] }}</div>
                                </li>
                                <li class="list-group-item">
                                    <div><strong>Marital Status:</strong></div>
                                    <div>{{ $userProfile["marital"] }}</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection