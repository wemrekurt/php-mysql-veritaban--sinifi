# php-mysql-veritaban--sinifi
PHP MySQL Veritabanı Sınıfı

Sınıfı inceleyerek diğer fonksiyonların kullanım şekillerini görebilirsiniz.
*Önemli Not:* Insert ve Update işlemlerini yapan fonksiyonlarda created_at ve updated_at değerleri ilgili tablo için otomatik oluşturulmaktadır. Bu nedenle veritabanınızı oluştururken created_at ve updated_at alanlarınızı DateTime veri türünde dahil etmeyi unutmayın.

## Örnek Sorgular

```php
$dbname = 'example';
$dbusername = 'foo';
$dbpassword = 'bar';

$db = new \Globally\Database('localhost',$dbname,$dbusername,$dbpassword);

$user = $db->selectOne('users',1,'id');
// users tablosundan id = 1 olan kaydı sorgular

$customers = $db->selectAll('customers', ['id', 'DESC']);
// customers tablosundaki tüm kayıtları sorgular

$todos = $db->selectWhere('todos',['to_date','ASC'],[['user_id',1],['state','0']]);
//user_id = 1 AND state=0 şeklinde sorgular için

$records = $db->sumwParams('records','amount',[['type',1]]);
// records tablosunda type=1 olan verilerin amount değerlerini toplayan sorgu

$blogData = ['title'=>'Başlık','content'=>'İçerik','keywords'=>'anahtar,kelime'];
$insert = $db->insert('blog',$blogData);
// insert işlemi yapan fonksiyon. Burada array key'leri veritabanındaki sütun isimleriyle eşleşmelidir

$blogData = ['id'=>1, 'title'=>'Yeni Başlık', 'content'=>'Yeni İçerik'];
$update = $db->updateWithId('blog',$blogData);
// id ile gönderilen değerleri günceller.

$remove = $db->delete('blog',1);
// blog tablosundaki id'si 1 olan kaydı siler.
```
