# pukiwiki_markdown.inc.php

[PukiwikiでMarkdownを使用可能にするプラグイン](https://pukiwiki.osdn.jp/?%E8%87%AA%E4%BD%9C%E3%83%97%E3%83%A9%E3%82%B0%E3%82%A4%E3%83%B3/markdown.inc.php)は[sonotsさんが作成されたもの](http://pukiwiki.sonots.com/?Plugin%2Fmarkdown.inc.php)が有名ですが、これはPHP 8.0に対応していないためにPukiwiki 1.5.4では使用できませんでした。そこではいふんさんがこれをPHPに対応させたver1.21を作成してくれて、それがPHP 8.0で使用できるようになっています。

しかし、sonotsさんのMarkdownプラグインは本当に純粋にテキストをMarkdown parser（Michelf/markdown）に投げているので、このMarkdown記法の中ではPukiwikiプラグインは使用できません。

ところで、先日Pukiwiki 1.5.3を無理やりMarkdown対応する改造をしているうちに、Michelf版ではなくerusev版を使っていること、またこの無理やり改造のなかで #plugin という表記法ではなく !plugin という表記法に変えることでMarkdownの見出し表記とPukiwikiプラグインを共存している方法があることに気がつきました。そこで、これを組み合わせればsonotsさんのMarkdownプラグインを使いながら !plugin の表記法にすることでPukiwikiプラグインも使用できるのでは？と考えました。

## Pukiwiki式もMarkdown式もリンクが使える

さらに、Markdown parserにかける前のテキストをhtmlsc() に投げるかどうかでPukiwiki方式のリンクが使えるかMarkdown式のリンクが使えるかが切り替わることがわかったので、強引にMarkdown式のリンクをPukiwiki式のリンクに書き換える一文を挟み込むことでいずれの書式でリンクを書いても使えるようになりました。

Pukiwiki式リンク表記 &#91;&#91;&#71;&#111;&#111;&#103;&#108;&#101;&#62;&#104;&#116;&#116;&#112;&#115;&#58;&#47;&#47;&#103;&#111;&#111;&#103;&#108;&#101;&#46;&#99;&#111;&#109;&#93;&#93;
→ [[Google>https://google.com]]

Markdown式リンク表記
&#91;&#71;&#111;&#111;&#103;&#108;&#101;&#93;&#40;&#104;&#116;&#116;&#112;&#115;&#58;&#47;&#47;&#103;&#111;&#111;&#103;&#108;&#101;&#46;&#99;&#111;&#109;&#41;
→ [Google](https://google.com)

## Markdown parserはMichelfでもerusevでも使える

Markdown parserはどれをつかっても大差はないのですが、sonotsさんのオリジナルは[Michelf/markdown](https://github.com/michelf/php-markdown)のMarkdown parserを使っていて、今回は[erusev/markdown](https://github.com/erusev/parsedown)も使えるようにしています。erusev/markdownのほうが改行が反映されるオプションを設定しやすく、また（セーフモードをfalseにすれば）RAW HTMLも使いやすくなっているので、個人的にはerusev/markdownのほうが好みです。ただし、個人または限られた人物のみが使用する場合以外  はerusev/markdownを使用する場合はMarkdown parserのセーフモードをオンにするほうが良いでしょう。

## 設置方法

### Michelf版を使用するとき

- Michelf版のmarkdown.inc.phpと、"Michelf"のMarkdown parserフォルダごとを、両方ともPukiwikiのpluginフォルダに設置します。

### erusev版を使用するとき

- erusev版のmarkdown.inc.phpと、"vendor"と書かれたMarkdown parserフォルダごとを、両方ともPukiwikiのpluginフォルダに設置します。

erusev版不特定多数が使用できるような場合はセーフモードまたはHTMLエスケープモードの設定をしておく方が望ましい。markdown.inc.phpの49行目の $result = $parsedown ->setBreaksEnabled(true) ->text($body); が設定を記載する場所です。詳しくは公式サイトのtutorialをご覧ください。

- 改行が不要な場合は ->setBreaksEnabled(true) を削除します。
- ->setSafeMode(true); を追加すればセーフモード
- ->setMarkupEscaped(true); を追記すればHTMLエスケープモードで、HTMLが全てエンティティ化されて無効になります。

例

    $result = $parsedown ->setSafeMode(true) ->setBreaksEnabled(true) ->text($body);

## 書き方

基本的にsonotsさんのオリジナルと同じです。

ただし、事前にpukiwiki.ini.phpで define('PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK', 0); の設定が0（つまり複数行プラグインを使用可能）になっていることを確認します。 
