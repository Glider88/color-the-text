@extends('color.app')

@section('title', 'Текст')

@section('script')
@verbatim
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function splitAndAppend(separator, str) {
                if (! str.includes(separator)) {
                    return [str];
                }

                let result = str.split(separator)
                for (let i = 0; i < result.length - 1; i++) {
                    result[i] = result[i] + separator
                }

                const last = result.pop()
                if (last !== '') {
                    result.push(last)
                }

                return result
            }

            function splitAndAppendForArray(separator, arrayOfStrings) {
                return arrayOfStrings.flatMap((str) => splitAndAppend(separator, str));
            }

            function split(separators, str) {
                if (separators.length === 0) {
                    return [str]
                }

                const firstSeparator = separators.shift();
                let result = splitAndAppend(firstSeparator, str);

                result = separators.reduce(
                    (acc, separator) => splitAndAppendForArray(separator, acc),
                    result,
                )

                return result;
            }

            function spanColor(number) {
                if (number === 1) {
                    return '$1<span style="color: green;">$2</span>$3';
                }

                if (number === 2) {
                    return '$1<span style="color: red;">$2</span>$3';
                }

                return '$2';
            }

            let content = document.getElementById("content").innerHTML.replaceAll("\\n", "")
            console.log('div.content: ', content)

            var sentences = split(["</p>", ".", "?", "!"], content)
            // console.log(sentences)
            const lastSentence = sentences.length - 1
            // console.log(lastSentence)
            var currentSentence = 0;

            function processWord(json) {
                const data = JSON.parse(json)
                const word = data.word

                const re1 = new RegExp(`[\\P{L}](${word})[\\P{L}]`, 'igu')
                const re2 = new RegExp(`([\\P{L}])(${word})([\\P{L}])`, 'igu')
                console.log('re1', re1)
                console.log('re2', re2)

                for (let i = currentSentence; i <= lastSentence; i++) {
                    const sentence = sentences[i]
                    console.log('sentence: ', sentence)
                    const find = sentence.match(re1)
                    console.log('find: ', find)
                    if (find !== null && find.length > 0) {
                        currentSentence = i
                        sentences[i] = sentence.replace(re2, spanColor(data.type))
                        console.log('updated sentence: ', sentences[i])
                        break
                    }
                }
            }

            const url = new URL('http://127.0.0.1:8000/.well-known/mercure')
            url.searchParams.append('topic', 'color-the-text')

            const eventSource = new EventSource(url)

            eventSource.onopen = function () {
                console.log('eventSource is connected')
            };

            eventSource.onmessage = function (event) {
                console.log('es get: ', event)
                processWord(event.data)
                const newContent = sentences.join('')
                console.log('newContent: ', newContent)
                document.getElementById("content").innerHTML = newContent
            };

            eventSource.onerror = function (error) {
                console.error('EventSource failed:', error)
            };
        });
    </script>
@endverbatim
@endsection

@section('style')
    <style></style>
@endsection

@section('body')
    {!! $content !!}
{{--    {{ $content }}--}}
@endsection
