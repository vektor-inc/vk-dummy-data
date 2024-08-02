# これはなに
WordPressのダミーデータを大量に作ります。２個のカスタム投稿タイプと、各投稿タイプに２個のタクソノミーを追加し、ダミー記事を指定された分、大量に追加します。

# どうやってつかうの？
- pluginsディレクトリに本ファイルを追加して、プラグインを有効化して下さい。
- 管理メニューに「Dummy Data」が追加されるので、クリック。作成したい記事数とバッチサイズを入れて「ダミーデータ作成」を押して下さい。処理経過が表示され、完了するとその旨表示されます。
- バッチサイズは大きくすると処理速度が上がりますが、大きすぎるとメモリを食います。ローカル環境であれば、10000ぐらいにしても良いです。
- 指定された記事を２つのカスタム投稿タイプに入れます。実際には指定した数の倍の記事数になりますので、25万件作成したい場合は、作成する記事数に125000、バッチサイズ10000ぐらいにすると最適です。
- 削除する時は「ダミーデータ削除」を押します。途中経過はでませんので、大量に記事を入れた時は気長にお待ち下さい。
- ダミーデータ削除を実施するともとに戻しますが、データが汚れても良い開発環境でお試し下さい。

# DBへの影響
- このプラグインは２つのカスタム投稿タイプと各投稿タイプに２つのタクソノミーを追加します。この時点でDBに影響ありません。
- ダミーデータ作成時、各タクソノミーにタームを登録し、投稿するダミー記事と紐づけます。
- ダミーデータ削除時、各タクソノミーのタームを削除し、関連付けをすべて削除し、関連するダミーデータを削除します。
- ダミーデータ削除時、登録したダミーデータについてのみ削除しますので、元からあるデータに影響はありません。
  


