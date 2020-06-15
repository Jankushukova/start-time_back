@component('mail::message')
# Change password Request

Click on the button below to change password.

@component('mail::button', ['url' => config('frontend.url').config('frontend.reset_password_url').$token])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
