<table border="0" width="100%" style="background: #eeeeee">
    <tr>
        <td>
            <table border="0" width="90%" align="center"
                style="table-layout: fixed;border-collapse: collapse; background: #ffffff;max-width:600px">
                @yield('header', \Illuminate\Support\Facades\View::make('emails.header'))
                <tr>
                    <td style="width:100%;padding:20px;font-size: 14px;color:#555555">
                        @yield('content')
                    </td>
                </tr>
                @yield('more-content')
                @yield('thankyou', \Illuminate\Support\Facades\View::make('emails.thankyou'))
            </table>
        </td>
    </tr>
    @yield('footer', \Illuminate\Support\Facades\View::make('emails.footer'))
</table>
