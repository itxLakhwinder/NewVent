<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{@$topic->title}}</title>	
	<meta name="description" content="{{@$topic->details}}">
	@if(@$topic->content_type == 'video')
		<link itemprop="thumbnailUrl" href="{{asset('img/vedio-icon.png')}}"> 
	@endif	 
	@if(@$topic->content_type == 'audio')
		<link itemprop="thumbnailUrl" href="{{asset('img/vent-audio.png')}}">  
	@endif
    
	
	<meta property="og:site_name" content="{{@$topic->title}}">
	<meta property="og:title" content="{{@$topic->title}}" />
	<meta property="og:description" content="{{@$topic->details}}" />
	<meta property="og:url" content="https://dev.ventspaceapp.com/topic-share/{{@$topic->id}}" />
	@if(@$topic->content_type == 'video')		
		<meta property="og:image" itemprop="image" content="{{asset('img/vedio-icon.png')}}" />
	@endif	 
	@if(@$topic->content_type == 'audio')
		<meta property="og:image" itemprop="image" content="{{asset('img/vent-audio.png')}}" />
	@endif
											
	
	<meta property="og:type" content="website" />
	<meta property="og:locale" content="en_GB" />

	<!-- Twitter Meta Tags -->   
	<meta name="twitter:title" content="{{@$topic->title}}">
	<meta property="twitter:description" content="{{@$topic->details}}" />
	@if(@$topic->content_type == 'video')		
		<meta name="twitter:image" content="{{asset('img/vedio-icon.png')}}">
	@endif	 
	@if(@$topic->content_type == 'audio')		
		<meta name="twitter:image" content="{{asset('img/vent-audio.png')}}">
	@endif
			
	
	
    <link href="{{asset('partner_assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('partner_assets/css/style.css')}}" rel="stylesheet">


    <!-- HTML5 shim and Respond.js IE8 support of HTML5 -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
     @yield('styles', '')
</head>
<body>
   <div class="vent_space_header">
 <div class="container">
    <div class="brand_logo">
       <a href="#"><img src="{{asset('partner_assets/images/logo.png')}}"></a>
    </div>
 </div>
</div>
<div class="form_section">
 <div class="sign_up login_inputs">	 
     <h3 class="h3_tittle">{{@$topic->title}}</h3>
	 <p class="">{{@$topic->details}}</p>
	 @if(@$topic->content_type == 'video')
	 	<video width="600" height="400" controls>
		 	<source src="https://dev-vent.s3.amazonaws.com/{{@$topic->image}}" type="video/mp4">
		 	Your browser does not support the video tag.
		</video>
	 @endif
	 @if(!@$topic->content_type)
	 	<img src="{{asset('partner_assets/images/logo.png')}}" />
	 @endif
	 @if(@$topic->content_type == 'audio')
	 	<audio controls>		  
		  <source src="https://dev-vent.s3.amazonaws.com/{{@$topic->image}}" type="audio/mpeg">
		Your browser does not support the audio element.
		</audio>
	 @endif
 </div>
</div>

</body>
    <script src="{{asset('partner_assets/js/bootstrap.bundle.min.js')}}" ></script>
    <script src="{{asset('partner_assets/js/jquery.min.js')}}" ></script>
    @yield('scripts', '')
</html>
