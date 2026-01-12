<p>{{ $transaction->seller->name }} 様</p>

<p>
以下の商品が取引完了となりました。
</p>

<p>
商品名：{{ $transaction->item->name }}<br>
価格：¥{{ $transaction->item->price }}
</p>

<p>
購入者：{{ $transaction->buyer->name }}
</p>

<p>
ご利用ありがとうございます。
</p>