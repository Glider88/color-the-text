# Color the text

Just playing with Laravel, Laravel Octane, FrankenPHP with working mode, SSE and LLM.

Main idea - colorize text like in the IDE using the color scheme, now select and color only the subjects and predicates in the text.

Works for russian text.

![](/storage/images/screenshot.png)

### Start:

Preparation:
```shell
npm install
cp .env.example .env
```

LLM:
I use **LM Studio**.
In the **Developer** tab, in **Settings** enable **Serve on Local Network** and set port to **1234**
```shell
sudo ufw allow 1234/tcp
```

Start docker:
```shell
bin/re  # first time
```
```shell
bin/up  # next times
```
Then:
```shell
bin/art key:generate
```
```shell
bin/art migrate
```
```shell
bin/art queue:work redis -v --queue=llm
```
```shell
bin/art queue:work redis -v --queue=sse
```
```shell
npm run "dev"
```

Front: http://127.0.0.1:8000/upload

