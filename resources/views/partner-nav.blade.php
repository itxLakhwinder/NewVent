<div class="top_notification_t">
      <div class="right_div">
         <!-- <div class="notification">
            <img src="{{asset('partner_assets/images/notification.png')}}">
            <span class="message"></span>
            <div class="notification_msg">
              <p>notification-1</p>
              <p>notification-2</p>
              <p>notification-3</p>
            </div>
         </div> -->
         <div class="dropdown">
            <div class=" dropdown-toggle"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
               <img src="{{@$partner->logo ? $partner->logo : asset('img/default.png') }}">{{ Auth::user()->name }}
            </div>
            <!-- <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
               <li><a class="dropdown-item" href="{{ url('/partner-logout') }}">Logout</a></li>              
            </ul> -->
         </div>
      </div>
</div>