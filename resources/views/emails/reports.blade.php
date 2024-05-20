<p style="color:#3d4852;font-size: 16px;line-height:1.5em;"><h4>Hi, Team</h4></p>
<p style="color:#3d4852;font-size: 16px;line-height:1.5em;">User: {{@$user->name}} </p>
<p style="color:#3d4852;font-size: 16px;line-height:1.5em;">Subject: {{@$report->subject}}  </p>
@if(isset($report->topic))
<p style="color:#3d4852;font-size: 16px;line-height:1.5em;">Topic: <a href="{{url('topic')}}/{{$report->topic->id}}" target="_blank">{{@$report->topic->title}}</a></p>
@endif
<p style="color:#3d4852;font-size: 16px;line-height:1.5em;">Detail: {{@$report->detail}}  </p>
@if(@$report->image)
	<p style="color:#3d4852;font-size: 16px;line-height:1.5em;">File:  
		<a href="{{asset('uploads/reports/')}}/{{$report->image}}" target="_blank">View</a>
	</p>
@endif