Kontroluje dany RSS feed a posle e-mail s novymi polozkami feedu.

## Konfigurace

V souboru config.php jsou definovany nasledujici konstanty:

FEED_URL - adresa feedu pro kontrolu
EMAIL_TO - cilova adresa na kterou se posle e-mail s novymi polozkami
EMAIL_FROM - odchozi adresa ze ktere se budou e-maily posilat
SUBJECT_PREFIX - nemenna cast predmetu e-mailu, vlozi se na jeho zacatek (uzitecne napr. pro filtrovani)
CACHE_PATH - cesta pro zapis Key/Value souboru, standardne nastavena na adresar 'cache';
             tyto soubory je vhodne zachovat, napr. pri prenosu na jiny system - pouzivaji se pro
             zachovani stavu mezi spustenim skriptu (cas posledniho spusteni a ID polozek, pro ktere uz bylo
             poslane upozorneni)

### Uloziste pro Key/Value


## Instalace

Je nutne povolit zapis do adresare cache/ a pripadne dalsiho adresare definovaneho v CACHE_PATH, napriklad:

chmod 777 cache/

Pro pouzivani staci periodicky spoustet soubor fetch.php, napriklad pres CRON.
Ukazka zaznamu v crontab pro spusteni kazdych 5 minut:

*/5 * * * * cd /cesta/k/skriptu;php fetch.php