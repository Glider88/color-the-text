<html>
    <body>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const url = new URL('http://127.0.0.1:8000/.well-known/mercure');
                url.searchParams.append('topic', 'lab');

                const eventSource = new EventSource(url);

                eventSource.onopen = function () {
                    console.log('eventSource is connected');
                };

                eventSource.onmessage = function (event) {
                    console.log('eventSource getting new data');
                    console.log(event.data);
                };

                eventSource.onerror = function (error) {
                    console.error('EventSource failed:', error);
                };
            });
        </script>
        <h1>Lab</h1>
    </body>
</html>
