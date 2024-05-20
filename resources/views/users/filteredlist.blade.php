    <div id="UserDataListing">
        <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
            <th>#</th>
            <th onclick="sortData('name')">Name <i class="fa fa-sort" aria-hidden="true" id="name"></i></th>
            <th onclick="sortData('email')">Email <i class="fa fa-sort" aria-hidden="true" id="email"></i></th>
            <th onclick="sortData('phone_number')">Phone number <i class="fa fa-sort" aria-hidden="true" id="phone_number"></i></th>
            <th onclick="sortData('address')">Address <i class="fa fa-sort" aria-hidden="true" id="address"></i></th>
            <th>Daily average time</th>
            <th>Questionnaire</th>
            <th>View</th>		  
            <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
            ?>
            @foreach($users as $user)
            <tr>
            <td>{{$i}}</td>
            <td>{{$user['name']}}</td>
            <td>{{$user['email']}}</td>
            <td>{{$user['phone_number']}}</td>
                <td>{{$user['address']}}</td>
                <td>{{@$user['time']}} Mins</td>
            <td>
                @if(@$user['answers_count'] && $user['answers_count'] > 0)
                Yes
                @else
                No
                @endif            
            </td>
            <td><a href="{{url('user')}}/{{$user['id']}}">View</a> </td>
            <td>
                @if($user['status'] == '1')
                <a href="{{url('user/enable')}}/{{$user['id']}}" onclick="return confirm('Are you sure?');">Enable</a>
				@elseif($user['status'] == '2')
				  	<a href="{{url('user/enable')}}/{{$user['id']}}" onclick="return confirm('Are you sure?');">Enable</a>
                @else
                <a href="{{url('user/disable')}}/{{$user['id']}}" onclick="return confirm('Are you sure?');">Disable</a>
                @endif
                |
                <a href="{{url('user/delete')}}/{{$user['id']}}" class="text-danger" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
            </tr>
            <?php 
            $i++;
            ?>
            @endforeach
        </tbody>
        </table>
        {!! $users->links() !!}
    </div>