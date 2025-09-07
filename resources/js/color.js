import axios from 'axios';

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
    // let content = document.getElementById("content").innerHTML.replaceAll("\\n", "")
    let content = document.getElementById("content").innerHTML
    // console.log('div.content: ', content)

    let sentences = splitSentence(["</p>", ".", "?", "!"], content)
    let currentSentence = 0;
    function processWord(json) {
        const data = JSON.parse(json)
        const word = data.word

        const lastSentence = sentences.length - 1
        for (let i = currentSentence; i <= lastSentence; i++) {
            const sentence = sentences[i]
            // console.log('sentence: ', sentence)
            // console.log('word: ', word)
            const find = sentence.match(new RegExp(`[\\P{L}](${word})[\\P{L}]`, 'igu'))
            // console.log('find: ', find)
            if (find !== null && find.length > 0) {
                currentSentence = i
                sentences[i] = sentence.replace(
                    new RegExp(`([\\P{L}])(${word})([\\P{L}])`, 'igu'),
                    spanColor(data.type)
                )
                // console.log('updated sentence: ', sentences[i])
                break
            }
        }
    }

    config.articles.forEach(article => {
        if (article.is_completed) {
            return
        }

        // defined in color/read.blade
        const url = new URL(config.mercure.url)
        url.searchParams.append('topic', config.mercure.topic + article.id)

        const eventSource = new EventSource(url)

        const esStr = 'es#' + article.id

        eventSource.onopen = function () {
            // console.log(esStr + ' is connected')
        };

        let start = false;
        eventSource.onmessage = function (event) {
            // console.log(esStr + ' get: ', event)
            if (event.data === 'start') {
                start = true;

                return
            }

            if (start && event.data === 'finish') {
                // console.log(esStr + ' completed')
                const content = document.getElementById("content").innerHTML
                axios.post(config.finish_url, {
                    id: article.id,
                    content: content,
                })
                    .then(function (response) {
                        //handle success
                        console.log(response);
                    })
                    .catch(function (response) {
                        //handle error
                        console.log(response);
                    });
            }

            if (event.data === 'finish') {
                // console.log(esStr + ' finished')
                eventSource.close()

                return
            }

            processWord(event.data)
            // const newContent = sentences.join('')
            // console.log(esStr + ' newContent: ', newContent)
            document.getElementById("content").innerHTML = newContent
        };

        eventSource.onerror = function (error) {
            console.error(esStr + ' failed:', error)
            eventSource.close();
        };
    })
});
