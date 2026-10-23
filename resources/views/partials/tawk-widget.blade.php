@if(config('tawk.enabled') && config('tawk.property_id') && config('tawk.widget_id'))
    <script nonce="{{ request()->attributes->get('csp_nonce') }}">
        var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
        (function () {
            var script = document.createElement('script');
            var firstScript = document.getElementsByTagName('script')[0];

            script.async = true;
            script.src = 'https://embed.tawk.to/{{ config('tawk.property_id') }}/{{ config('tawk.widget_id') }}';
            script.charset = 'UTF-8';
            script.setAttribute('crossorigin', '*');
            firstScript.parentNode.insertBefore(script, firstScript);
        })();
    </script>
@endif
