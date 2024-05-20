@include('emails.header')
    <p>
        <h2>Hi {{@$user['first_name']}}</h2>
    </p>
    <p style="color:#000;font-size: 16px;line-height:22px;margin-bottom:0px">
        Thanks for signing up as a therapist to our VentSpace App platform. You’ll be notified if your account is approved by our admin team within 24-48 hours. Once approved, your information will show on our Mental Health Support Marketplace, within the app.
    </p>
    <p style="color:#000;font-size: 16px;line-height:22px;margin-bottom:0px">
        Haven’t downloaded the VentSpace App? Download it now: <a target="_blank" href="https://apps.apple.com/us/app/ventspace/id1514627232"> iOS </a> | <a target="_blank" href="https://play.google.com/store/apps/details?id=com.application.ventspace"> Android </a>
    </p>
@include('emails.footer')