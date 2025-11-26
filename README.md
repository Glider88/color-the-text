# Color the text

Main idea - colorize text like in the IDE using the color scheme, now select and color only the subjects and predicates in the text. Works for russian text.

## Preparation:
```shell
npm install
cp .env.example .env
```

## Start docker

First time:
```shell
bin/re
```

Next times:
```shell
bin/up
```

## Then:
```shell
bin/art migrate
bin/art queue:work redis -v --queue=llm
bin/art queue:work redis -v --queue=sse
npm run "dev"
```

## LLM:
I used **LM Studio** and **google/gemma-3-12b**.
In the **Developer** tab, in **Settings** enable **Serve on Local Network** and set port to **1234**

## Front:
Go to http://127.0.0.1:8000/upload, add Title, Text and pick llm model.

