import axios from 'axios'

interface ConfigItem {
    id: number
    sse_url: string
    topic: string
}

interface WordData {
    word: string
    type: number
}

function splitAndAppend(separator: string, str: string): string[] {
    if (!str.includes(separator)) {
        return [str]
    }

    let result = str.split(separator)
    for (let i = 0; i < result.length - 1; i++) {
        result[i] = result[i] + separator
    }

    const last = result.pop()!
    if (last !== '') {
        result.push(last)
    }

    return result
}

function splitAndAppendForArray(separator: string, arrayOfStrings: string[]): string[] {
    return arrayOfStrings.flatMap((str) => splitAndAppend(separator, str))
}

function splitSentence(separators: string[], str: string): string[] {
    if (separators.length === 0) {
        return [str]
    }

    const firstSeparator = separators.shift()!
    let result = splitAndAppend(firstSeparator, str)

    result = separators.reduce(
        (acc, separator) => splitAndAppendForArray(separator, acc),
        result,
    );

    return result
}

function spanColor(number: number): string {
    if (number === 1) {
        return '$1<span style="color: green;">$2</span>$3'
    }

    if (number === 2) {
        return '$1<span style="color: red;">$2</span>$3'
    }

    return '$1$2$3'
}

let content = document.getElementById("content")?.innerHTML || ""
let sentences: string[] = splitSentence(["</p>", ".", "?", "!"], content)
let currentSentence = 0

function processWord(json: string): void {
    const data: WordData = JSON.parse(json)
    const word = data.word

    const lastSentence = sentences.length - 1
    for (let i = currentSentence; i <= lastSentence; i++) {
        const sentence = sentences[i]
        const find = sentence.match(new RegExp(`[\\P{L}](${word})[\\P{L}]`, 'igu'))

        if (find !== null && find.length > 0) {
            currentSentence = i
            sentences[i] = sentence.replace(
                new RegExp(`([\\P{L}])(${word})([\\P{L}])`, 'igu'),
                spanColor(data.type)
            )

            break
        }
    }
}

axios
    .get<{ data: ConfigItem[] }>('/config')
    .then(function (response) {
        response.data.data.forEach((item) => {
            console.log(item)
            const esStr = 'es#' + item.id

            const url = new URL(item.sse_url);
            url.searchParams.append('topic', item.topic)
            const eventSource = new EventSource(url)

            eventSource.onopen = function () {
                console.log(esStr + ' is connected')
            };

            let start = false;
            eventSource.onmessage = function (event) {
                console.log(esStr + ' get: ', event)

                if (event.data === 'start') {
                    start = true

                    return
                }

                if (start && event.data === 'finish') {
                    console.log(esStr + ' completed')
                    const content = document.getElementById("content")?.innerHTML || ""
                    axios.post('/finish', {
                        id: item.id,
                        content: content,
                    });
                }

                if (event.data === 'finish') {
                    console.log(esStr + ' finished')
                    eventSource.close()

                    return
                }

                processWord(event.data)
                document.getElementById("content")!.innerHTML = sentences.join('')
            };

            eventSource.onerror = function (error) {
                console.error(esStr + ' failed:', error)
                eventSource.close()
            };
        });
    })
    .catch(error => {
        console.error('Failed to fetch config:', error)
    })
