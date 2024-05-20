<div class="left_bar">
   <div class="brand_logo">
      <a href="#"><img src="{{asset('partner_assets/images/logo.png')}}"></a>
      <div class="menu_toogle">
        <div class="toggle">
            <img src="/public/partner_assets/images/menu.png">
        </div>
    </div>
   </div>
   <div class="left_links">
      <a href="{{url('/partner-analytics')}}" class="{{ request()->is('partner-analytics') ? 'active' : '' }}"
>Analytics</a>
      <a href="{{url('/partner-profile')}}" class="{{ request()->is('partner-profile') ? 'active' : '' }}"
>Profile</a>
      <a href="{{url('/partner-billing')}}" class="{{ request()->is('partner-billing') ? 'active' : '' }}"
>Billing</a>
      <a href="{{url('/partner-account')}}" class="{{ request()->is('partner-account') ? 'active' : '' }}"
>Account</a>
<a href="{{url('/partner-logout')}}" class="{{ request()->is('partner-logout') ? 'active' : '' }}"
>Log Out</a>
   </div>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
   <script>
    $(document).ready(function() {
        $(".toggle img").click(function() {
            $(".left_links").toggleClass("show");
        })
    });
</script>
</div>
