@include('auth.dashboard')

@section('content')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
</head>

<body>
<section class="section about-section gray-bg" id="about">
            <div class="container">
                <div class="row align-items-center flex-row-reverse">
                    <div class="col-lg-6">
                        <div class="about-text go-to">
                            <h3 class="dark-color">{{ $userProfile["fullname"] }}</h3>
                            <div class="row about-list">
                                <div class="col-md-6">
                                    <div class="media">
                                        <label>Gender : </label>
                                        <p>{{ $userProfile["gender"] }}</p>
                                    </div>
                                    <div class="media">
                                        <label>Citizenship : </label>
                                        <p>{{ $userProfile["citizenship"] }}</p>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="media">
                                        <label>Religion : </label>
                                        <p>{{ $userProfile["religion"] }}</p>
                                    </div>
                                    <div class="media">
                                        <label>Marital Status : </label>
                                        <p>{{ $userProfile["marital"] }}</p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-avatar">
                            <img src="{{ Storage::url('id-card/' . $userProfile["id-card"])}}" class="col-6"title="" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>
</body>