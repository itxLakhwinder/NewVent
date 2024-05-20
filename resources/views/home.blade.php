@extends('layouts.app')

@section('content')
@section('styles')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" />
<style type="text/css">
    
    /*
     * Off Canvas at medium breakpoint
     * --------------------------------------------------
     */
        
    @media screen and (max-width: 48em) {
      .row-offcanvas {
        position: relative;
        -webkit-transition: all 0.25s ease-out;
        -moz-transition: all 0.25s ease-out;
        transition: all 0.25s ease-out;
      }
      .row-offcanvas-left .sidebar-offcanvas {
        left: -33%;
      }
      .row-offcanvas-left.active {
        left: 33%;
        margin-left: -6px;
      }
      .sidebar-offcanvas {
        position: absolute;
        top: 0;
        width: 33%;
        height: 100%;
      }
    }
    /*
     * Off Canvas wider at sm breakpoint
     * --------------------------------------------------
     */
    
    @media screen and (max-width: 34em) {
      .row-offcanvas-left .sidebar-offcanvas {
        left: -45%;
      }
      .row-offcanvas-left.active {
        left: 45%;
        margin-left: -6px;
      }
      .sidebar-offcanvas {
        width: 45%;
      }
    }
    
    .card {
      overflow: hidden;
    }
    
    .card-block .rotate {
      z-index: 8;
      float: right;
      height: 100%;
    }
    
    .card-block .rotate i {
      color: rgba(20, 20, 20, 0.15);
      position: absolute;
      left: 0;
      left: auto;
      right: -10px;
      bottom: 0;
      display: block;
      -webkit-transform: rotate(-44deg);
      -moz-transform: rotate(-44deg);
      -o-transform: rotate(-44deg);
      -ms-transform: rotate(-44deg);
      transform: rotate(-44deg);
    }
    .main-box {
        margin-bottom: 30px;
    }
</style>
@endsection
<section class="wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa fa-bars"></i> Dashboard</h3>
        </div>
        <!--  -->
        <div class="col-md-9 col-lg-10 main">
            <div class="row mb-3">
                <!-- <div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-success">
                        <div class="card-block bg-success">
                            <div class="rotate">
                                <i class="fa fa-list fa-5x"></i>
                            </div>
                            <h6 class="text-uppercase">Posts by group</h6>
                            <h1 class="display-1">134</h1>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-danger">
                        <div class="card-block bg-danger">
                            <div class="rotate">
                                <i class="fa fa-list fa-4x"></i>
                            </div>
                            <h6 class="text-uppercase">Posts by mental health type</h6>
                            <h1 class="display-1">87</h1>
                        </div>
                    </div>
                </div> -->
                <div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-warning">
                        <div class="card-block bg-warning">
                            <div class="rotate">
                                <i class="fa fa-user fa-5x"></i>
                            </div>
                            <h6 class="text-uppercase">Total number of users</h6>
                            <h1 class="display-1">{{@$users}}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-warning">
                        <div class="card-block bg-warning">
                            <div class="rotate">
                                <i class="fa fa-user fa-5x"></i>
                            </div>
                            <h6 class="text-uppercase">Total number of posts</h6>
                            <h1 class="display-1">{{@$topics}}</h1>
                        </div>
                    </div>
                </div>
                <!--<div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-info">
                        <div class="card-block bg-info">
                            <div class="rotate">
                                <i class="fa fa-list fa-5x"></i>
                            </div>
                            <h6 class="text-uppercase">Today posts</h6>
                            <h1 class="display-1">{{$todaytopics}}</h1>
                        </div>
                    </div>
                </div>-->
                
                <div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-success">
                        <div class="card-block bg-success">
                            <div class="rotate">
                                <i class="fa fa-share fa-5x"></i>
                            </div>
                            <h6 class="text-uppercase">Daily posts</h6>
                            <h1 class="display-1">{{$todaytopics}}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-success">
                        <div class="card-block bg-success">
                            <div class="rotate">
                                <i class="fa fa-share fa-5x"></i>
                            </div>
                            <h6 class="text-uppercase">Weekly posts</h6>
                            <h1 class="display-1">{{$weeks}}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-success">
                        <div class="card-block bg-success">
                            <div class="rotate">
                                <i class="fa fa-share fa-5x"></i>
                            </div>
                            <h6 class="text-uppercase">Monthly posts</h6>
                            <h1 class="display-1">{{$month}}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-danger">
                        <div class="card-block bg-danger">
                            <div class="rotate">
                                <i class="fa fa-list fa-4x"></i>
                            </div>
                            <h6 class="text-uppercase">Total comments</h6>
                            <h1 class="display-1">{{$comments}}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-danger">
                        <div class="card-block bg-danger">
                            <div class="rotate">
                                <i class="fa fa-list fa-4x"></i>
                            </div>
                            <h6 class="text-uppercase">Questionnaire filled</h6>
                            <a href="/users?q=qfilled">
                                <h1 class="display-1">{{$answers}}</h1>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-warning">
                        <div class="card-block bg-warning">
                            <div class="rotate">
                                <i class="fa fa-user fa-5x"></i>
                            </div>
                            <h6 class="text-uppercase">Average age on the app</h6>
                            <h1 class="display-1">{{@$averageage}}</h1>
                        </div>
                    </div>
                </div>
				<div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-warning">
                        <div class="card-block bg-warning">
                            <div class="rotate">
                               <i class="fa fa-list fa-4x"></i>
                            </div>
                            <h6 class="text-uppercase">Text Posts</h6>
                            <h1 class="display-1">{{@$text}}</h1>
                        </div>
                    </div>
                </div>
				<div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-success">
                        <div class="card-block bg-success">
                            <div class="rotate">
                               <i class="fa fa-list fa-4x"></i>
                            </div>
                            <h6 class="text-uppercase">Video Posts</h6>
                            <a href="/topics?q=video">
                                <h1 class="display-1">{{@$video}}</h1>
                            </a>
                        </div>
                    </div>
                </div>
				<div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-warning">
                        <div class="card-block bg-warning">
                            <div class="rotate">
                                <i class="fa fa-list fa-4x"></i>
                            </div>
                            <h6 class="text-uppercase">Audio Posts</h6>
                            <a href="/topics?q=audio">
                                <h1 class="display-1">{{@$audio}}</h1>
                            </a>
                        </div>
                    </div>
                </div>
				<div class="col-xl-3 col-lg-6 main-box">
                    <div class="card card-inverse card-warning">
                        <div class="card-block bg-warning">
                            <div class="rotate">
                                <i class="fa fa-list fa-4x"></i>
                            </div>
                            <h6 class="text-uppercase">Daily average time users are on the app</h6>
                            <h1 class="log-info">{{@$tot_log_avg}}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  -->
    </div>
    
</section>

@endsection
