<?php


namespace AcikVeri\Importer\Interfaces;
use Closure;


interface Importer
{
    // Uzak bir adresden veriyi çekmek için kullanılır.
    public function loadFromUrl(string $url, array $headers = []);

    // Veriyi el ile yülemek için kullanılır.
    public function loadFromString(string $data);

    // Verinin kayıt edileceği tabloya ait modelin gösterilmesi için kullanılır.
    public function setModel(string $table);

    // Kayıt edileccek veriyi ve tabloda hangi sütüna kayıt edileceğini belirtmek için kullanılır.
    public function insert(string $column, string $key);

    // Yüklenen verideki istenilen veriyi almak için kullanılır.
    // Orn. ->get('a.b.c');
    public function get(string $path = null);

    // İlişkili tablolarda veri eklemek için kullanılır.
    public function relation(string $column, Closure $callback);

    // Eğer kayıtlı veri var ise kayıtlı verileri yeni veriler ile karşılaştırır.
    // Yeni veride değişklik var ise kayıtlı verileri günceller.
    public function update();

    // İçeri aktarma işlemini başlatır.
    public function import();

}
