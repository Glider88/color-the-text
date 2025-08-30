@extends('color.app')

@section('title', 'Текст')

@php
    $cfg = app()->get(\MercureConfig::class);
@endphp

@section('script')
    <script>
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

        function splitSentence(separators, str) {
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

            return '$1$2$3';
        }

        document.addEventListener('DOMContentLoaded', function () {
            let content = document.getElementById("content").innerHTML.replaceAll("\\n", "")
            console.log('div.content: ', content)

            let sentences = splitSentence(["</p>", ".", "?", "!"], content)
            let currentSentence = 0;
            function processWord(json) {
                const data = JSON.parse(json)
                const word = data.word

                const lastSentence = sentences.length - 1
                for (let i = currentSentence; i <= lastSentence; i++) {
                    const sentence = sentences[i]
                    console.log('sentence: ', sentence)
                    console.log('word: ', word)
                    const find = sentence.match(new RegExp(`[\\P{L}](${word})[\\P{L}]`, 'igu'))
                    console.log('find: ', find)
                    if (find !== null && find.length > 0) {
                        currentSentence = i
                        sentences[i] = sentence.replace(
                            new RegExp(`([\\P{L}])(${word})([\\P{L}])`, 'igu'),
                            spanColor(data.type)
                        )
                        console.log('updated sentence: ', sentences[i])
                        break
                    }
                }
            }

            const url = new URL('{{ $cfg->url }}')
            url.searchParams.append('topic', '{{ $cfg->topic }}')

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
@endsection

@section('style')
    <style></style>
@endsection

@section('body')
    {!! $content !!}
@endsection
